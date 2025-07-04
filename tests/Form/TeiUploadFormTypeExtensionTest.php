<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\Form;

use Lodel\Bundle\CoreBundle\Form\Site\TeiUploadFormType;
use Lodel\DataInteroperabilityBundle\EventListener\TeiUploadFormListener;
use Lodel\DataInteroperabilityBundle\Form\TeiUploadFormTypeExtension;
use Lodel\DataInteroperabilityBundle\Service\TransformationProvider;
use Lodel\DataInteroperabilityBundle\Service\Transformer;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Unit tests for the TeiUploadFormTypeExtension class.
 *
 * This test suite validates the behavior of the form extension that adds
 * transformation and validation fields to the TeiUploadFormType, depending
 * on the environment and configuration.
 *
 * @group lodel
 */
final class TeiUploadFormTypeExtensionTest extends TypeTestCase
{
    private Transformer|MockObject $transformer;
    private TeiUploadFormListener $formListener;
    private TransformationProvider $transformationProvider;
    private TranslatorInterface|MockObject $translator;
    private TeiUploadFormTypeExtension $formTypeExtension;

    /**
     * Prepares the testing environment.
     *
     * - Mocks the Transformer and initializes the TeiUploadFormTypeExtension
     *   with the TeiUploadFormListener.
     * - Sets up the Symfony form testing environment.
     */
    protected function setUp(): void
    {
        $this->transformer = $this->createMock(Transformer::class);
        $this->formListener = new TeiUploadFormListener($this->transformer);

        $params = new ParameterBag([
            'lodel_data_interoperability' => [
                'transformation' => [
                    'fooToBar' => [
                        'label' => 'Foo to Bar',
                        'operation' => 'import',
                    ],
                ],
            ],
        ]);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->transformationProvider = new TransformationProvider($params, $this->translator);
        $this->formTypeExtension = new TeiUploadFormTypeExtension($this->formListener, $this->transformationProvider, $this->translator);

        parent::setUp();
    }

    /**
     * Tests the buildForm method when Java is installed.
     *
     * - Verifies that the "transformation" field is added to the form.
     * - Ensures the correct configuration of this field, including field type and options.
     * - Asserts the absence of the "java_warning" field when Java is available.
     */
    public function testBuildForm(): void
    {
        $formTypeExtension = new \ReflectionClass($this->formTypeExtension);
        $isJavaInstalled = $formTypeExtension->getProperty('isJavaInstalled');
        $isJavaInstalled->setAccessible(true);
        $isJavaInstalled->setValue($this->formTypeExtension, true);

        $formBuilder = $this->factory->createBuilder(TeiUploadFormType::class);
        $this->assertFalse($formBuilder->has('transformation'));

        $this->formTypeExtension->buildForm($formBuilder, []);
        $form = $formBuilder->getForm();
        $this->assertTrue($form->has('transformation'));
        $this->assertFalse($form->has('java_warning'));

        $transformationField = $form->get('transformation');
        $this->assertInstanceOf(ChoiceType::class, $transformationField->getConfig()->getType()->getInnerType());

        $expectedChoices = $this->transformationProvider->getTransformationsByOperation('import');
        $this->assertEquals($expectedChoices, $transformationField->getConfig()->getOption('choices'));
    }

    /**
     * Tests the buildForm method when Java is not installed.
     *
     * - Ensures that the "transformation" field is not added to the form.
     * - Verifies the presence of the "java_warning" field when Java is not available.
     */
    public function testBuildFormWithoutJavaInstalled(): void
    {
        $formTypeExtension = new \ReflectionClass($this->formTypeExtension);
        $isJavaInstalled = $formTypeExtension->getProperty('isJavaInstalled');
        $isJavaInstalled->setAccessible(true);
        $isJavaInstalled->setValue($this->formTypeExtension, false);

        $formBuilder = $this->factory->createBuilder(TeiUploadFormType::class);
        $this->assertFalse($formBuilder->has('transformation'));

        $this->formTypeExtension->buildForm($formBuilder, []);
        $form = $formBuilder->getForm();
        $this->assertFalse($form->has('transformation'));
        $this->assertTrue($form->has('java_warning'));
    }

    /**
     * Tests the getExtendedTypes method.
     *
     * - Ensures that the form extension applies to the TeiUploadFormType class.
     * - Verifies that the extension will extend only the TeiUploadFormType class.
     */
    public function testGetExtendedTypes(): void
    {
        $this->assertEquals([TeiUploadFormType::class], TeiUploadFormTypeExtension::getExtendedTypes());
    }
}
