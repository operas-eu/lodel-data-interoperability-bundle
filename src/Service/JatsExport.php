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

use Lodel\Bundle\CoreBundle\DataProvider\ContentDataProviderInterface;
use Lodel\Bundle\CoreBundle\DataProvider\DataProvider;
use Lodel\Bundle\CoreBundle\DTO\Site\Content;
use Lodel\ServiceIOBundle\TEI\TEIExporterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service for exporting content from TEI XML to JATS XML format.
 *
 * This service retrieves content data, converts it to TEI XML via TEIExporterInterface,
 * and then transforms it into JATS XML via the Transformer class.
 * The resulting JATS XML is returned as an HTTP response.
 */
class JatsExport extends Transformer
{
    /**
     * Constructor.
     *
     * @param DataProvider          $dataProvider         service to access data providers
     * @param TEIExporterInterface  $teiExporterInterface interface to export a Content object to TEI XML
     * @param ParameterBagInterface $params               configuration for the service (inherited from Transformer)
     */
    public function __construct(
        private DataProvider $dataProvider,
        private TEIExporterInterface $teiExporterInterface,
        /** @phpstan-ignore-next-line */
        private ParameterBagInterface $params,
    ) {
        parent::__construct($params); // Calls the constructor of Transformer.
    }

    /**
     * Exports a content item to JATS XML format.
     *
     * This method retrieves a Content object by its ID, exports it to TEI XML,
     * then transforms it to JATS XML and returns the result in an HTTP response.
     *
     * @param int $id ID of the content to export
     *
     * @return Response response containing the JATS XML
     *
     * @throws \Exception if the content is not found, if TEI XML generation fails,
     *                    or if the transformation to JATS fails
     */
    public function export(int $id, bool $download = false): Response
    {
        // Retrieves the ContentDataProvider for Content objects.
        /** @var ContentDataProviderInterface $contentDataProvider */
        $contentDataProvider = $this->dataProvider->get(Content::class);

        try {
            // Retrieves the Content object with its associated values by its ID.
            /** @var Content $content */
            $content = $contentDataProvider->findWithValues($id);
        } catch (\Exception $e) {
            throw new \Exception("Content with ID {$id} not found."); // Throws an exception if the content is not found.
        }

        // Exports the content to TEI XML.
        $teiXml = $this->teiExporterInterface->export($content)->saveXML();
        if (false === $teiXml) {
            throw new \Exception('Failed to generate TEI XML.'); // Throws an exception if TEI export fails.
        }

        // Creates a temporary file to store the TEI XML.
        $teiFile = tempnam(sys_get_temp_dir(), 'tei_');

        // Writes the TEI XML to the temporary file.
        file_put_contents($teiFile, $teiXml);

        // Transforms the TEI XML file to JATS XML using the transform method inherited from Transformer.
        $jatsFile = $this->transform($teiFile, 'teiToJats');

        // Deletes the temporary TEI file after transformation.
        unlink($teiFile);

        // Loads and formats the JATS XML.
        $jats = new \DOMDocument('1.0', 'UTF-8');
        $jats->formatOutput = true;
        $jats->preserveWhiteSpace = false;
        $jats->load($jatsFile);

        // Deletes the temporary JATS file after reading.
        unlink($jatsFile);

        // Returns the JATS XML as an HTTP response.
        $response = new Response(
            (string) $jats->saveXML(), // Converts the JATS XML to string and includes it in the response.
            Response::HTTP_OK, // HTTP 200 OK status.
            [
                'Content-Type' => 'text/xml', // Sets the content type to XML.
            ]
        );

        if ($download) {
            $response->headers->set('Content-Disposition', 'attachment; filename="'.$id.'-jats.xml"');
        }

        return $response;
    }
}
