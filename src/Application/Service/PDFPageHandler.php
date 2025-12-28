<?php

namespace Gabriellopes\Pdfmanager\Application\Service;

interface PDFPageHandler
{
    public function pagesCount(): int;
    public function usePage(int $pageNumber, FileShape $shape): void;
}
