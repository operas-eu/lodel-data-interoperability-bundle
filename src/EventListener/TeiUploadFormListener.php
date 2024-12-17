<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\EventListener;

use Lodel\DataInteroperabilityBundle\Service\Transformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Listener for handling file transformations in form submissions.
 *
 * This listener is triggered before form data is submitted (PRE_SUBMIT).
 * It validates and transforms the uploaded file based on the chosen transformation type.
 */
class TeiUploadFormListener implements EventSubscriberInterface
{
    /**
     * Constructor with dependency injection.
     *
     * @param Transformer $transformer service for handling file transformations
     */
    public function __construct(private Transformer $transformer)
    {
    }

    /**
     * Subscribes to the PRE_SUBMIT event for forms.
     *
     * @return array<string,string> list of events and their corresponding methods
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    /**
     * Handles the PRE_SUBMIT event to validate and transform uploaded files.
     *
     * @param FormEvent $event the form event containing the submitted data
     */
    public function onPreSubmit(FormEvent $event): void
    {
        // Get submitted form data
        $formData = $event->getData();

        // Check if required fields are set
        if (isset($formData['uploadedFile'], $formData['transformation'])) {
            $uploadedFile = $formData['uploadedFile']; // Uploaded file object
            $transformation = $formData['transformation']; // Transformation type

            // Perform the requested transformation
            $transformedFilePath = $this->transformer->transform($uploadedFile->getPathname(), $transformation);

            // Create a new UploadedFile object for the transformed file
            $transformedFile = new UploadedFile(
                $transformedFilePath,
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType(),
                null,
                true // Mark the file as "already moved"
            );

            // Replace the uploaded file with the transformed file in form data
            $formData['uploadedFile'] = $transformedFile;

            // Update the form event with modified data
            $event->setData($formData);
        }
    }
}
