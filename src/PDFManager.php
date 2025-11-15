<?php

namespace Gabriellopes\Pdfmanager;

use setasign\Fpdi\Fpdi;

class PDFManager
{
    private Fpdi $pdf;

    public function __construct(private FileProvider $fileProvider)
    {
        $this->pdf = new Fpdi();
    }

    public function merge(MergePDFRequest $request): void
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

class MergePDFRequest
{
    private array $toMerge;
    private string $outFilename;

    public function __construct(string $outFilename, MergePDFFile ...$toMerge)
    {
        $this->toMerge = $toMerge;
        $this->outFilename = $outFilename;
    }

    /**
     * Return the output file name
     *
     * @return string
     */
    public function outFilename()
    {
        return $this->outFilename;
    }

    /**
     * Return the files to merge
     *
     * @return MergePDFFile[]
     */
    public function toMerge()
    {
        return $this->toMerge;
    }
}

class MergePDFFile
{
    private string $filename;
    private array $ignorePages;

    public function __construct(string $filename, array $ignorePages)
    {
        $this->filename = $filename;
        $this->ignorePages = $ignorePages;
    }

    /**
     * Return the file name
     *
     * @return string
     */
    public function filename()
    {
        return $this->filename;
    }

    /**
     * Return the pages to be ignored.
     *
     * @return int[]
     */
    public function ignorePages()
    {
        return $this->ignorePages;
    }
}
