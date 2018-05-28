<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Subscribers;

use Doctrine\ORM\Events;
use EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException;
use EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityStub;
use Tests\EoneoPay\Externals\ORM\Stubs\EntityWithRulesStub;
use Tests\EoneoPay\Externals\SubscribersTestCase;

class ValidateEventSubscriberTest extends SubscribersTestCase
{
    /**
     * Subscriber should return prePersist and preUpdate events.
     *
     * @return void
     */
    public function testGetSubscribedEvents(): void
    {
        /** @var \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface $validator */
        $validator = $this->mockValidator();
        /** @var \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface $translator */
        $translator = $this->mockTranslator();

        self::assertEquals(
            [Events::prePersist, Events::preUpdate],
            (new ValidateEventSubscriber($validator, $translator))->getSubscribedEvents()
        );
    }

    /**
     * Subscriber should not call validate if event object getRules does not return an array.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldNotValidateIfGetRulesNotArray(): void
    {
        $this->processNotValidateTest(new EntityWithRulesStub());
    }

    /**
     * Subscriber should not call validate if event object does not have getRules method.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldNotValidateIfNoGetRulesMethod(): void
    {
        $this->processNotValidateTest(new EntityStub());
    }

    /**
     * Subscriber should not call validate if event object is not EntityInterface.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldNotValidateIfNotEntityInterface(): void
    {
        $this->processNotValidateTest(new \stdClass());
    }

    /**
     * Subscriber should throw EntityValidationException if validation fails.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldThrowEntityValidationExceptionIfValidationFails(): void
    {
        $this->expectException(EntityValidationFailedException::class);

        $object = (new EntityWithRulesStub())->setRules();

        $validator = $this->mockValidator();
        $validator
            ->shouldReceive('validate')
            ->once()
            ->with(['entityId' => null], $object->getRules())
            ->andReturn(false);
        $validator
            ->shouldReceive('getFailures')
            ->once()
            ->withNoArgs()
            ->andReturn([]);

        $translator = $this->mockTranslator();
        $translator
            ->shouldReceive('trans')
            ->once()
            ->with('exceptions.validation.failed')
            ->andReturn('exceptions.validation.failed');

        /** @var \Doctrine\ORM\Event\LifecycleEventArgs $lifeCycleEvent */
        $lifeCycleEvent = $this->mockLifeCycleEvent($object);

        /** @var \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface $validator */
        /** @var \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface $translator */
        (new ValidateEventSubscriber($validator, $translator))->preUpdate($lifeCycleEvent);
    }

    /**
     * Subscriber should validate if event object is EntityInterface and getRules returns an array.
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldValidateIfInterfaceAndGetRulesIsArray(): void
    {
        $object = (new EntityWithRulesStub())->setRules();

        $validator = $this->mockValidator();
        $validator
            ->shouldReceive('validate')
            ->once()
            ->with(['entityId' => null], $object->getRules())
            ->andReturn(true);

        $translator = $this->mockTranslator();
        $translator->shouldNotReceive('trans');

        /** @var \Doctrine\ORM\Event\LifecycleEventArgs $lifeCycleEvent */
        $lifeCycleEvent = $this->mockLifeCycleEvent($object);

        /** @var \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface $validator */
        /** @var \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface $translator */
        (new ValidateEventSubscriber($validator, $translator))->preUpdate($lifeCycleEvent);

        // This will only run if validation passes as an exception will be thrown
        self::assertTrue(true);
    }
}
