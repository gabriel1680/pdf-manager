<?php

use Gabriellopes\Pdfmanager\Application\Service\FileProvider;
use Gabriellopes\Pdfmanager\Application\UseCase\MergePDF;
use Gabriellopes\Pdfmanager\MergePDFFile;
use Gabriellopes\Pdfmanager\MergePDFRequest;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class MergePDFTest extends TestCase
{
    private LegacyMockInterface&MockInterface&FileProvider $provider;
    private MergePDF $sut;

    protected function setUp(): void
    {
        $this->provider = Mockery::mock(FileProvider::class);
        $this->sut = new MergePDF($this->provider);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testShouldThrowWhenRequestDoesNotHaveFiles(): void
    {
        $input = new MergePDFRequest("some-file.pdf");
        $this->expectException(RuntimeException::class);
        $this->sut->execute($input);
    }

    public function testShouldThrowWhenFileDoesNotExists(): void
    {
        $this->provider
            ->shouldReceive('getFileWith')
            ->andThrow(new RuntimeException());
        $input = new MergePDFRequest("invalid resource name", new MergePDFFile("a-file.pdf", []));
        $this->expectException(RuntimeException::class);
        $this->sut->execute($input);
    }
}
