<?php

namespace Gabriellopes\Pdfmanager\In\Cli\Command\Merge;

use Closure;
use Gabriellopes\Pdfmanager\In\Cli\Command\CLICommand;

class MergePDFCommand implements CLICommand
{
    private MergeSpecValidator $validator;
    private MergeSpecToRequestMapper $mapper;

    public function __construct(private Closure $mergePDF)
    {
        $this->validator = new MergeSpecValidator();
        $this->mapper = new MergeSpecToRequestMapper();
    }

    public function getOptions(): array
    {
        return ['spec' => 'Path to JSON merge spec file'];
    }

    public function run(array $options): void
    {
        if (!isset($options['spec'])) {
            throw new \RuntimeException("Missing required --spec option");
        }
        $specData = $this->validator->validate($options['spec']);
        $request = $this->mapper->map($specData);
        ($this->mergePDF)($request);
    }
}
