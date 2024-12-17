<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Form;

use Lodel\Bundle\CoreBundle\Form\Site\TeiUploadFormType;
use Lodel\DataInteroperabilityBundle\Enum\TransformationType;
use Lodel\DataInteroperabilityBundle\EventListener\TeiUploadFormListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Extends the TeiUploadFormType to add transformation-related fields.
 */
class TeiUploadFormTypeExtension extends AbstractTypeExtension
{
    private bool $isJavaInstalled;

    /**
     * Constructor with dependency injection.
     *
     * @param TeiUploadFormListener $formListener listener to handle transformation
     */
    public function __construct(private TeiUploadFormListener $formListener)
    {
        // Check if Java is installed
        exec('java --version 2>&1', $output, $result);
        $this->isJavaInstalled = 0 === $result;
    }

    /**
     * Adds transformation fields or a warning if Java is missing.
     *
     * @param FormBuilderInterface $builder Form builder instance
     * @param array<string,mixed>  $options Options passed to the form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Parent call for compatibility
        parent::buildForm($builder, $options);

        if ($this->isJavaInstalled) {
            // Add a dropdown for selecting transformation type
            $builder
                ->add('transformation', ChoiceType::class, [
                    'choices' => TransformationType::choices(), // Uses enum for options
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
     * @return iterable<string> list of form types to extend
     */
    public static function getExtendedTypes(): iterable
    {
        return [TeiUploadFormType::class];
    }
}
