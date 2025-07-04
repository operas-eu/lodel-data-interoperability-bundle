<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\Service;

use Lodel\Bundle\CoreBundle\DataProvider\ContentDataProviderInterface;
use Lodel\Bundle\CoreBundle\DataProvider\DataProvider;
use Lodel\Bundle\CoreBundle\DTO\Site\Content;
use Lodel\DataInteroperabilityBundle\Service\JatsExport;
use Lodel\ServiceIOBundle\TEI\TEIExporterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unit tests for the JatsExport service.
 *
 * These tests verify the behavior of the JatsExport service,
 * including content retrieval, TEI XML generation, and JATS XML transformation.
 *
 * @group lodel
 */
final class JatsExportTest extends KernelTestCase
{
    private const TEI_ORIGINAL = __DIR__.'/../fixtures/teiToJats/tei-commons_original.xml';
    private const JATS_RESULT = __DIR__.'/../fixtures/teiToJats/jats-publishing_result.xml';

    private DataProvider|MockObject $dataProvider;
    private MockObject $content;
    private MockObject $contentDataProvider;
    private MockObject $teiExporter;
    private ParameterBag $params;
    private JatsExport $jatsExport;

    /**
     * Sets up the test environment by initializing mock dependencies
     * and creating an instance of JatsExport.
     *
     * This method is run before each test case.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->dataProvider = $this->createMock(DataProvider::class);
        $this->content = $this->createMock(Content::class);
        $this->contentDataProvider = $this->createMock(ContentDataProviderInterface::class);
        $this->teiExporter = $this->createMock(TEIExporterInterface::class);

        $config = [
            'lodel_data_interoperability' => [
                'saxon_dir' => __DIR__.'/../../src/Resources/scripts/saxon-he-10.6.jar',
                'stylesheets_dir' => __DIR__.'/../fixtures/stylesheets',
                'transformation' => [
                    'teiToJats' => [
                        'label' => 'TEI to JATS',
                        'operation' => 'export',
                        'files' => [
                            'tei_to_jats.xsl',
                        ],
                    ],
                ],
            ],
        ];
        $this->params = new ParameterBag($config);

        // Configure the data provider mock to return the content data provider
        $this->dataProvider
            ->expects($this->once())
            ->method('get')
            ->with(Content::class)
            ->willReturn($this->contentDataProvider)
        ;

        $this->jatsExport = new JatsExport(
            $this->dataProvider,
            $this->teiExporter,
            $this->params,
        );
    }

    /**
     * Tests the export method for successful content transformation.
     *
     * Verifies that the JATS XML response matches the expected result.
     * Ensures the proper generation of the JATS XML from the TEI XML.
     */
    public function testExport(): void
    {
        $id = 1;

        // Mock the content retrieval to return a mock content object
        $this->contentDataProvider
            ->expects($this->once())
            ->method('findWithValues')
            ->with($id)
            ->willReturn($this->content)
        ;

        // Load the expected TEI XML for comparison
        $teiXml = new \DOMDocument();
        $teiXml->loadXML((string) file_get_contents(self::TEI_ORIGINAL));

        // Mock the TEI export process
        $this->teiExporter
            ->expects($this->once())
            ->method('export')
            ->with($this->content)
            ->willReturn($teiXml)
        ;

        // Call the export method and verify the response
        $response = $this->jatsExport->export($id, true);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('text/xml', $response->headers->get('Content-Type'));
        $this->assertSame('attachment; filename="'.$id.'-jats.xml"', $response->headers->get('Content-Disposition'));
        $this->assertXmlStringEqualsXmlFile(self::JATS_RESULT, (string) $response->getContent());
    }

    /**
     * Tests the export method when the content is not found.
     *
     * Verifies that an exception is thrown with the appropriate error message
     * when no content is found for the given ID.
     */
    public function testExportThrowsExceptionWhenContentNotFound(): void
    {
        $id = 0;

        // Mock the content retrieval to throw an exception when content is not found
        $this->contentDataProvider
            ->expects($this->once())
            ->method('findWithValues')
            ->with($id)
            ->willThrowException(new \Exception(sprintf('Content "%d" not found.', $id)))
        ;

        // Expect an exception to be thrown
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Content with ID '.$id.' not found.');

        // Call the export method, which should throw the exception
        $this->jatsExport->export($id);
    }

    /**
     * Tests the export method when TEI XML generation fails.
     *
     * Verifies that an exception is thrown with the appropriate error message
     * if the TEI XML generation fails.
     */
    public function testExportThrowsExceptionWhenTEIXMLGenerationFails(): void
    {
        $id = 1;

        // Mock the content retrieval to return a mock content object
        $this->contentDataProvider
            ->expects($this->once())
            ->method('findWithValues')
            ->with($id)
            ->willReturn($this->content)
        ;

        // Mock the TEI export process to return a DOMDocument that fails to generate XML
        $domDocument = $this->createMock(\DOMDocument::class);
        $domDocument
            ->expects($this->once())
            ->method('saveXML')
            ->willReturn(false)
        ;

        $this->teiExporter
            ->expects($this->once())
            ->method('export')
            ->with($this->content)
            ->willReturn($domDocument)
        ;

        // Expect an exception to be thrown with a specific error message
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to generate TEI XML.');

        // Call the export method, which should throw the exception
        $this->jatsExport->export($id);
    }
}

namespace Lodel\ServiceIOBundle\TEI;

use Lodel\Bundle\CoreBundle\DTO\Site\Content;

if (!interface_exists(TEIExporterInterface::class)) {
    interface TEIExporterInterface
    {
        public function export(Content $content): \DOMDocument;
    }
}
