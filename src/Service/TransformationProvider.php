<?php

declare(strict_types=1);

/*
 * This file is part of the CRAFT-OA Project (https://www.craft-oa.eu/)
 * funded by the European Union (HORIZON-INFRA-2022-EOSC-01 Grant Agreement: 101094397).
 *
 * Developments have been made at OpenEdition Center, a french CNRS Support and Research Unit (UAR 2504)
 * associated with Aix-Marseille University, the EHESS and Avignon University.
 *
 * Authors: João Martins, Jean-Christophe Souplet, Nicolas Vernot Cortes.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lodel\DataInteroperabilityBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * This service is responsible for retrieving available transformation types
 * defined in the Symfony configuration file (`lodel_data_interoperability.yaml`).
 *
 * It extracts the transformation configurations and provides them for use in forms
 * or any other components that need them.
 */
final class TransformationProvider
{
    /**
     * @var array<string, array{label: string}> stores the list of transformation types available in the configuration
     */
    private array $transformations;

    /**
     * Constructor: Retrieves transformation types from the configuration.
     *
     * The constructor accepts the `ParameterBagInterface` service, which provides access
     * to Symfony parameters (like the configuration settings).
     *
     * @param ParameterBagInterface $params the service allowing access to Symfony parameters
     */
    public function __construct(private ParameterBagInterface $params)
    {
        // Store the full transformation configuration directly
        $this->transformations = $this->params->get('lodel_data_interoperability')['transformation'] ?? [];
    }

    /**
     * Retrieves the list of available transformation types.
     *
     * This method processes the transformations and returns them as an array with
     * labels as keys and transformation identifiers as values.
     *
     * @return array the transformation types defined in the configuration
     */
    public function getTransformations(): array
    {
        // Initialize the choices array with the default 'None' option.
        $choices = ['None' => 'none'];

        // Loop through each transformation defined in the configuration and add it to the choices array.
        foreach ($this->transformations as $key => $transformation) {
            // Get the label of the transformation from the configuration.
            $label = $transformation['label'];
            // Add the transformation label and its key to the choices array.
            $choices[$label] = $key;
        }

        // Return the complete list of transformations, including the 'None' option.
        return $choices;
    }
}
