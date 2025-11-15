<?php

namespace Gabriellopes\Pdfmanager;

interface FileProvider
{
    /**
     * Provides a file function
     *
     * @param string $filename
     * @return resource
     */
    public function getFileWith(string $filename);
}
