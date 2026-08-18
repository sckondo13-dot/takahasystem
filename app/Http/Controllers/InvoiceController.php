<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\DailyReport;
use App\Models\Company;
use App\Services\Pdf\InvoicePdfService;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::with([
            'client',
            'site',
        ]);

        /*
        |--------------------------
        | 月検索
        |--------------------------
        */

        if ($request->filled('month')) {

            $month = Carbon::parse($request->month);

            $query->whereYear(
                'invoice_date',
                $month->year
            )->whereMonth(
                'invoice_date',
                $month->month
            );
        }

        /*
        |--------------------------
        | 元請検索
        |--------------------------
        */

        if ($request->filled('client_id')) {

            $query->where(
                'client_id',
                $request->client_id
            );
        }

        /*
        |--------------------------
        | 状態
        |--------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------
        | 請求番号
        |--------------------------
        */

        if ($request->filled('keyword')) {

            $query->where(
                'invoice_no',
                'like',
                "%{$request->keyword}%"
            );
        }

        $invoices = $query
            ->latest('invoice_date')
            ->paginate(20);

        $clients = Client::orderBy('name')->get();

        return view(
            'invoices.index',
            compact(
                'invoices',
                'clients'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        $prefix = 'INV-' . now()->format('Ym');

        $last = Invoice::where(
            'invoice_no',
            'like',
            $prefix . '%'
        )->latest()->first();

        $number = $last
            ? intval(substr($last->invoice_no, -3)) + 1
            : 1;

        $invoiceNo = sprintf(
            '%s-%03d',
            $prefix,
            $number
        );

        return view(
            'invoices.create',
            compact(
                'clients',
                'invoiceNo'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'site_id' => 'nullable|exists:sites,id',
            'month' => 'required|date_format:Y-m',
            'title' => 'nullable|string|max:255',
            'invoice_date' => 'required|date',
            'payment_due' => 'required|date',
            'remarks' => 'nullable|string',
        ]);
        /*
    |--------------------------------------------------------------------------
    | 対象月
    |--------------------------------------------------------------------------
    */

        $month = Carbon::createFromFormat(
            'Y-m',
            $request->month
        )->startOfMonth();

        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        /*
    |--------------------------------------------------------------------------
    | 請求元会社
    |--------------------------------------------------------------------------
    */

        $company = Company::firstOrFail();

        /*
    |--------------------------------------------------------------------------
    | 請求書番号
    |--------------------------------------------------------------------------
    */

        $prefix = 'INV-' . now()->format('Ym');

        $last = Invoice::where(
            'invoice_no',
            'like',
            $prefix . '%'
        )
            ->latest('id')
            ->first();

        $number = $last
            ? intval(substr($last->invoice_no, -3)) + 1
            : 1;

        $invoiceNo = sprintf(
            '%s-%03d',
            $prefix,
            $number
        );

        /*
    |--------------------------------------------------------------------------
    | 現場の売上・交通費を取得
    |--------------------------------------------------------------------------
    */

        $sales = 0;
        $transportation = 0;

        if ($request->site_id) {

            $reports = DailyReport::with('details')
                ->where('site_id', $request->site_id)
                ->whereBetween('work_date', [
                    $start,
                    $end
                ])
                ->get();

            foreach ($reports as $report) {

                $sales += $report->details->sum('sales');

                $transportation +=
                    $report->details->sum('transportation_cost');
            }
        }

        /*
    |--------------------------------------------------------------------------
    | 請求金額
    |--------------------------------------------------------------------------
    */

        $subtotal = $sales + $transportation;

        $tax = floor($subtotal * 0.10);

        $total = $subtotal + $tax;

        /*
    |--------------------------------------------------------------------------
    | 請求書作成
    |--------------------------------------------------------------------------
    */

        $invoice = Invoice::create([

            'company_id' => $company->id,

            'client_id' => $request->client_id,

            'site_id' => $request->site_id,

            'invoice_no' => $invoiceNo,

            'invoice_type' => 'normal',

            'title' => $request->title,

            'invoice_date' => $request->invoice_date,

            'payment_due' => $request->payment_due,

            'subtotal' => $subtotal,

            'tax' => $tax,

            'total' => $total,

            'remarks' => $request->remarks,

        ]);

        /*
    |--------------------------------------------------------------------------
    | 日報との紐付け
    |--------------------------------------------------------------------------
    */

        if ($request->site_id) {

            $reportIds = DailyReport::where(
                'site_id',
                $request->site_id
            )
                ->whereBetween('work_date', [
                    $start,
                    $end
                ])
                ->pluck('id');

            $invoice
                ->dailyReports()
                ->sync($reportIds);
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with(
                'success',
                '請求書を作成しました'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'company',
            'client',
            'site',
            'details',
            'dailyReports',
        ]);

        return view('invoices.show', compact(
            'invoice'
        ));
    }

    public function pdf(
        Invoice $invoice,
        InvoicePdfService $pdf
    ) {
        return $pdf->preview($invoice);
    }

    public function downloadPdf(
        Invoice $invoice,
        InvoicePdfService $pdf
    ) {
        return $pdf->downloadPdf($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
