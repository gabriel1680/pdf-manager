<?php

namespace Gabriellopes\Pdfmanager\In\CLI;

use Gabriellopes\Pdfmanager\In\Cli\Command\CLICommand;
use Gabriellopes\Pdfmanager\In\Cli\Command\Merge\MergePDFCommand;
use Gabriellopes\Pdfmanager\PDFManager;
use RuntimeException;

class PDFManagerCli
{
    /** @var CLICommand[] */
    private array $commands;

    public function __construct(private PDFManager $pdfManager)
    {
        $this->commands = [];
        $this->bindCommands();
    }

    private function bindCommands()
    {
        $this->register("merge", new MergePDFCommand($this->pdfManager->merge(...)));
    }

    private function register(string $name, CLICommand $command): void
    {
        $this->commands[$name] = $command;
    }

    public function run(array $args): void
    {
        try {
            $commandName = $args[1] ?? null;
            $command = $this->commands[$commandName];
            if (!$commandName || !isset($command)) {
                throw new RuntimeException("Unknown command: $commandName\n");
            }
            $options = $this->parseOptionsFrom($args, $command->getOptions());
            $command->run($options);
        } catch (\Throwable $e) {
            fwrite(STDERR, "Error: {$e->getMessage()}\n");
            exit(1);
        }
    }

    private function parseOptionsFrom(array $args, array $commandOptions): array
    {
        $rawArgs = array_slice($args, 2);
        $options = [];
        foreach ($commandOptions as $flag => $_) {
            foreach ($rawArgs as $arg) {
                if (str_starts_with($arg, "--{$flag}=")) {
                    $options[$flag] = substr($arg, strlen("--{$flag}="));
                }
            }
        }
        return $options;
    }
}
