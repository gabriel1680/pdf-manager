<?php

namespace Gabriellopes\Pdfmanager\In;

use Gabriellopes\Pdfmanager\MergePDFFile;
use Gabriellopes\Pdfmanager\MergePDFRequest;
use Gabriellopes\Pdfmanager\PDFManager;

class PDFManagerCli
{
    private array $handlers;

    public function __construct(private PDFManager $pdfManager)
    {
        $this->handlers = [];
        $this->bindCommands();
    }

    public function run(array $args): void
    {
        $handler = $this->handlers[$args[1]];
        if (!isset($handler)) {
            echo "Invalid command";
            exit(1);
        }
        $handler($args);
    }

    private function bindCommands(): void
    {
        $this->handlers["merge"] = function (array $args) {
            $outFilename = $args[2];
            $request = new MergePDFRequest($outFilename);
            $filename = $args[3];
            $pages = [];
            for ($i = 1; $i < count($args); $i++) {
                array_push($pages, $args[$i]);
            }
            $file = new MergePDFFile($filename, $pages);
            $request->addFile($file);
            $this->pdfManager->merge($request);
        };
    }
}

interface CLIHandler
{
    public function on(string $command, callable $callback);
}
