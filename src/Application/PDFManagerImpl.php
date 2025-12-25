<?php

namespace Gabriellopes\Pdfmanager\Application;

use Gabriellopes\Pdfmanager\Application\UseCase\MergePDF;
use Gabriellopes\Pdfmanager\PDFManager;
use Gabriellopes\Pdfmanager\MergePDFRequest;

class PDFManagerImpl implements PDFManager
{
    public function __construct(private MergePDF $mergePDF) {}

    public function merge(MergePDFRequest $request): void
    {
        $this->mergePDF->execute($request);
    }
}
