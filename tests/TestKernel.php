<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests;

use Lodel\DataInteroperabilityBundle\DataInteroperabilityBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * TestKernel class extends Symfony's Kernel to set up a minimal test environment.
 * It registers the necessary bundles and configurations for testing.
 */
final class TestKernel extends Kernel
{
    /**
     * Registers the bundles required for the test environment.
     *
     * This method adds the Symfony FrameworkBundle (which provides basic Symfony services)
     * and the DataInteroperabilityBundle (which is the specific bundle being tested).
     *
     * @return iterable<int, BundleInterface> a list of bundles to be registered
     */
    public function registerBundles(): iterable
    {
        // Register the Symfony FrameworkBundle to include basic Symfony services
        yield new FrameworkBundle();

        // Register the custom DataInteroperabilityBundle for testing
        yield new DataInteroperabilityBundle();
    }

    /**
     * Registers the container configuration.
     *
     * This method allows you to add any minimal configuration needed for the test.
     * You can load custom configuration files or services for the test environment.
     *
     * @param LoaderInterface $loader the loader to use for configuration files
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        // Add minimal configuration if necessary
    }
}
