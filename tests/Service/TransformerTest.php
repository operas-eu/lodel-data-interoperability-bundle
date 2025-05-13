<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\Service;

use Lodel\DataInteroperabilityBundle\Service\Transformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Unit tests for the Transformer service.
 *
 * These tests validate the functionality of the Transformer service,
 * including the transformation of XML files between different schemas
 * and error handling for unsupported transformation types.
 *
 * @group lodel
 */
final class TransformerTest extends KernelTestCase
{
    /** @var string Path to the directory containing test XML fixtures. */
    private string $fixturesDir;

    /** @var array Configuration parameters required by the transformer service. */
    private array $params;

    /** @var Transformer The Transformer service under test. */
    private Transformer $transformer;

    /**
     * Sets up the test environment.
     *
     * - Boots the Symfony kernel to access the container.
     * - Initializes the path to test fixtures.
     * - Defines the base configuration for transformations.
     */
    protected function setUp(): void
    {
        // Boots the Symfony kernel to access the service container.
        self::bootKernel();

        // Initializes the fixtures directory path.
        $this->fixturesDir = __DIR__.'/../fixtures/';

        // Retrieves the ParameterBag to configure the necessary parameters for transformations.
        $this->params = [
            'lodel_data_interoperability' => [
                'saxon_dir' => __DIR__.'/../../src/Resources/scripts/saxon-he-10.6.jar',
                'stylesheets_dir' => $this->fixturesDir.'stylesheets',
                'transformation' => [],
            ],
        ];
    }

    /**
     * Provides test data for various transformations.
     *
     * Each entry includes:
     * - Transformation type
     * - Input XML filename
     * - Expected output XML filename.
     *
     * @return array list of transformations and corresponding files
     */
    protected function fixturesFiles(): array
    {
        return [
            'None' => [
                'none',
                'tei-commons_original.xml',
                'tei-commons_original.xml',
            ],
            'JATS to TEI' => [
                'jatsToTei',
                'jats-publishing_original.xml',
                'tei-commons_result.xml',
            ],
        ];
    }

    /**
     * Tests the `transform` method for various transformation scenarios.
     *
     * Verifies that:
     * - The transformed output matches the expected XML content.
     * - Temporary files are properly cleaned up when transformations are applied.
     *
     * @dataProvider fixturesFiles
     *
     * @param string $transformationType the type of transformation to perform
     * @param string $original           the original XML file name
     * @param string $result             the expected result XML file name
     */
    public function testTransform(string $transformationType, string $original, string $result): void
    {
        $this->params['lodel_data_interoperability']['transformation']['jatsToTei'] = [
            'label' => 'Jats to TEI',
            'files' => [
                'jats_to_tei-1.xsl',
                'jats_to_tei-2.xsl',
            ],
        ];

        $this->transformer = new Transformer(new ParameterBag($this->params));

        // Perform the transformation and get the resulting file path
        $transformed = $this->transformer->transform($this->fixturesDir.$transformationType.'/'.$original, $transformationType);

        // Load both the actual and expected XML content
        $actual = simplexml_load_string((string) file_get_contents($transformed));
        $expected = simplexml_load_string((string) file_get_contents($this->fixturesDir.$transformationType.'/'.$result));

        // Assert that the transformed output matches the expected result
        $this->assertEquals($expected, $actual);

        // Clean up the temporary file if it's not a 'none' transformation type
        if ('none' !== $transformationType) {
            unlink($transformed);
        }
    }

    /**
     * Tests that an exception is thrown when an unknown transformation type is provided.
     *
     * @throws \Exception
     */
    public function testTransformWithUnknownTypeThrowsException(): void
    {
        $transformation = 'fooToBar';

        $this->transformer = new Transformer(new ParameterBag($this->params));

        // Expect an exception with a specific message
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Transformation type '{$transformation}' is not configured.");

        // Attempt to transform with an unsupported type
        $this->transformer->transform('test.xml', $transformation);
    }

