<?php

namespace Gabriellopes\Pdfmanager\In;

use Gabriellopes\Pdfmanager\MergePDFFile;
use Gabriellopes\Pdfmanager\MergePDFRequest;
use RuntimeException;

class MergeSpecToRequestMapper
{
    /**
     * @throws RuntimeException
     */
    public function map(array $spec): MergePDFRequest
    {
        if (!isset($spec['output']['file'], $spec['inputs'])) {
            throw new RuntimeException('Invalid spec structure provided to translator');
        }
        $files = [];
        foreach ($spec['inputs'] as $input) {
            $files[] = new MergePDFFile($input['file'], $input['exclude']);
        }
        return new MergePDFRequest($spec['output']['file'], ...$files);
    }
}
