<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\Twig;

use Lodel\DataInteroperabilityBundle\Twig\JatsExportExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

/**
 * Test case for the JatsExportExtension class.
 * This test ensures that the Twig extension correctly generates export buttons
 * and registers the expected Twig functions.
 */
final class JatsExportExtensionTest extends TestCase
{
    /** @var UrlGeneratorInterface&MockObject Mocked URL generator service */
    private UrlGeneratorInterface&MockObject $urlGenerator;

    /** @var Translator Translator service for handling translations */
    private Translator $translator;

    /** @var JatsExportExtension The Twig extension instance under test */
    private JatsExportExtension $extension;

    /**
     * Sets up the test environment before each test case.
     * Initializes mocks and dependencies required for testing.
     */
    protected function setUp(): void
    {
        // Mock the URL generator service
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        // Create a real Translator instance and add in-memory translations
        $this->translator = new Translator('en');
        $this->translator->addLoader('array', new ArrayLoader());
        $this->translator->addResource('array', [
            'lodel.interoperability.jats.export.view' => 'View XML',
            'lodel.interoperability.jats.export.download' => 'Download XML',
        ], 'en');

        // Define how the mocked URL generator should respond to specific calls
        $this->urlGenerator
            ->method('generate')
            ->willReturnCallback(fn ($name, $parameters) => match ([$name, $parameters]) {
                // Mocked URL for viewing the JATS export
                ['export_tei_to_jats', ['id' => 123]] => '/view-url',
                // Mocked URL for downloading the JATS export
                ['export_tei_to_jats', ['id' => 123, 'download' => true]] => '/download-url',
                default => throw new \InvalidArgumentException('Unexpected arguments: '.json_encode([$name, $parameters])),
            }
            );

        // Instantiate the extension with the mocked services
        $this->extension = new JatsExportExtension($this->urlGenerator, $this->translator);
    }

    /**
     * Tests that the `generateButtons` method produces the expected HTML output.
     */
    public function testGenerateButtons(): void
    {
        // Expect the generate method to be called exactly twice (once for each button)
        $this->urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->willReturnOnConsecutiveCalls('/view-url', '/download-url')
        ;

        // Call the method being tested
        $result = $this->extension->generateButtons(123);

        // Assert that key parts of the HTML output are present
        $this->assertStringContainsString('<a href=/view-url>View XML</a>', $result);
        $this->assertStringContainsString('<a href=/download-url>Download XML</a>', $result);
    }

    /**
     * Tests the `getFunctions` method to verify that the Twig function is properly registered.
     */
    public function testGetFunctions(): void
    {
        // Retrieve the registered Twig functions
        $functions = $this->extension->getFunctions();

        // Assert that only one function is registered
        $this->assertCount(1, $functions);

        // Assert that the function has the expected name
        $this->assertSame('jats_export_buttons', $functions[0]->getName());
    }
}
