<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Subscribers;

use Doctrine\ORM\Events;
use EoneoPay\External\ORM\Exceptions\EntityValidationFailedException;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Tests\EoneoPay\External\ORM\Stubs\EntityStub;
use Tests\EoneoPay\External\ORM\Stubs\EntityWithRulesStub;
use Tests\EoneoPay\External\SubscribersTestCase;

class ValidateEventSubscriberTest extends SubscribersTestCase
{
    /**
     * Subscriber should return prePersist and preUpdate events.
     */
    public function testGetSubscribedEvents(): void
    {
        /** @var \Illuminate\Contracts\Validation\Factory $factory */
        $factory = $this->mockValidationFactory();

        self::assertEquals(
            [Events::prePersist, Events::preUpdate],
            (new ValidateEventSubscriber($factory))->getSubscribedEvents()
        );
    }

    /**
     * Subscriber should not call validate if event object getRules does not return an array.
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If validation fails
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
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldThrowEntityValidationExceptionIfValidationFails(): void
    {
        $this->expectException(EntityValidationFailedException::class);

        $factory = $this->mockValidationFactory();
        $validator = $this->mockValidator();

        $validator->shouldReceive('validate')->once()->withNoArgs()->andThrow($this->mockValidationException());
        $factory->shouldReceive('make')->once()->with([], [])->andReturn($validator);

        $object = (new EntityWithRulesStub())->setRules();

        /** @var \Doctrine\ORM\Event\LifecycleEventArgs $lifeCycleEvent */
        $lifeCycleEvent = $this->mockLifeCycleEvent($object);

        /** @var \Illuminate\Contracts\Validation\Factory $factory */
        (new ValidateEventSubscriber($factory))->preUpdate($lifeCycleEvent);
    }

    /**
     * Subscriber should validate if event object is EntityInterface and getRules returns an array.
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\EntityValidationFailedException If validation fails
     */
    public function testShouldValidateIfInterfaceAndGetRulesIsArray(): void
    {
        $factory = $this->mockValidationFactory();
        $validator = $this->mockValidator();

        $validator->shouldReceive('validate')->once()->withNoArgs()->andReturn(null);
        $factory->shouldReceive('make')->once()->with([], [])->andReturn($validator);

        $object = (new EntityWithRulesStub())->setRules();

        /** @var \Doctrine\ORM\Event\LifecycleEventArgs $lifeCycleEvent */
        $lifeCycleEvent = $this->mockLifeCycleEvent($object);

        /** @var \Illuminate\Contracts\Validation\Factory $factory */
        (new ValidateEventSubscriber($factory))->preUpdate($lifeCycleEvent);

        // This will only run if validation passes as an exception will be thrown
        self::assertTrue(true);
    }
}
