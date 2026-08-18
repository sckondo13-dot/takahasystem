<?php

namespace App\Services\Pdf;

use App\Models\Invoice;

class InvoicePdfService extends BasePdfService
{
    /**
     * ブラウザ表示
     */
    public function preview(Invoice $invoice)
    {
        return $this->stream(
            'pdf.invoice',
            [
                'invoice' => $invoice,
            ],
            'a4',
            'portrait'
        );
    }

    /**
     * PDFダウンロード
     */
    public function downloadPdf(Invoice $invoice)
    {
        $filename = sprintf(
            '請求書_%s.pdf',
            $invoice->invoice_no
        );

        return $this->download(
            'pdf.invoice',
            [
                'invoice' => $invoice,
            ],
            $filename
        );
    }
}
