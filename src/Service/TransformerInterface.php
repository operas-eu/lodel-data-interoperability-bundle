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

/**
 * Interface for defining a transformer service that handles XML transformations.
 */
interface TransformerInterface
{
    /**
     * Transforms an input XML file based on a specified action.
     *
     * @param string $inputFile path to the input XML file
     * @param string $action    The transformation action to be applied.
     *                          (e.g., specific transformation type or operation)
     *
     * @return string path to the resulting transformed XML file
     *
     * @throws \Exception if the transformation fails
     */
    public function transform(string $inputFile, string $action): string;
}
