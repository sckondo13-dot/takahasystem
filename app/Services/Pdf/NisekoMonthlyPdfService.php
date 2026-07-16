<?php

namespace App\Services\Pdf;

class NisekoMonthlyPdfService extends BasePdfService
{
    /**
     * ブラウザ表示
     */
    public function preview(array $data)
    {
        return $this->stream(
            'pdf.niseko-monthly',
            $data,
            'a4',
            'portrait'
        );
    }

    /**
     * ダウンロード
     */
    public function downloadPdf(array $data)
    {
        $month = $data['month'];

        $filename = sprintf(
            'ニセコ木造解体月報_%s.pdf',
            $month->format('Y年m月')
        );

        return $this->download(
            'pdf.niseko-monthly',
            $data,
            $filename,
            'a4',
            'portrait'
        );
    }
}
