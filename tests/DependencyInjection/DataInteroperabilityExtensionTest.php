<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\DependencyInjection;

use Lodel\DataInteroperabilityBundle\DependencyInjection\DataInteroperabilityExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Unit tests for the DataInteroperabilityExtension class.
 *
 * This test class ensures that the extension properly loads the configuration, processes it,
 * and sets the parameters in the container.
 */
final class DataInteroperabilityExtensionTest extends TestCase
{
    /** @var DataInteroperabilityExtension the DataInteroperabilityExtension instance to be tested */
    private DataInteroperabilityExtension $extension;

    /** @var ContainerBuilder the container builder used for the tests */
    private ContainerBuilder $container;

    /** The alias used to reference parameters and services related to this bundle */
    private const ALIAS = 'lodel_data_interoperability';

    /**
     * Set up the container and the extension before each test.
     */
    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new DataInteroperabilityExtension();
    }

    /**
     * Test that the extension correctly loads and processes the configuration.
     *
     * This test ensures that the configuration values are processed and set in the container
     * when the extension is loaded.
     */
    public function testLoad(): void
    {
        // Define a sample configuration (you can load it from a file or mock it)
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

        // Load the extension with the configuration
        $this->extension->load([$config], $this->container);

        // Assert that the configuration parameters are correctly set in the container
        $this->assertTrue($this->container->hasParameter(self::ALIAS));
        $params = (array) $this->container->getParameter(self::ALIAS);

        // Assert specific values from the configuration
        $this->assertArrayHasKey('saxon_dir', $params);
        $this->assertEquals('/path/to/saxon.jar', $params['saxon_dir']);
        $this->assertArrayHasKey('stylesheets_dir', $params);
        $this->assertEquals('/path/to/stylesheets', $params['stylesheets_dir']);
        $this->assertArrayHasKey('transformation', $params);
        $this->assertArrayHasKey('fooToBar', $params['transformation']);
        $this->assertArrayHasKey('label', $params['transformation']['fooToBar']);
        $this->assertEquals('Foo to Bar', $params['transformation']['fooToBar']['label']);
        $this->assertArrayHasKey('operation', $params['transformation']['fooToBar']);
        $this->assertEquals('import', $params['transformation']['fooToBar']['operation']);
        $this->assertArrayHasKey('files', $params['transformation']['fooToBar']);
        $this->assertCount(2, $params['transformation']['fooToBar']['files']);
        $this->assertEquals('file1.xsl', $params['transformation']['fooToBar']['files'][0]);
        $this->assertEquals('file2.xsl', $params['transformation']['fooToBar']['files'][1]);
    }

    /**
     * Test the prepend method to ensure it correctly loads configuration into the container.
     *
     * This test checks that before calling the prepend method, there are no configurations
     * loaded for the 'lodel_data_interoperability' and 'framework.translation' extensions.
     * After calling prepend, it ensures that the configurations for both extensions are properly added to the container.
     */
    public function testPrepend(): void
    {
        // Assert that no configuration is loaded for 'lodel_data_interoperability' before calling prepend
        $this->assertEmpty($this->container->getExtensionConfig('lodel_data_interoperability'));

        // Assert that no 'translator' configuration exists within 'framework' before calling prepend
        $frameworkConfig = $this->container->getExtensionConfig('framework');
        $translatorConfig = array_filter($frameworkConfig, fn ($config) => isset($config['translator']));
        $this->assertEmpty($translatorConfig);

        // Call the prepend method, which should add or merge the configuration for the extensions
        $this->extension->prepend($this->container);

        // Assert that configuration for 'lodel_data_interoperability' has been added after calling prepend
        $this->assertNotEmpty($this->container->getExtensionConfig('lodel_data_interoperability'));

        // Assert that 'translator' configuration exists and is not empty within 'framework' after calling prepend
        $frameworkConfig = $this->container->getExtensionConfig('framework');
        $translatorConfig = array_filter($frameworkConfig, fn ($config) => isset($config['translator']));
        $this->assertNotEmpty($translatorConfig);
    }

    /**
     * Test the getAlias method of the extension.
     *
     * This test ensures that the correct alias is returned for the extension.
     */
    public function testGetAlias(): void
    {
        // Assert that the alias returned by the getAlias() method is 'lodel_data_interoperability'
        $this->assertEquals(self::ALIAS, $this->extension->getAlias());
    }
}
