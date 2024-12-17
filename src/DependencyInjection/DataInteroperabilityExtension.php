<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * DataInteroperabilityExtension.
 *
 * This class loads and manages the bundle configuration.
 * It is automatically called when the bundle is registered.
 */
class DataInteroperabilityExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array<array<mixed>> $config    Configuration values from the application
     * @param ContainerBuilder    $container The Dependency Injection container where services and parameters are registered
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
