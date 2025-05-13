<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\DependencyInjection;

use Lodel\DataInteroperabilityBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Unit tests for the Configuration class in the DataInteroperabilityBundle.
 *
 * This test class ensures that the configuration structure is correctly built,
 * processed, and defaults are applied when necessary.
 */
final class ConfigurationTest extends TestCase
{
    /** @var Configuration the Configuration instance to be tested */
    private Configuration $configuration;

    /**
     * Set up the Configuration instance before each test.
     */
    protected function setUp(): void
    {
        $this->configuration = new Configuration();
    }

    /**
     * Test that the configuration tree builder correctly validates the expected structure.
     *
     * This test creates a sample configuration array, processes it using Symfony's
     * Processor, and asserts that the configuration is correctly transformed and validated.
     */
    public function testConfigTreeBuilder(): void
    {
        // Define a sample configuration with parameters for the configuration file
        $config = [
            'saxon_dir' => '/path/to/saxon.jar',
            'stylesheets_dir' => '/path/to/stylesheets',
            'transformation' => [
                'fooToBar' => [
                    'label' => 'Foo to Bar',
                    'operation' => 'import',
                    'files' => [
                        'file1.xsl',
                        'file2.xsl',
                    ],
                ],
            ],
        ];

        // Create the Processor to process the configuration
        $processor = new Processor();
        $processedConfig = $processor->processConfiguration($this->configuration, [$config]);

        // Verify that the configuration is correctly transformed and validated
        $this->assertArrayHasKey('saxon_dir', $processedConfig);
        $this->assertEquals('/path/to/saxon.jar', $processedConfig['saxon_dir']);
        $this->assertArrayHasKey('stylesheets_dir', $processedConfig);
        $this->assertEquals('/path/to/stylesheets', $processedConfig['stylesheets_dir']);
        $this->assertArrayHasKey('transformation', $processedConfig);
        $this->assertArrayHasKey('fooToBar', $processedConfig['transformation']);
        $this->assertArrayHasKey('label', $processedConfig['transformation']['fooToBar']);
        $this->assertEquals('Foo to Bar', $processedConfig['transformation']['fooToBar']['label']);
        $this->assertArrayHasKey('operation', $processedConfig['transformation']['fooToBar']);
        $this->assertEquals('import', $processedConfig['transformation']['fooToBar']['operation']);
        $this->assertArrayHasKey('files', $processedConfig['transformation']['fooToBar']);
        $this->assertCount(2, $processedConfig['transformation']['fooToBar']['files']);
        $this->assertEquals('file1.xsl', $processedConfig['transformation']['fooToBar']['files'][0]);
        $this->assertEquals('file2.xsl', $processedConfig['transformation']['fooToBar']['files'][1]);
    }
}
