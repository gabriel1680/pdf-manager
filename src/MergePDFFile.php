<?php

namespace Gabriellopes\Pdfmanager;

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
