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
use Symfony\Component\Process\Process;

/**
 * Service responsible for transforming XML files using XSLT stylesheets through Saxon-HE.
 */
class Transformer implements TransformerInterface
{
    /**
     * @var array<string, mixed> Handles configuration settings for stylesheets and Saxon-HE processing
     */
    private array $config;

    /**
     * Initializes the service by retrieving the 'lodel_data_interoperability' configuration
     * from the application's parameter bag.
     *
     * @param ParameterBagInterface $params The parameter bag service containing configuration parameters.
     *                                      This will be used to fetch the 'lodel_data_interoperability' configuration.
     */
    public function __construct(private ParameterBagInterface $params)
    {
        // Load and store the 'lodel_data_interoperability' configuration as an associative array.
        $this->config = (array) $this->params->get('lodel_data_interoperability');
    }

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
        if ('none' !== $transformation) {
            // Retrieve the list of XSLT stylesheets for the given transformation type.
            $xsltSheets = $this->getStylesheets($transformation);
        }

        // Initialize a DOMDocument for formatting and final validation of the XML output.
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        if (isset($xsltSheets)) {
            $actual = 1;

            // Apply each stylesheet sequentially.
            foreach ($xsltSheets as $xsltSheet) {
                // Execute the transformation using the current stylesheet.
                ${"outputFile{$actual}"} = $this->execute($inputFile, $transformation, $xsltSheet);

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
        $inputFile = preg_replace('/\.xml$/', '', $inputFile).'.xml';

        // Save the final XML file to ensure proper formatting.
        $dom->load($inputFile);
        $dom->save($inputFile);

        return $inputFile;
    }

    /**
     * Retrieves the list of XSLT stylesheets for a given transformation type.
     *
     * @param string $transformation the transformation type
     *
     * @return string[] list of XSLT stylesheet filenames
     *
     * @throws \Exception If the transformation is not configured in the settings
     */
    public function getStylesheets(string $transformation): array
    {
        // Verify if the given transformation type is defined in the configuration.
        if (!isset($this->config['transformation'][$transformation])) {
            // Throw an exception if the transformation type is not found in the configuration.
            throw new \Exception("Transformation type '{$transformation}' is not configured.");
        }

        // Check if the transformation has an associated list of files.
        if (!isset($this->config['transformation'][$transformation]['files'])) {
            throw new \Exception("Transformation '$transformation' does not have a listed set of files.");
        }

        // Get the list of stylesheets files associated with the transformation.
        $stylesheets = $this->config['transformation'][$transformation]['files'];

        // Ensure that each stylesheet file exists in the designated directory.
        foreach ($stylesheets as $stylesheet) {
            $stylesheetPath = $this->config['stylesheets_dir'].'/'.$stylesheet;
            if (!file_exists($stylesheetPath)) {
                throw new \Exception("Stylesheet file '$stylesheet' not found.");
            }
        }

        return $stylesheets;
    }

    /**
     * Executes an XSLT transformation using Saxon-HE.
     *
     * @param string $inputFile      path to the input XML file
     * @param string $transformation the transformation type (used for naming and path)
     * @param string $xsltSheet      the XSLT stylesheet to apply
     *
     * @return string path to the output XML file
     *
     * @throws \Exception if the Saxon-HE process encounters an error
     */
    protected function execute(string $inputFile, string $transformation, string $xsltSheet): string
    {
        // Create a temporary output file.
        $outputFile = tempnam(sys_get_temp_dir(), $transformation.'_');
        rename($outputFile, $outputFile .= '.xml');

        // Build the command to execute the Saxon-HE transformation.
        $process = new Process([
            'java',
            '-jar',
            $this->config['saxon_dir'],
            '-s:'.$inputFile, // Source file
            '-xsl:'.$this->config['stylesheets_dir'].'/'.$xsltSheet, // XSLT stylesheet
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
