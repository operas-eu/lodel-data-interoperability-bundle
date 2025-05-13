<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests;

use Lodel\DataInteroperabilityBundle\DataInteroperabilityBundle;
use Lodel\DataInteroperabilityBundle\DependencyInjection\DataInteroperabilityExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Unit test class for the DataInteroperabilityBundle.
 *
 * This test class is responsible for verifying the functionality of the
 * DataInteroperabilityBundle within the Lodel application. It includes tests
 * for various methods and services provided by the bundle, ensuring the bundle
 * behaves as expected when it is loaded into the application context.
 * The tests focus on configuration loading, dependency injection, and any custom
 * logic implemented within the bundle, particularly the extension handling.
 */
final class DataInteroperabilityBundleTest extends TestCase
{
    /**
     * Tests the getContainerExtension() method of the DataInteroperabilityBundle.
     *
     * This test ensures that the method correctly returns an instance of the
     * extension responsible for handling the bundle's configuration.
     */
    public function testGetContainerExtension(): void
    {
        $bundle = new DataInteroperabilityBundle();

        // Assert that the extension is not null.
        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(ExtensionInterface::class, $extension);

        // Assert that the returned extension is an instance of DataInteroperabilityExtension.
        $this->assertInstanceOf(DataInteroperabilityExtension::class, $extension);
    }
}
