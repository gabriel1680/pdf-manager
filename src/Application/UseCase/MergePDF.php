<?php

namespace Gabriellopes\Pdfmanager\Application\UseCase;

use setasign\Fpdi\Fpdi;
use Gabriellopes\Pdfmanager\Application\Service\FileProvider;
use Gabriellopes\Pdfmanager\MergePDFRequest;

class MergePDF
{
    private Fpdi $pdf;

    public function __construct(private FileProvider $fileProvider)
    {
        $this->pdf = new Fpdi();
    }

    public function execute(MergePDFRequest $request): void
    {
        foreach ($request->toMerge() as $file) {
            $stream = $this->fileProvider->getFileWith($file->filename());
            $pageCount = $this->pdf->setSourceFile($stream);
            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                if (in_array($pageNumber, $file->ignorePages())) {
                    continue;
                }
                $templateID = $this->pdf->importPage($pageNumber);
                $this->pdf->getTemplateSize($templateID);
                $this->pdf->addPage();
                $this->pdf->useTemplate($templateID, 0, 0, 210);
            }
        }
        $this->pdf->Output("F", "tmp/{$request->outFilename()}.pdf", true);
    }
}
