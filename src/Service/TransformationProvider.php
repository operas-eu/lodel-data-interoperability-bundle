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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This service is responsible for retrieving available transformation types
 * defined in the Symfony configuration file (`lodel_data_interoperability.yaml`).
 *
 * It extracts the transformation configurations and provides them for use in forms
 * or any other components that need them.
 */
final class TransformationProvider
{
    /** @var array stores the list of transformation types available in the configuration */
    private array $transformations;

    /**
     * Constructor: Retrieves transformation types from the configuration.
     *
     * The constructor accepts the `ParameterBagInterface` service, which provides access
     * to Symfony parameters (like the configuration settings).
     *
     * @param ParameterBagInterface $params     the service allowing access to Symfony parameters
     * @param TranslatorInterface   $translator the service responsible for translation of text messages
     */
    public function __construct(
        private ParameterBagInterface $params,
        private TranslatorInterface $translator,
    ) {
        // Store the full transformation configuration directly
        $this->transformations = $this->params->get('lodel_data_interoperability')['transformation'] ?? [];
    }

    /**
     * Retrieves the list of transformation types filtered by the given operation type ('import' or 'export').
     *
     * This method returns only the transformations whose 'operation' value matches the given type.
     * It is typically used to separate transformations meant for importing data from those used to export it.
     *
     * @param string $operation The type of operation to filter by. Must be 'import' or 'export'.
     *
     * @return array an associative array where keys are human-readable labels and values are transformation identifiers
     *
     * @throws \Exception if an unsupported operation type is provided
     */
    public function getTransformationsByOperation(string $operation): array
    {
        // Validate the operation value to prevent typos or unsupported operations
        if (!in_array($operation, ['import', 'export'], true)) {
            throw new \Exception("Invalid operation '$operation'. Expected 'import' or 'export'.");
        }

        // Initialize the choices array with the default 'None' option (translated).
        $choices = [$this->translator->trans('lodel.interoperability.transformation.none') => 'none'];

        // Loop through each transformation in the configuration.
        foreach ($this->transformations as $key => $transformation) {
            // Only include transformations matching the given operation type.
            if ($operation === $transformation['operation']) {
                // Get the transformation's label
                $label = $transformation['label'];
                // Add it to the list of choices.
                $choices[$label] = $key;
            }
        }

        return $choices;
    }
}
