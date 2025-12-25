<?php

namespace Gabriellopes\Pdfmanager;

interface PDFManager
{
    public function merge(MergePDFRequest $request): void;
}
