<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\Controller;

use Lodel\DataInteroperabilityBundle\Controller\JatsExportController;
use Lodel\DataInteroperabilityBundle\Service\JatsExport;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unit test for the JatsExportController.
 * These tests ensure that the controller correctly handles the JATS export process
 * and responds appropriately in success and failure scenarios.
 */
final class JatsExportControllerTest extends TestCase
{
    // Path to the expected JATS result file for comparison
    private const JATS_RESULT = __DIR__.'/../fixtures/teiToJats/jats-publishing_result.xml';

    /**
     * Test the controller's __invoke method, which triggers the export process.
     * This simulates a request and verifies the response.
     */
    public function testInvoke(): void
    {
        // Create a mock of the JatsExport service
        /** @var JatsExport|MockObject $jatsExport */
        $jatsExport = $this->createMock(JatsExport::class);

        // Instantiate the controller with the mocked JatsExport service
        /** @var JatsExportController $jatsExportController */
        $jatsExportController = new JatsExportController($jatsExport);

        // Create a mock Request object
        $request = new Request();

        // Randomize the 'download' parameter between true and false
        $download = (bool) rand(0, 1);  // Randomly set download to either true or false
        $request->query->set('download', $download);  // Add random 'download' parameter to the request

        // Set up the mock to return a predefined JATS XML response when the 'export' method is called
        $jatsExport
            ->expects($this->once()) // Ensure 'export' is called exactly once
            ->method('export')
            ->with(123, $download) // Test for content with ID 123 and download = false
            ->willReturn(
                new Response(
                    (string) file_get_contents(self::JATS_RESULT), // Return the content from the predefined JATS XML file
                    Response::HTTP_OK, // HTTP status code 200 (OK)
                    [
                        'Content-Type' => 'text/xml', // Set the response content type to XML
                    ],
                )
            );

        // Call the controller's __invoke method, simulating the request
        $response = $jatsExportController->__invoke(123, $request);

        // Assert that the response has the expected status code
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        // Assert that the response's Content-Type header is set to 'text/xml'
        $this->assertSame('text/xml', $response->headers->get('Content-Type'));

        // Assert that the response content matches the expected JATS XML file
        $this->assertXmlStringEqualsXmlFile(self::JATS_RESULT, (string) $response->getContent());
    }

    /**
     * Tests that the controller returns a 500 response when an exception is thrown during export.
     */
    public function testInvokeThrowsException(): void
    {
        // Create controller without the JatsExport service
        $jatsExportController = new JatsExportController();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The JatsExport service is required but not available. Please ensure the dependent bundle Service IO providing this service is installed and enabled.');

        $request = new Request();

        // Call __invoke() which should throw the exception
        $jatsExportController->__invoke(123, $request);
    }
}
