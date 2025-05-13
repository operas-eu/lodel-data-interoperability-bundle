<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Form;

use Lodel\Bundle\CoreBundle\Form\Site\TeiUploadFormType;
use Lodel\DataInteroperabilityBundle\EventListener\TeiUploadFormListener;
use Lodel\DataInteroperabilityBundle\Service\TransformationProvider;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extends the TeiUploadFormType to add transformation-related fields.
 *
 * This class adds functionality to the TeiUploadFormType by injecting transformation options
 * into the form, using the TransformationProvider service to fetch available transformations.
 * It also includes a Java installation check and displays a warning if Java is not installed.
 */
class TeiUploadFormTypeExtension extends AbstractTypeExtension
{
    /** @var bool Indicates whether Java is installed on the system. */
    private bool $isJavaInstalled;

    /**
     * Constructor with dependency injection.
     *
     * @param TeiUploadFormListener $formListener listener to handle transformation
     */
    public function __construct(
        private TeiUploadFormListener $formListener,
        private TransformationProvider $transformationProvider,
    ) {
        // Check if Java is installed on the system
        exec('java --version 2>&1', $output, $result);
        $this->isJavaInstalled = 0 === $result;
    }

    /**
     * Adds transformation fields or a warning if Java is missing.
     *
     * This method builds the form with a dropdown for selecting the transformation type
     * if Java is installed. If Java is not installed, a warning message is shown.
     *
     * @param FormBuilderInterface $builder The form builder instance
     * @param array                $options Options passed to the form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Parent call for compatibility
        parent::buildForm($builder, $options);

        if ($this->isJavaInstalled) {
            // Add a dropdown for selecting transformation type using the TransformationProvider
            $builder
                ->add('transformation', ChoiceType::class, [
                    'choices' => $this->transformationProvider->getTransformations(), // Uses TransformationProvider to get choices
                    'label' => 'Transformation', // Label displayed on the form
                    'mapped' => false, // Field is not mapped to the entity
                ]);

            // Attach the listener to handle transformations and validations
            $builder->addEventSubscriber($this->formListener);
        } else {
            // Display a warning message if Java is not installed
            $builder
                ->add('java_warning', TextType::class, [
                    'data' => 'Java is required for transformation functionality but is not installed on this system.', // Warning message
                    'label' => 'Transformation', // Label for clarity
                    'mapped' => false, // Not mapped to the entity
                    'attr' => [
                        'class' => 'text-danger', // Style the warning as an error
                    ],
                ]);
        }
    }

    /**
     * Returns the class that this extension applies to.
     *
     * This method returns the form type that is being extended.
     *
     * @return iterable<string> list of form types to extend
     */
    public static function getExtendedTypes(): iterable
    {
        return [TeiUploadFormType::class];
    }
}
