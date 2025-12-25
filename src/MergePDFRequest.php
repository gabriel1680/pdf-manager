<?php

namespace Gabriellopes\Pdfmanager;

class MergePDFRequest
{
    private array $toMerge;
    private string $outFilename;

    public function __construct(string $outFilename, MergePDFFile ...$toMerge)
    {
        $this->outFilename = $outFilename;
        $this->toMerge = count($toMerge) > 0 ? $toMerge : [];
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
        return clone $this->toMerge;
    }

    /**
     * Add a file to be merged
     *
     * @param MergePDFFile $file
     * @return void
     */
    public function addFile(MergePDFFile $file)
    {
        array_push($this->toMerge, $file);
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
