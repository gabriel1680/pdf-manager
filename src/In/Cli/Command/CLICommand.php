<?php

namespace Gabriellopes\Pdfmanager\In\Cli\Command;

interface CliCommand
{
    /**
     * Returns an array of expected CLI flags for this command.
     * Example: ['spec' => 'Path to JSON spec file']
     */
    public function getOptions(): array;

    /**
     * Execute the command with an array of parsed options.
     *
     * @param array $options Associative array of flag => value
     */
    public function run(array $options): void;
}
