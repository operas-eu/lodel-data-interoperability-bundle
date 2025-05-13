<?php

declare(strict_types=1);

use Lodel\DataInteroperabilityBundle\Service\TransformationProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Unit tests for the TransformationProvider service.
 *
 * This test class ensures that the TransformationProvider correctly retrieves transformation types
 * from the `lodel_data_interoperability` configuration and returns them in the expected format.
 */
final class TransformationProviderTest extends TestCase
{
    /**
     * Tests the getTransformations method when the configuration is empty.
     *
     * Expected behavior:
     * - The method should return an array containing only the default "None" option.
     * - No additional transformation types should be present.
     */
    public function testGetTransformationsWithEmptyConfig(): void
    {
        // Create a mock configuration with an empty transformation section.
        $params = new ParameterBag(['lodel_data_interoperability' => []]);

        // Instantiate the provider with the empty configuration.
        $transformationProvider = new TransformationProvider($params);

        // Define the expected result.
        $expected = ['None' => 'none'];

        // Assert that the returned transformations match the expected default value.
        $this->assertSame($expected, $transformationProvider->getTransformations());
    }

    /**
     * Tests the getTransformations method with a valid configuration containing transformations.
     *
     * Expected behavior:
     * - The method should return an array with the "None" option as default.
     * - Each transformation label from the configuration should be correctly mapped to its key.
     */
    public function testGetTransformationsWithValidConfig(): void
    {
        // Create a mock configuration with predefined transformations.
        $params = new ParameterBag([
            'lodel_data_interoperability' => [
                'transformation' => [
                    'fooToBar' => [
                        'label' => 'Foo to Bar',
                    ],
                    'barToFoo' => [
                        'label' => 'Bar to Foo',
                    ],
                ],
            ],
        ]);

        $transformationProvider = new TransformationProvider($params);

        $expected = [
            'None' => 'none',
            'Foo to Bar' => 'fooToBar',
            'Bar to Foo' => 'barToFoo',
        ];

        $this->assertSame($expected, $transformationProvider->getTransformations());
    }
}
