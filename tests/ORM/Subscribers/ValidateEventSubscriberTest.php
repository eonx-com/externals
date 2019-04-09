<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Subscribers;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\Externals\ORMTestCase;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\ValidatableStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Exceptions\EntityValidationFailedExceptionStub;
use Tests\EoneoPay\Externals\Stubs\Translator\TranslatorStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;

/**
 * @covers \EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber
 */
class ValidateEventSubscriberTest extends ORMTestCase
{
    /**
     * EventSubscriber should return expected list of events.
     *
     * @return void
     */
    public function testGetSubscribedEventsReturnsExpectedListOfEvents(): void
    {
        $expected = ['prePersist', 'preUpdate'];

        self::assertEquals($expected, $this->createInstance()->getSubscribedEvents());
    }

    /**
     * Test no exception is thrown if entity is not validatable
     *
     * @return void
     */
    public function testValidatorContinuesIfEntityNotValidatable(): void
    {
        $this->createInstance()->prePersist(new LifecycleEventArgs(
            new EntityStub(),
            new EntityManagerStub()
        ));

        // If no exception was thrown we're golden
        $this->addToAssertionCount(1);
    }

    /**
     * Test no exception is thrown if validation passes
     *
     * @return void
     */
    public function testValidatorContinuesIfValidationPasses(): void
    {
        $this->createInstance()->prePersist(new LifecycleEventArgs(
            new ValidatableStub(['integer' => 1, 'string' => 'string']),
            new EntityManagerStub()
        ));

        // If no exception was thrown we're golden
        $this->addToAssertionCount(1);
    }

    /**
     * Test validation throws exception if validation fails pre-persist
     *
     * @return void
     */
    public function testValidatorThrowsExceptionIfPrePersistValidationFails(): void
    {
        $this->expectException(EntityValidationFailedExceptionStub::class);

        $this->createInstance()->prePersist(new LifecycleEventArgs(
            new ValidatableStub(),
            new EntityManagerStub()
        ));
    }

    /**
     * Test validation throws exception if validation fails pre-update
     *
     * @return void
     */
    public function testValidatorThrowsExceptionIfPreUpdateValidationFails(): void
    {
        $this->expectException(EntityValidationFailedExceptionStub::class);

        $this->createInstance()->preUpdate(new LifecycleEventArgs(
            new ValidatableStub(),
            new EntityManagerStub()
        ));
    }

    /**
     * Create validate subscriber instance
     *
     * @return \EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber
     */
    private function createInstance(): ValidateEventSubscriber
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['required' => ':attribute is required']);
        $translator = new Translator($loader, 'en');

        return new ValidateEventSubscriber(new TranslatorStub(), new Validator(new Factory($translator)));
    }
}
