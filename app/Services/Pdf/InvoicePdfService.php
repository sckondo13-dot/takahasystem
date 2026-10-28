<?php

namespace App\Services\Pdf;

use App\Models\Invoice;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

class InvoicePdfService extends BasePdfService
{
    /**
     * ブラウザ表示
     * 送付状を付ける設定の場合は、送付状＋請求書を表示
     */
    public function preview(Invoice $invoice)
    {
        $pdf = $this->makeCombinedPdf($invoice);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="invoice.pdf"'
            );
    }

    /**
     * PDFダウンロード
     */
    public function downloadPdf(Invoice $invoice)
    {
        $pdf = $this->makeCombinedPdf($invoice);

        $filename = sprintf(
            '請求書_%s.pdf',
            $invoice->invoice_no
        );

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="' . rawurlencode($filename) . '"'
            );
    }

    /**
     * 請求書PDFを生成し、必要なら送付状PDFと結合する
     */
    private function makeCombinedPdf(Invoice $invoice): string
    {
        $invoice->loadMissing([
            'company',
            'client',
            'site',
            'details.site',
            'details.workType',
            'coverLetters',
        ]);

        // 送付状なしの場合は、従来どおり請求書PDFだけを返す
        if (! $invoice->send_cover_letter) {
            return $this->makePdf(
                'pdf.invoice',
                ['invoice' => $invoice],
                'a4',
                'portrait'
            )->output();
        }

        // 送付状PDFを生成
        $coverPdf = $this->makePdf(
            'pdf.cover-letter',
            ['invoice' => $invoice],
            'a4',
            'portrait'
        )->output();

        // 請求書PDFを生成
        $invoicePdf = $this->makePdf(
            'pdf.invoice',
            ['invoice' => $invoice],
            'a4',
            'portrait'
        )->output();

        // 送付状 → 請求書の順に結合
        $mergedPdf = new Fpdi();

        foreach ([$coverPdf, $invoicePdf] as $pdfBytes) {
            $pageCount = $mergedPdf->setSourceFile(
                StreamReader::createByString($pdfBytes)
            );

            for ($page = 1; $page <= $pageCount; $page++) {
                $templateId = $mergedPdf->importPage($page);
                $size = $mergedPdf->getTemplateSize($templateId);

                $mergedPdf->AddPage(
                    $size['orientation'],
                    [$size['width'], $size['height']]
                );

                $mergedPdf->useTemplate($templateId);
            }
        }

        return $mergedPdf->Output('S');
    }
}
