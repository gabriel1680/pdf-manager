<?php

namespace Gabriellopes\Pdfmanager\In\Cli\Command\Merge;

use Gabriellopes\Pdfmanager\MergePDFFile;
use Gabriellopes\Pdfmanager\MergePDFRequest;

class MergeSpecToRequestMapper
{
    /**
     * @throws RuntimeException
     */
    public function map(array $spec): MergePDFRequest
    {
        $files = [];
        foreach ($spec['inputs'] as $input) {
            $files[] = new MergePDFFile($input['file'], $input['exclude']);
        }
        return new MergePDFRequest($spec['output']['file'], ...$files);
    }
}
