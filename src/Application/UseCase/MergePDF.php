<?php

namespace Gabriellopes\Pdfmanager\Application\UseCase;

use Gabriellopes\Pdfmanager\Application\Service\FileShape;
use Gabriellopes\Pdfmanager\Application\Service\PDFHandler;
use Gabriellopes\Pdfmanager\MergePDFRequest;
use RuntimeException;

class MergePDF
{
    public function __construct(private readonly PDFHandler $pdfHandler) {}

    public function execute(MergePDFRequest $request): void
    {
        if ($request->isEmpty()) {
            throw new RuntimeException("Empty files to merge");
        }
        foreach ($request->toMerge() as $file) {
            $pageHandler = $this->pdfHandler->loadFile($file->filename());
            for ($pageNumber = 1; $pageNumber <= $pageHandler->pagesCount(); $pageNumber++) {
                if (in_array($pageNumber, $file->ignorePages())) {
                    continue;
                }
                $pageHandler->importPage($pageNumber, FileShape::A4);
            }
        }
        $this->pdfHandler->writeTo($request->outFilename());
    }
}
