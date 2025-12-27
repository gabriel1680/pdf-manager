<?php

namespace Gabriellopes\Pdfmanager\Application\Service;

use setasign\Fpdi\PdfParser\StreamReader;

interface FileSystem
{
    public function readFrom(string $filepath): StreamReader;
    public function writeTo(string $filepath): void;
}