    /**
     * Tests that an exception is thrown if no XSL files are defined.
     *
     * @throws \Exception
     */
    public function testTransformWithoutSetOfFilesThrowsException(): void
    {
        $transformation = 'jatsToTei';

        $this->params['lodel_data_interoperability']['transformation'][$transformation] = [
            'label' => 'JATS to TEI',
        ];

        $this->transformer = new Transformer(new ParameterBag($this->params));

        // Expect an exception with a specific message
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Transformation '{$transformation}' does not have a listed set of files.");

        // Attempt to transform with an unsupported type
        $this->transformer->transform(__DIR__.'/../fixtures/jatsToTei/jats-publishing_original.xml', $transformation);
    }

    /**
     * Tests that an exception is thrown if a stylesheet file is missing.
     *
     * @throws \Exception
     */
    public function testTransformWithoutFileNotFoundThrowsException(): void
    {
        $transformation = 'jatsToTei';
        $fileNotFound = 'jats-to-tei.xsl';

        $this->params['lodel_data_interoperability']['transformation'][$transformation] = [
            'label' => 'Foo to Bar',
            'files' => [
                $fileNotFound,
            ],
        ];

        $this->transformer = new Transformer(new ParameterBag($this->params));

        // Expect an exception with a specific message
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Stylesheet file '{$fileNotFound}' not found.");

        // Attempt to transform with an unsupported type
        $this->transformer->transform(__DIR__.'/../fixtures/jatsToTei/jats-publishing_original.xml', $transformation);
    }

    /**
     * Tests that an exception is thrown if the input file is either non-existent or invalid.
     *
     * @throws \Exception
     */
    public function testTransformWithInvalidInputThrowsException(): void
    {
        $this->params['lodel_data_interoperability']['transformation']['jatsToTei'] = [
            'label' => 'Jats to TEI',
            'files' => [
                'jats_to_tei-1.xsl',
                'jats_to_tei-2.xsl',
            ],
        ];

        $this->transformer = new Transformer(new ParameterBag($this->params));

        $inputFile = 'invalid.xml';

        // Expect an exception when invalid input is provided
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Source file $inputFile does not exist");

        // Assume 'invalid.xml' is either non-existent or invalid
        $this->transformer->transform($inputFile, 'jatsToTei');
    }

    /**
     * Tests that the `getStylesheets` method returns sorted stylesheets for a given transformation type.
     *
     * Verifies that:
     * - The method returns an array.
     * - The array is not empty.
     * - Each item in the array ends with a '.xsl' extension.
     */
    public function testGetStylesheetsReturnsSortedStylesheets(): void
    {
        $this->params['lodel_data_interoperability']['transformation']['jatsToTei'] = [
            'label' => 'Jats to TEI',
            'files' => [
                'jats_to_tei-1.xsl',
                'jats_to_tei-2.xsl',
            ],
        ];

        $this->transformer = new Transformer(new ParameterBag($this->params));

        // Get the stylesheets for a specific transformation type
        $stylesheets = $this->transformer->getStylesheets('jatsToTei');

        // Assert that the returned value is an array and not empty
        $this->assertIsArray($stylesheets);
        $this->assertNotEmpty($stylesheets);

        // Check that each stylesheet ends with the '.xsl' extension
        foreach ($stylesheets as $stylesheet) {
            $this->assertStringEndsWith('.xsl', $stylesheet);
        }
    }

    /**
     * Clean up the testing environment after each test.
     *
     * - Defines the directory where temporary files are stored.
     * - Searches for and deletes any temporary files starting with "jatsToTei_".
     * - Ensures proper cleanup by calling the parent tearDown method.
     */
    protected function tearDown(): void
    {
        // Define the directory where temporary files are created.
        $tempDir = sys_get_temp_dir();

        // Search for files that start with "jatsToTei_".
        $tempFiles = glob($tempDir.'/jatsToTei_*');
        if ($tempFiles) {
            foreach ($tempFiles as $tempFile) {
                // Check if the current item is a file before attempting deletion.
                if (is_file($tempFile)) {
                    unlink($tempFile); // Delete the file.
                }
            }
        }

        // Ensure proper cleanup by calling the parent tearDown method.
        parent::tearDown();
    }
}
