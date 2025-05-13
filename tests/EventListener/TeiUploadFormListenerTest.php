<?php

declare(strict_types=1);

namespace Lodel\DataInteroperabilityBundle\Tests\EventListener;

use Lodel\DataInteroperabilityBundle\EventListener\TeiUploadFormListener;
use Lodel\DataInteroperabilityBundle\Service\Transformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Unit tests for the TeiUploadFormListener class.
 *
 * This test suite ensures that the form listener behaves as expected when handling
 * the "pre-submit" event of a Symfony form, particularly for file uploads and
 * transformation processes. It mocks the dependencies to isolate the form listener's logic.
 */
final class TeiUploadFormListenerTest extends TestCase
{
    /** @var Transformer&MockObject Mocked Transformer service used for testing the transformation logic. */
    private Transformer&MockObject $transformer;

    /** @var TeiUploadFormListener Instance of the listener being tested. */
    private TeiUploadFormListener $formListener;

    /** @var FormEvent&MockObject Mocked Symfony FormEvent. */
    private FormEvent&MockObject $event;

    /** @var string Path to the temporary uploaded file used in tests. */
    private string $uploadedFilePath;

    /** @var string Path to the temporary transformed file used in tests. */
    private string $transformedFilePath;

    /**
     * Prepares the testing environment.
     *
     * - Creates temporary files for uploaded and transformed files.
     * - Mocks the Transformer and FormEvent dependencies.
     * - Instantiates the TeiUploadFormListener with the mocked Transformer.
     *
     * This setup is necessary to isolate the test environment and ensure that we
     * focus solely on the behavior of the form listener without external dependencies.
     */
    protected function setUp(): void
    {
        $this->uploadedFilePath = sys_get_temp_dir().'/uploaded_file.xml';
        file_put_contents($this->uploadedFilePath, 'original-content');

        $this->transformedFilePath = sys_get_temp_dir().'/transformed_file.xml';
        file_put_contents($this->transformedFilePath, 'transformed-content');

        $this->transformer = $this->createMock(Transformer::class);
        $this->formListener = new TeiUploadFormListener($this->transformer);
        $this->event = $this->createMock(FormEvent::class);
    }

    /**
     * Tests the normal behavior of the onPreSubmit method.
     *
     * This test ensures that the form listener:
     * - Transforms the uploaded file.
     * - Updates the form data with the transformed file's path.
     *
     * We assert that:
     * - The file is correctly transformed.
     * - The transformed file replaces the uploaded file in the form data.
     */
    public function testOnPreSubmit(): void
    {
        $uploadedFile = new UploadedFile(
            $this->uploadedFilePath,
            'uploaded_file.xml',
            'application/xml',
        );

        $formData = [
            'uploadedFile' => $uploadedFile,
            'transformation' => 'some_transformation',
        ];

        $this->event
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData)
        ;

        $this->transformer
            ->expects($this->once())
            ->method('transform')
            ->willReturn($this->transformedFilePath)
        ;

        $this->assertNotEquals($this->uploadedFilePath, $this->transformedFilePath);

        $this->event
            ->expects($this->once())
            ->method('setData')
            ->with($this->callback(function ($data) use ($uploadedFile) {
                return isset($data['uploadedFile'])
                       && $data['uploadedFile'] instanceof UploadedFile
                       && $data['uploadedFile']->getPathname() === $this->transformedFilePath
                       && $data['uploadedFile']->getClientOriginalName() === $uploadedFile->getClientOriginalName().'.'.($uploadedFile->getClientOriginalExtension() ?: 'xml')
                       && $data['uploadedFile']->getClientMimeType() === $uploadedFile->getClientMimeType();
            }));

        $this->formListener->onPreSubmit($this->event);
    }

    /**
     * Cleans up temporary files created during tests.
     *
     * This ensures no temporary files are left after the test execution.
     * It helps maintain a clean test environment and prevents unnecessary disk usage.
     */
    protected function tearDown(): void
    {
        unlink($this->uploadedFilePath);
        unlink($this->transformedFilePath);
    }
}
