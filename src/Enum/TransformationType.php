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

namespace Lodel\DataInteroperabilityBundle\Enum;

/**
 * Enum class representing different transformation types.
 *
 * This enum is used to represent the type of transformation between formats,
 * such as converting from JATS Publishing to TEI Commons.
 */
enum TransformationType: string
{
    // Declaring the different possible transformation types
    case NONE = 'none';
    case JATS_TO_TEI = 'jatsToTei';

    /**
     * Returns a readable label for the transformation type.
     *
     * This is used for displaying the transformation type in user interfaces.
     *
     * @return string the label for the transformation type
     */
    public function type(): string
    {
        return match ($this) {
            self::NONE => 'None', // Label for no transformation
            self::JATS_TO_TEI => 'JATS Publishing to TEI Commons', // Label for transformation from JATS to TEI
        };
    }

    /**
     * Generates an array of key-value pairs for use in forms.
     *
     * This static method is useful for generating a list of transformation types,
     * where the keys are the labels for the types and the values are the corresponding enum values.
     *
     * @return string[] Array of key-value pairs for form options (label => value)
     */
    public static function choices(): array
    {
        // Map the cases to labels and values for form selection.
        return array_combine(
            array_map(fn ($case) => $case->type(), self::cases()), // Labels as keys
            array_map(fn ($case) => $case->value, self::cases())  // Values as values
        );
    }
}
