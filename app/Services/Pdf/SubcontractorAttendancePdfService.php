<?php

namespace App\Services\Pdf;

class SubcontractorAttendancePdfService extends BasePdfService
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
            '下請け出勤簿_%s_%s.pdf',
            $data['subcontractor']->name,
            $data['month']->format('Y年m月')
        );

        return $this->download(
            'pdf.attendance',
            $data,
            $filename
        );
    }
}
