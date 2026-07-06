<?php

namespace App\Services\Pdf;

class AttendancePdfService extends BasePdfService
{
    /**
     * ブラウザ表示
     */
    public function preview(array $data)
    {
        return $this->stream(
            'pdf.attendance',
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
        $filename = sprintf(
            '個人出勤簿_%s_%s.pdf',
            $data['employee']->name,
            $data['month']->format('Y年m月')
        );

        return $this->download(
            'pdf.attendance',
            $data,
            $filename
        );
    }
}
