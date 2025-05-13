<?php

declare(strict_types=1);

use Lodel\DataInteroperabilityBundle\Service\TransformationProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;

/**
 * Unit tests for the TransformationProvider service.
 *
 * This test class ensures that the TransformationProvider correctly retrieves transformation types
 * from the `lodel_data_interoperability` configuration and returns them in the expected format.
 */
final class TransformationProviderTest extends TestCase
{
    /** @var Translator Handles translation of transformation labels */
    private Translator $translator;

    /** @var ParameterBagInterface Stores transformation configuration */
    private ParameterBagInterface $params;

    /**
     * Sets up the test environment by initializing:
     * - A Translator instance with English as the default locale.
     * - A ParameterBagInterface with configurable transformation data.
     */
    protected function setUp(): void
    {
        // Initialize a real Translator with an in-memory translation array
        $this->translator = new Translator('en');

        // Add translations for transformation labels
        $this->translator->addLoader('array', new ArrayLoader());
        $this->translator->addResource('array', [
            'lodel.interoperability.transformation.none' => 'None',
            'lodel.interoperability.transformation.fooToBar' => 'Foo to Bar',
            'lodel.interoperability.transformation.barToFoo' => 'Bar to Foo',
        ], 'en');

        // Create a mock ParameterBag with two transformations: one "import" and one "export"
        $this->params = new ParameterBag([
            'lodel_data_interoperability' => [
                'transformation' => [
                    'fooToBar' => [
                        'label' => 'Foo to Bar',
                        'operation' => 'import',
                    ],
                    'barToFoo' => [
                        'label' => 'Bar to Foo',
                        'operation' => 'export',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Tests the getTransformations method with an empty configuration.
     *
     * Expected behavior:
     * - The method should return an array containing only the 'none' value.
     */
    public function testGetTransformationsWithEmptyConfig(): void
    {
        // Default parameter bag (empty config)
        $params = new ParameterBag(['lodel_data_interoperability' => []]);

        // Instantiate the provider
        $transformationProvider = new TransformationProvider($params, $this->translator);

        // Retrieve the transformations from the provider
        $transformations = $transformationProvider->getTransformationsByOperation('import');

        // Assert that there is exactly one entry in the transformations array
        $this->assertCount(1, $transformations);

        // Assert that the only value in the array is 'none'
        $this->assertContains('none', $transformations);
    }

    /**
     * Tests the getTransformations method with a valid configuration containing transformations.
     *
     * Expected behavior:
     * - The method should return an array with the 'none' value as default.
     * - Each transformation label should be correctly translated.
     */
    public function testGetTransformationsWithValidConfig(): void
    {
        // Instantiate the TransformationProvider with the mock config
        $transformationProvider = new TransformationProvider($this->params, $this->translator);

        // Get only the transformations marked as "import"
        $transformations = $transformationProvider->getTransformationsByOperation('import');

        // Assert that we get exactly 2 entries: one "None" + one import transformation
        $this->assertCount(2, $transformations);

        // Assert that the default "None" option is present
        $this->assertArrayHasKey('None', $transformations);
        $this->assertSame('none', $transformations['None']);

        // Assert that the import transformation is present with the correct label
        $this->assertArrayHasKey('Foo to Bar', $transformations);
        $this->assertSame('fooToBar', $transformations['Foo to Bar']);

        // Assert that the export transformation is not included in the "import" operation result
        $this->assertArrayNotHasKey('Bar to Foo', $transformations);
    }

    /**
     * Tests that getTransformationsByOperation() throws an InvalidArgumentException
     * when called with an unsupported operation value.
     */
    public function testGetTransformationsByOperationThrowsOnInvalidOperation(): void
    {
        // Instantiate the TransformationProvider with the mock config
        $transformationProvider = new TransformationProvider($this->params, $this->translator);

        // Assert: Expect an Exception with a specific message
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid operation 'convert'. Expected 'import' or 'export'.");

        // Act: Call with an unsupported operation name
        $transformationProvider->getTransformationsByOperation('convert');
    }
}
