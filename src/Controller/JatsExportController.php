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

namespace Lodel\DataInteroperabilityBundle\Controller;

use Lodel\DataInteroperabilityBundle\Service\JatsExport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for exporting TEI XML content to JATS XML format.
 *
 * This controller relies on the JatsExport service to perform the actual transformation
 * and generate the HTTP response.
 */
class JatsExportController
{
    /**
     * Constructor.
     *
     * @param JatsExport|null $jatsExport Optional service for TEI-to-JATS export.
     *                                    Defaults to null, but required for proper operation.
     */
    public function __construct(private ?JatsExport $jatsExport = null)
    {
    }

    /**
     * Handles the export request for given content ID.
     *
     * @param int     $id      identifier of the content to export
     * @param Request $request symfony HTTP Request object to retrieve query parameters
     *
     * @return Response HTTP response containing the exported JATS XML
     *
     * @throws \Exception if the JatsExport service is not available
     */
    #[Route('/{_site}/jats/{id}', name: 'export_tei_to_jats', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function __invoke(int $id, Request $request): Response
    {
        // Verify that the JatsExport service is injected and ready to use.
        if (!$this->jatsExport) {
            throw new \Exception('The JatsExport service is required but not available. Please ensure the dependent bundle Service IO providing this service is installed and enabled.');
        }

        // Retrieve the 'download' flag from query parameters, defaulting to false.
        // This flag determines if the exported content should trigger a file download.
        $download = $request->query->getBoolean('download');

        // Delegate the transformation and response creation to the JatsExport service
        return $this->jatsExport->export($id, $download);
    }
}
