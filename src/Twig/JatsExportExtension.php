<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension to generate export buttons for JATS.
 * This extension provides a custom Twig function to create buttons
 * for viewing and downloading JATS XML files.
 */
class JatsExportExtension extends AbstractExtension
{
    /**
     * Constructor injects URL generator and translator services.
     *
     * @param UrlGeneratorInterface $urlGenerator Service for generating URLs
     * @param TranslatorInterface   $translator   Service for handling translations
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * Registers the custom Twig function for generating export buttons.
     *
     * @return array<TwigFunction> List of available Twig functions
     */
    public function getFunctions(): array
    {
        return [
            // Declares a Twig function `jats_export_buttons` that calls `generateButtons()`
            // The `is_safe` option ensures that the returned HTML is not escaped by Twig
            new TwigFunction('jats_export_buttons', [$this, 'generateButtons'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Generates HTML buttons for viewing and downloading a JATS export.
     *
     * @param int $id The ID of the resource to export
     *
     * @return string HTML string containing two buttons
     */
    public function generateButtons(int $id): string
    {
        // Generates the URL for viewing the exported JATS XML
        $viewUrl = $this->urlGenerator->generate('export_tei_to_jats', ['id' => $id]);
        // Retrieves the translated label for the "View XML" button
        $viewLabel = $this->translator->trans('lodel.interoperability.jats.export.view');

        // Generates the URL for downloading the exported JATS XML
        $downloadUrl = $this->urlGenerator->generate('export_tei_to_jats', ['id' => $id, 'download' => true]);
        // Retrieves the translated label for the "Download XML" button
        $downloadLabel = $this->translator->trans('lodel.interoperability.jats.export.download');

        // Returns the HTML string with translated button labels
        return '
            <ul class="links">
                <li class="links__item">
                    <a href='.$viewUrl.'>'.$viewLabel.'</a>
                </li>
                <li class="links__item">
                    <a href='.$downloadUrl.'>'.$downloadLabel.'</a>
                </li>
            </ul>
        ';
    }
}
