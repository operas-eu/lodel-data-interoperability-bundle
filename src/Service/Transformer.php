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

use Symfony\Component\Process\Process;

/**
 * Handles XML file transformation using XSLT stylesheets via Saxon-HE.
 */
class Transformer implements TransformerInterface
{
    // Path to the Saxon-HE JAR file for XSLT processing.
    private const SAXON_DIR = __DIR__.'/../Resources/scripts/saxon-he-10.6.jar';

    // Directory containing XSLT stylesheets organized by transformation type.
    private const STYLESHEET_DIR = __DIR__.'/../Resources/stylesheets';

    /**
     * Transforms an XML file into another XML format using the specified transformation type.
     *
     * @param string $inputFile      path to the input XML file
     * @param string $transformation the transformation type (corresponding to a stylesheet directory)
     *
     * @return string path to the transformed output XML file
     *
     * @throws \Exception if the transformation fails
     */
    public function transform(string $inputFile, string $transformation): string
    {
        // Retrieve the list of XSLT stylesheets for the given transformation type.
        $xsltSheets = self::getStylesheets($transformation);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        if ($xsltSheets) {
            $actual = 1;

            // Process each stylesheet in sequence.
            foreach ($xsltSheets as $xsltSheet) {
                // Execute the transformation using the current stylesheet.
                ${"outputFile{$actual}"} = static::execute($inputFile, $transformation, $xsltSheet);

                // Clean up the previous output file if this is not the first iteration.
                if ($actual > 1) {
                    $previous = $actual - 1;
                    unlink(${"outputFile{$previous}"});
                }

                // On the final transformation, save and return the output file.
                if ($actual === count($xsltSheets)) {
                    $outputFile = ${"outputFile{$actual}"};

                    $dom->load($outputFile);
                    $dom->save($outputFile);

                    return $outputFile;
                }

                // Use the output of the current transformation as input for the next.
                $inputFile = ${"outputFile{$actual}"};
                ++$actual;
            }
        }

        // Ensure the file has an XML extension.
        if ('.xml' !== substr($inputFile, -4)) {
            rename($inputFile, $inputFile .= '.xml');
        }

        // Save the final XML file to ensure proper formatting.
        $dom->load($inputFile);
        $dom->save($inputFile);

        return $inputFile;
    }

    /**
     * Retrieves the XSLT stylesheets for the specified transformation type.
     *
     * @param string $transformation the transformation type
     *
     * @return string[]|null an array of stylesheet filenames, or null if none are found
     */
    private static function getStylesheets(string $transformation): ?array
    {
        $stylesheets = [];

        try {
            // Iterate over the stylesheet directory for the transformation type.
            $directory = new \DirectoryIterator(self::STYLESHEET_DIR.'/'.$transformation);
            foreach ($directory as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $stylesheets[] = $fileinfo->getFilename();
                }
            }

            // Sort the stylesheets alphabetically for consistent processing order.
            asort($stylesheets);

            return $stylesheets;
        } catch (\Exception $e) {
            // Return null if the directory does not exist or cannot be read.
            return null;
        }
    }

    /**
     * Executes the Saxon-HE JAR to apply the given XSLT stylesheet to the input file.
     *
     * @param string $inputFile      path to the input XML file
     * @param string $transformation the transformation type (used for paths and naming)
     * @param string $xsltSheet      filename of the XSLT stylesheet to apply
     *
     * @return string path to the transformed output XML file
     *
     * @throws \Exception if the process fails or errors occur
     */
    protected static function execute(string $inputFile, string $transformation, string $xsltSheet): string
    {
        // Create a temporary output file.
        $outputFile = tempnam(sys_get_temp_dir(), $transformation.'_');
        rename($outputFile, $outputFile .= '.xml');

        // Build the command to execute the Saxon-HE transformation.
        $process = new Process([
            'java',
            '-jar',
            self::SAXON_DIR,
            '-s:'.$inputFile, // Source file
            '-xsl:'.self::STYLESHEET_DIR.'/'.$transformation.'/'.$xsltSheet, // XSLT stylesheet
            '-o:'.$outputFile, // Output file
        ]);
        $process->setTimeout(300); // Set a timeout of 5 minutes for the process.
        $process->run();

        // Throw an exception if the process fails.
        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }

        // Return the path to the transformed output file.
        return $outputFile;
    }
}
