<?php

namespace Gabriellopes\Pdfmanager;

use setasign\Fpdi\Fpdi;

class PDFManager
{
    private Fpdi $pdf;

    public function __construct()
    {
        $this->pdf = new Fpdi();
    }

    public function merge($files): void
    {
        foreach ($files as $file) {
            $pageCount = $this->pdf->setSourceFile(__DIR__ . "/../resources/" . $file["name"]);
            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                if (isset($file["no"]) && in_array($pageNumber, $file["no"])) {
                    continue;
                }
                $templateID = $this->pdf->importPage($pageNumber);
                $this->pdf->getTemplateSize($templateID);
                $this->pdf->addPage();
                $this->pdf->useTemplate($templateID, 0, 0, 210);
            }
        }
        $this->pdf->Output("F", "tmp/doc.pdf", true);
    }
}
