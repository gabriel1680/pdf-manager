<?php

namespace Gabriellopes\Pdfmanager\Application\Service;

interface PDFHandler
{
    public function writeTo(string $filename): void;
    public function readFrom(string $filename): PDFPageHandler;
}
