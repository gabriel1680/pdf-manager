<?php

namespace Gabriellopes\Pdfmanager\In;

use Gabriellopes\Pdfmanager\MergePDFRequest;
use Gabriellopes\Pdfmanager\PDFManager;

class PDFManagerCli
{
    private array $handlers;
    private MergeSpecValidator $validator;
    private MergeSpecToRequestMapper $mapper;

    public function __construct(private PDFManager $pdfManager)
    {
        $this->validator = new MergeSpecValidator();
        $this->mapper = new MergeSpecToRequestMapper();
        $this->handlers = [];
        $this->bindCommands();
    }

    public function run(array $args): void
    {
        try {
            $handler = $this->handlers[$args[1]];
            if (!isset($handler)) {
                echo "Invalid command";
                exit(1);
            }
            $handler($args);
        } catch (\Throwable $th) {
            fwrite(STDERR, "Error: {$th->getMessage()}\n");
            exit(1);
        }
    }

    private function bindCommands(): void
    {
        $this->handlers["merge"] = function (array $args) {
            $request = $this->getMergeRequest($args);
            $this->pdfManager->merge($request);
        };
    }

    private function getMergeRequest(array $args): MergePDFRequest
    {
        $spec = null;
        $args = array_slice($args, 2);
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--spec=')) {
                $spec = substr($arg, strlen('--spec='));
                break;
            }
        }
        if ($spec === null) {
            throw new \RuntimeException("Missing required --spec option");
        }
        $spec = $this->validator->validate($spec);
        return $this->mapper->map($spec);
    }
}

interface CLIHandler
{
    public function on(string $command, callable $callback);
}
