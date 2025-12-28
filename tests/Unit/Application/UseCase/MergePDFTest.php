<?php

use Gabriellopes\Pdfmanager\Application\Service\FileShape;
use Gabriellopes\Pdfmanager\Application\Service\PDFHandler;
use Gabriellopes\Pdfmanager\Application\Service\PDFPageHandler;
use Gabriellopes\Pdfmanager\Application\UseCase\MergePDF;
use Gabriellopes\Pdfmanager\MergePDFFile;
use Gabriellopes\Pdfmanager\MergePDFRequest;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class MergePDFTest extends TestCase
{
    private MockInterface&PDFHandler $pdfHandler;
    private MergePDF $sut;

    protected function setUp(): void
    {
        $this->pdfHandler = Mockery::mock(PDFHandler::class);
        $this->sut = new MergePDF($this->pdfHandler);
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
        $this->pdfHandler
            ->shouldReceive('readFrom')
            ->andThrow(new RuntimeException());
        $input = new MergePDFRequest("invalid resource name", new MergePDFFile("a-file.pdf", []));
        $this->expectException(RuntimeException::class);
        $this->sut->execute($input);
    }

    public function testShouldImportAllPages(): void
    {
        $pageHandler = new MockPDFPageHandler(1);
        $this->pdfHandler
            ->shouldReceive('readFrom')
            ->andReturn($pageHandler);
        $this->pdfHandler->shouldReceive('writeTo')->once();
        $toMerge = [
            new MergePDFFile("a-file.pdf", []),
        ];
        $input = new MergePDFRequest("some-file.pdf", ...$toMerge);
        $this->sut->execute($input);
        $importedPages = $pageHandler->importedPages();
        $this->assertCount(1, $importedPages);
        $importedPage = $importedPages[0];
        $this->assertEquals(1, $importedPage->pageNumber);
        $this->assertEquals(FileShape::A4, $importedPage->shape);
    }

    public function testShouldImportSomePagesAndIgnoreOthers(): void
    {
        $pageHandler = new MockPDFPageHandler(2);
        $this->pdfHandler
            ->shouldReceive('readFrom')
            ->andReturn($pageHandler);
        $this->pdfHandler->shouldReceive('writeTo')->once();
        $toMerge = [
            new MergePDFFile("a-file.pdf", [1]),
            new MergePDFFile("b-file.pdf", [2])
        ];
        $input = new MergePDFRequest("some-file.pdf", ...$toMerge);

        $this->sut->execute($input);

        $importedPages = $pageHandler->importedPages();
        $this->assertCount(2, $importedPages);

        $importedPage = $importedPages[0];
        $this->assertEquals(2, $importedPage->pageNumber);
        $this->assertEquals(FileShape::A4, $importedPage->shape);

        $importedPage = $importedPages[1];
        $this->assertEquals(1, $importedPage->pageNumber);
        $this->assertEquals(FileShape::A4, $importedPage->shape);
    }
}

final class MockPDFPageHandler implements PDFPageHandler
{
    /** @var ImportedPage[] */
    private array $importedPages;

    public function __construct(public readonly int $pagesCount)
    {
        $this->importedPages = [];
    }

    /**
     * @return ImportedPage[]
     **/
    public function importedPages()
    {
        return $this->importedPages;
    }

    public function pagesCount(): int
    {
        return $this->pagesCount;
    }

    public function usePage(int $pageNumber, FileShape $shape): void
    {
        $this->importedPages[] = new ImportedPage($pageNumber, $shape);
    }
}

final class ImportedPage
{
    public function __construct(
        public readonly int $pageNumber,
        public readonly FileShape $shape
    ) {}
}
