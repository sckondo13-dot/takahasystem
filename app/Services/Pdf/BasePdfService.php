<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\Facade\Pdf;

abstract class BasePdfService
{
    /**
     * PDF生成
     */
    protected function makePdf(
        string $view,
        array $data = [],
        string $paper = 'a4',
        string $orientation = 'portrait'
    ) {
        return Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation);
    }

    /**
     * ダウンロード
     */
    protected function download(
        string $view,
        array $data,
        string $filename,
        string $paper = 'a4',
        string $orientation = 'portrait'
    ) {
        return $this->makePdf(
            $view,
            $data,
            $paper,
            $orientation
        )->download($filename);
    }

    /**
     * ブラウザ表示
     */
    protected function stream(
        string $view,
        array $data,
        string $paper = 'a4',
        string $orientation = 'portrait'
    ) {
        return $this->makePdf(
            $view,
            $data,
            $paper,
            $orientation
        )->stream();
    }
}
