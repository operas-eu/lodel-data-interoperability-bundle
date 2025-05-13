<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Defines and validates the configuration structure for the Lodel Data Interoperability Bundle.
 *
 * This class implements the {@see ConfigurationInterface} provided by Symfony, and is responsible
 * for creating a configuration tree that specifies the structure, default values, and validation
 * rules for the bundle's configuration options.
 *
 * The configuration includes:
 * - `saxon_dir`: Path to the Saxon-HE JAR file for XSLT transformations.
 * - `stylesheets_dir`: Directory where XSLT stylesheets are stored.
 * - `transformation`: Defines available transformations, including files and labels.
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * Builds and returns the configuration tree for this bundle.
     *
     * This method defines the structure and defaults for the configuration values
     * that can be set for the bundle. It uses Symfony's ConfigurationComponent
     * to define the configuration tree, which will later be validated and merged.
     *
     * @return TreeBuilder The configuration tree builder for the bundle
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        // Initialize the TreeBuilder with the root node name 'lodel_data_interoperability'
        /** @var TreeBuilder $treeBuilder */
        $treeBuilder = new TreeBuilder('lodel_data_interoperability');

        // Access the root node of the configuration
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        // Define the children of the root node and their properties
        $rootNode
            ->children()
                ->scalarNode('saxon_dir')
                    ->defaultValue(dirname(__DIR__).'/Resources/scripts/saxon-he-10.6.jar')
                    ->info('Path to the Saxon-HE JAR file for performing XSLT transformations. This can be overridden in the configuration file.')
                ->end()
                ->scalarNode('stylesheets_dir')
                    ->defaultValue(dirname(__DIR__).'/Resources/stylesheets')
                    ->info('The directory where XSLT stylesheets will be downloaded or stored. This can be overridden in the configuration file.')
                ->end()
                ->arrayNode('transformation')
                    ->useAttributeAsKey('name') // The transformation name is used as the array key
                    ->arrayPrototype() // Each transformation is defined as an array
                        ->children()
                            // Define a 'label' for each transformation
                            ->scalarNode('label')
                                ->isRequired()
                                ->info('A human-readable label describing the transformation.')
                            ->end()
                            // Define the list of XSLT files to be applied in sequence during the transformation
                            ->arrayNode('files')
                                ->scalarPrototype() // Define the filename of each XSLT file (string)
                                    ->isRequired() // Every file name must be provided
                                    ->info('The name of the XSLT file for this transformation step. Order is important.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        // Return the configured tree builder
        return $treeBuilder;
    }
}
