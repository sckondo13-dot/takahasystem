<?php

namespace App\Services\Pdf;

class TruckMonthlyPdfService extends BasePdfService
{
    /**
     * ブラウザ表示
     */
    public function preview(array $data)
    {
        return $this->stream(
            'pdf.truck-monthly',
            $data,
            'a4',
            'portrait'
        );
    }

    /**
     * PDFダウンロード
     */
    public function downloadPdf(array $data)
    {
        $month = $data['month'];

        $filename = sprintf(
            '北海道リサイクラー_トラック明細_%s.pdf',
            $month->format('Y年m月')
        );

        return $this->download(
            'pdf.truck-monthly',
            $data,
            $filename,
            'a4',
            'portrait'
        );
    }
}
