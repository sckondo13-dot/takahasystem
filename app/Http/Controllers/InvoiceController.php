<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\DailyReport;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Site;
use App\Models\WorkType;
use App\Services\Pdf\InvoicePdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * 請求書一覧
     */
    public function index(Request $request)
    {
        $query = Invoice::with([
            'client',
            'site',
            'details.site',
        ]);

        if ($request->filled('month')) {
            $query->whereMonth(
                'invoice_date',
                Carbon::parse($request->month)->month
            )->whereYear(
                'invoice_date',
                Carbon::parse($request->month)->year
            );
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $query->where(
                'invoice_no',
                'like',
                '%' . $request->keyword . '%'
            );
        }

        $invoices = $query
            ->latest('invoice_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $clients = Client::orderBy('name')->get();

        return view('invoices.index', compact(
            'invoices',
            'clients'
        ));
    }

    /**
     * 請求書作成
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        $invoiceDate = now()->toDateString();

        $paymentDue = now()
            ->addMonth()
            ->endOfMonth()
            ->toDateString();

        $invoiceMonth = now()->format('Y-m');

        return view('invoices.create', compact(
            'clients',
            'invoiceDate',
            'paymentDue',
            'invoiceMonth'
        ));
    }

    /**
     * 請求書保存
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'client_id' => [
                'required',
                'exists:clients,id',
            ],

            'month' => [
                'required',
                'date_format:Y-m',
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'payment_due' => [
                'required',
                'date',
            ],

            'title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'details' => [
                'required',
                'array',
                'min:1',
            ],

            'details.*.description' => [
                'required',
                'string',
                'max:255',
            ],

            'details.*.quantity' => [
                'required',
                'numeric',
                'min:0',
            ],

            'details.*.unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'details.*.tax_type' => [
                'required',
                Rule::in(['taxable', 'exempt']),
            ],

            'details.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'details.*.site_id' => [
                'nullable',
                'exists:sites,id',
            ],

            'details.*.work_type_id' => [
                'nullable',
                'exists:work_types,id',
            ],

            'details.*.source_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'details.*.progress_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'details.*.remaining_rate' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ]);

        $company = Company::firstOrFail();

        $invoice = DB::transaction(function () use (
            $validated,
            $company
        ) {
            /*
         * 請求番号を発行
         */
            $prefix = 'INV-' . now()->format('Ym');

            $lastInvoice = Invoice::where(
                'invoice_no',
                'like',
                $prefix . '-%'
            )
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $number = $lastInvoice
                ? ((int) substr($lastInvoice->invoice_no, -3)) + 1
                : 1;

            $invoiceNo = sprintf(
                '%s-%03d',
                $prefix,
                $number
            );

            /*
         * 明細金額と請求金額をサーバー側で計算
         */
            $taxableTotal = 0;
            $exemptTotal = 0;
            $tax = 0;

            $preparedDetails = [];

            foreach ($validated['details'] as $index => $detail) {
                $quantity = (float) $detail['quantity'];
                $unitPrice = (float) $detail['unit_price'];

                $amount = (int) round(
                    $quantity * $unitPrice
                );

                if ($detail['tax_type'] === 'taxable') {
                    $taxableTotal += $amount;
                } else {
                    $exemptTotal += $amount;
                }

                $preparedDetails[] = [
                    'site_id' => $detail['site_id'] ?? null,
                    'work_type_id' => $detail['work_type_id'] ?? null,
                    'description' => $detail['description'],
                    'quantity' => $quantity,
                    'unit' => $detail['unit'] ?? null,
                    'tax_type' => $detail['tax_type'],
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'sort_order' => $index + 1,
                    'source_type' => $detail['source_type'] ?? 'manual',
                    'progress_rate' => $detail['progress_rate'] ?? null,
                    'remaining_rate' => $detail['remaining_rate'] ?? null,
                ];
            }

            /*
         * 消費税は課税対象合計の10％を切り捨て
         */
            $tax = (int) floor($taxableTotal * 0.10);

            $total = $taxableTotal + $exemptTotal + $tax;

            /*
         * 請求書を保存
         */
            $invoice = Invoice::create([
                'company_id' => $company->id,
                'client_id' => $validated['client_id'],
                'site_id' => null,
                'invoice_no' => $invoiceNo,
                'invoice_type' => 'normal',
                'title' => $validated['title'] ?? '請求書',
                'invoice_date' => $validated['invoice_date'],
                'payment_due' => $validated['payment_due'],
                'subtotal' => $taxableTotal + $exemptTotal,
                'tax' => $tax,
                'total' => $total,
                'remarks' => $validated['remarks'] ?? null,
                'status' => 'draft',
            ]);

            /*
         * 明細を保存
         */
            foreach ($preparedDetails as $detail) {
                $invoice->details()->create($detail);
            }

            /*
         * 請負現場の残率を更新
         */
            foreach ($preparedDetails as $detail) {
                if (
                    !empty($detail['site_id']) &&
                    $detail['remaining_rate'] !== null
                ) {
                    Site::where('id', $detail['site_id'])
                        ->update([
                            'remaining_rate' => $detail['remaining_rate'],
                        ]);
                }
            }

            return $invoice;
        });

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', '請求書を作成しました');
    }

    /**
     * 請求書詳細
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'company',
            'client',
            'site',
            'details.site',
            'details.workType',
            'dailyReports',
        ]);

        return view(
            'invoices.show',
            compact('invoice')
        );
    }

    /**
     * PDFプレビュー
     */
    public function pdf(
        Invoice $invoice,
        InvoicePdfService $pdf
    ) {
        return $pdf->preview($invoice);
    }

    /**
     * PDFダウンロード
     */
    public function downloadPdf(
        Invoice $invoice,
        InvoicePdfService $pdf
    ) {
        return $pdf->downloadPdf($invoice);
    }

    /**
     * 請求月に該当する現場一覧
     */
    public function getSites(Request $request)
    {
        $request->validate([
            'client_id' => [
                'required',
                'exists:clients,id',
            ],

            'month' => [
                'required',
                'date_format:Y-m',
            ],
        ]);

        $sites = Site::query()
            ->where(
                'client_id',
                $request->client_id
            )
            ->activeAt($request->month)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'contract_type',
                'contract_amount',
                'remaining_rate',
                'contract_start',
                'contract_end',
            ]);

        return response()->json([
            'count' => $sites->count(),
            'sites' => $sites,
        ]);
    }

    /**

現場の請求用月次データ
     */
    public function getSiteDetails(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $month = Carbon::createFromFormat(
            'Y-m',
            $request->month
        )->startOfMonth();

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $site = Site::findOrFail($request->site_id);

        $reports = DailyReport::with([
            'details.workType',
            'items',
        ])
            ->where('site_id', $site->id)
            ->whereBetween('work_date', [$start, $end])
            ->orderBy('work_date')
            ->get();

        $sales = $reports
            ->flatMap->details
            ->sum('sales');

        $transportation = $reports
            ->flatMap->details
            ->sum('transportation_cost');

        $expressway = $reports
            ->flatMap->details
            ->sum('expressway_cost');

        $parking = $reports
            ->flatMap->details
            ->sum('parking_cost');

        $workTypes = $reports
            ->flatMap->details
            ->filter(function ($detail) {
                return $detail->workType;
            })
            ->groupBy('work_type_id')
            ->map(function ($details) {
                $workType = $details->first()->workType;

                return [
                    'id' => $workType->id,
                    'name' => $workType->name,
                    'sales' => $details->sum('sales'),
                    'man_hours' => $details->sum('man_hours'),
                ];
            })
            ->values();

        $items = $reports
            ->flatMap(function ($report) {
                return $report->items->map(function ($item) use ($report) {
                    return [
                        'name' => $item->name,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'date' => $report->work_date?->format('Y-m-d'),
                    ];
                });
            })
            ->values();

        return response()->json([
            'site' => [
                'id' => $site->id,
                'name' => $site->name,
                'contract_type' => $site->contract_type,
                'contract_amount' => $site->contract_amount,
                'remaining_rate' => $site->remaining_rate,
            ],
            'sales' => $sales,
            'transportation' => $transportation,
            'expressway' => $expressway,
            'parking' => $parking,
            'work_types' => $workTypes,
            'items' => $items,
        ]);
    }


    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
