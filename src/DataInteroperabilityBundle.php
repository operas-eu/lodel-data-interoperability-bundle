<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle;

use Lodel\DataInteroperabilityBundle\DependencyInjection\DataInteroperabilityExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Represents the InteroperabilityBundle for the Lodel application.
 *
 * This class acts as the entry point for the bundle. It extends Symfony's
 * core Bundle class and allows for custom configuration and initialization
 * of the bundle when it is registered in the application.
 */
final class DataInteroperabilityBundle extends Bundle
{
    /**
     * Returns the container extension that will handle the configuration for this bundle.
     *
     * This method ensures that the extension is only created once, on demand.
     * The extension class is responsible for loading the configuration and setting up services
     * for this bundle.
     *
     * @return ExtensionInterface|null returns the extension object if it is set, or null otherwise
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        // Check if the extension is already instantiated.
        if (null === $this->extension) {
            // If not, instantiate the DataInteroperabilityExtension class.
            $this->extension = new DataInteroperabilityExtension();
        }

        // Return the extension instance.
        return $this->extension;
    }
}
