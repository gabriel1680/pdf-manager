<?php

namespace Tests\Unit\In\Cli\Command;

use Closure;
use Gabriellopes\Pdfmanager\In\Cli\Command\Merge\MergePDFCommand;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\DataProvider;

final class MergePDFCommandTest extends TestCase
{
    private MergePDFCommand $sut;
    private Closure $mergePDF;
    private int $calledTimes;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup("root");
        $this->calledTimes = 0;
        $this->mergePDF = function () {
            $this->calledTimes++;
        };
        $this->sut = new MergePDFCommand($this->mergePDF);
    }

    public function testInvalidOptions()
    {
        $options = [];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing required --spec option");
        $this->sut->run($options);
    }

    public function testInvalidSpecFileLocation()
    {
        $options = ['spec' => "invalid.json"];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Spec file not found: invalid.json");
        $this->sut->run($options);
    }

    public function testSpecFileWithNoPermission()
    {
        $file = vfsStream::newFile('no-permission.json', 0000)
            ->at($this->root);
        $options = ['spec' => $file->url()];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Spec file is not readable");
        $this->sut->run($options);
    }

    public static function invalidJsonProvider(): array
    {
        return [
            ["invalid.json", '{"name": "abc", ', "Invalid JSON"],
            ["invalid-format.json", '"just a string"', "Spec file must decode to an object"],
        ];
    }

    #[DataProvider('invalidJsonProvider')]
    public function testSpecFileWithInvalidJSONFormat($filename, $content, $expectedMessaage)
    {
        $file = vfsStream::newFile($filename)
            ->withContent($content)
            ->at($this->root);
        $options = ['spec' => $file->url()];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessaage);
        $this->sut->run($options);
    }

    public static function invalidSpecProvider(): array
    {
        return [
            // output
            ["invalid-output.json", '{}', "Missing output.file definition"],
            ["invalid-output-1.json", '{"output": ""}', "Missing output.file definition"],
            ["invalid-output-2.json", '{"output": {"file": []}}', "output.file must be a non-empty string"],
            ["invalid-output-3.json", '{"output": {"file": ""}}', "output.file must be a non-empty string"],
            // inputs
            ["invalid-input.json", '{"output": {"file": "some.pdf"}, "inputs": ""}', "inputs must be a non-empty array"],
            ["invalid-input-1.json", '{"output": {"file": "some.pdf"}, "inputs": []}', "At least one input PDF must be defined"],
        ];
    }

    #[DataProvider('invalidSpecProvider')]
    public function testSpecFileWithInvalidSpecFormat($filename, $content, $expectedMessaage)
    {
        $file = vfsStream::newFile($filename)
            ->withContent($content)
            ->at($this->root);
        $options = ['spec' => $file->url()];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessaage);
        $this->sut->run($options);
    }

    public static function invalidInputsProvider(): array
    {
        return [
            ["invalid-inputs.json", '{"output": {"file": "some.pdf"}, "inputs": [""]}', "Input #0 must be an object"],
            ["invalid-inputs-1.json", '{"output": {"file": "some.pdf"}, "inputs": [{  }]}', "Input #0 missing valid file"],
            ["invalid-inputs-2.json", '{"output": {"file": "some.pdf"}, "inputs": [{ "file": {} }]}', "Input #0 missing valid file"],
            ["invalid-inputs-3.json", '{"output": {"file": "some.pdf"}, "inputs": [{ "file": "" }]}', "Input #0 missing valid file"],
            ["invalid-inputs-4.json", '{"output": {"file": "some.pdf"}, "inputs": [{ "file": "abc.json", "exclude": "" }]}', "Input #0 exclude must be an array of page numbers"],
            ["invalid-inputs-5.json", '{"output": {"file": "some.pdf"}, "inputs": [{ "file": "abc.json", "exclude": ["a"] }]}', "Input #0 exclude contains invalid page number"],
            ["invalid-inputs-6.json", '{"output": {"file": "some.pdf"}, "inputs": [{ "file": "abc.json", "exclude": [-1] }]}', "Input #0 exclude contains invalid page number"],
            ["invalid-inputs-7.json", '{"output": {"file": "some.pdf"}, "inputs": [{ "file": "abc.json", "exclude": [0] }]}', "Input #0 exclude contains invalid page number"],
        ];
    }

    #[DataProvider('invalidInputsProvider')]
    public function testSpecFileWithInvalidInputs($filename, $content, $expectedMessaage)
    {
        $file = vfsStream::newFile($filename)
            ->withContent($content)
            ->at($this->root);
        $options = ['spec' => $file->url()];
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($expectedMessaage);
        $this->sut->run($options);
    }

    public function testSpecFileWithValidData()
    {
        $this->expectNotToPerformAssertions();
        $content = SpecBuilder::builder()
            ->withOuput("some.pdf")
            ->addInput("abc.json", [1])
            ->build();
        $file = vfsStream::newFile("valid-data.json")
            ->withContent(json_encode($content))
            ->at($this->root);
        $options = ['spec' => $file->url()];
        $this->sut->run($options);
    }
}

class SpecBuilder
{
    private string $output;
    private array $inputs;

    private function __construct()
    {
        $this->inputs = [];
    }

    public static function builder(): self
    {
        return new SpecBuilder();
    }

    public function withOuput(string $file): self
    {
        $this->output = $file;
        return $this;
    }

    public function withInputs(array $inputs): self
    {
        $this->inputs = $inputs;
        return $this;
    }

    public function addInput(string $filename, array $exclude): self
    {
        $this->inputs[] = ["file" => $filename, "exclude" => $exclude];
        return $this;
    }

    public function build(): array
    {
        return [
            "output" => ["file" => $this->output],
            "inputs" => $this->inputs
        ];
    }
}
