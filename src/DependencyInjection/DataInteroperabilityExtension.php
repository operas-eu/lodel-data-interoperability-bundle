<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * DataInteroperabilityExtension.
 *
 * This class is responsible for loading and managing the configuration for the Lodel Data Interoperability bundle.
 * It is automatically invoked when the bundle is registered within the Symfony application.
 */
final class DataInteroperabilityExtension extends Extension implements PrependExtensionInterface
{
    /**
     * The alias used as the root key in configuration files.
     * This is also the name used to reference parameters and services related to this bundle.
     */
    private const ALIAS = 'lodel_data_interoperability';

    /**
     * Loads a specific configuration into the service container.
     *
     * This method is called to load the bundle's configuration from YAML files into the Symfony container.
     * It processes the given configuration values and registers them with the container.
     *
     * @param array            $configs   Configuration values from the application
     * @param ContainerBuilder $container The Dependency Injection container where services and parameters are registered
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Create a loader instance to load the services.yaml file into the container
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        // Load the service definitions from the services.yaml file
        $loader->load('services.yaml');

        /** @var Configuration $configuration */
        // Instantiate the configuration class to process the configuration values
        $configuration = new Configuration();
        // Process the configuration values and merge them
        $config = $this->processConfiguration($configuration, $configs);
        // Store the processed configuration values as parameters in the container
        $container->setParameter(self::ALIAS, $config);
    }

    /**
     * Prepares additional configuration before the container is compiled.
     *
     * This method allows for prepending additional configuration to be merged with the application configuration.
     * It is called during the compilation of the container, just before the actual configuration is loaded.
     *
     * @param ContainerBuilder $container The Dependency Injection container
     */
    public function prepend(ContainerBuilder $container): void
    {
        // Define the list of configuration packages to prepend
        $packages = [
            self::ALIAS, // The configuration package for this bundle
        ];

        // Loop through each package and prepend its configuration
        foreach ($packages as $package) {
            // Load the configuration file for the package (in YAML format)
            $config = Yaml::Parse((string) file_get_contents(__DIR__.'/../Resources/config/packages/'.$package.'.yaml'));

            // Prepend each configuration to the corresponding extension
            foreach ($config as $name => $conf) {
                // Add the configuration to the container, ensuring it's merged with any existing configurations
                $container->prependExtensionConfig($name, $conf);
            }
        }
    }

    /**
     * Returns the alias of this extension.
     *
     * The alias is used to refer to the extension in configuration files (e.g., services.yaml).
     *
     * @return string The alias of the extension
     */
    public function getAlias(): string
    {
        // Return the alias to be used in configuration files
        return self::ALIAS;
    }
}
