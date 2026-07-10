<?php

namespace App\Services\Pdf;

class SiteMonthlyPdfService extends BasePdfService
{
    /**
     * ブラウザ表示
     */
    public function preview(array $data)
    {
        return $this->stream(
            'pdf.site-monthly',
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
        $site = $data['site'];
        $month = $data['month'];

        $filename = sprintf(
            '現場別月報_%s_%s.pdf',
            $site->name,
            $month->format('Y年m月')
        );

        return $this->download(
            'pdf.site-monthly',
            $data,
            $filename
        );
    }
}
