<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\ORM\Subscribers;

use Doctrine\ORM\Events;
use EoneoPay\External\ORM\Subscribers\ValidateEventSubscriber;
use Tests\EoneoPay\External\ORM\Stubs\InterfaceAndGetRulesStub;
use Tests\EoneoPay\External\ORM\Stubs\InterfaceNoGetRulesStub;
use Tests\EoneoPay\External\SubscribersTestCase;

class ValidateEventSubscriberTest extends SubscribersTestCase
{
    /**
     * Subscriber should return prePersist and preUpdate events.
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertEquals(
            [Events::prePersist, Events::preUpdate],
            (new ValidateEventSubscriber($this->mockValidator()))->getSubscribedEvents()
        );
    }

    /**
     * Subscriber should not call validate if event object getRules does not return an array.
     */
    public function testShouldNotValidateIfGetRulesNotArray(): void
    {
        $this->processNotValidateTest(new InterfaceAndGetRulesStub());
    }

    /**
     * Subscriber should not call validate if event object does not have getRules method.
     */
    public function testShouldNotValidateIfNoGetRulesMethod(): void
    {
        $this->processNotValidateTest(new InterfaceNoGetRulesStub());
    }

    /**
     * Subscriber should not call validate if event object is not EntityInterface.
     */
    public function testShouldNotValidateIfNotEntityInterface(): void
    {
        $this->processNotValidateTest(new \stdClass());
    }

    /**
     * Subscriber should validate if event object is EntityInterface and getRules returns an array.
     */
    public function testShouldValidateIfInterfaceAndGetRulesIsArray(): void
    {
        $validator = $this->mockValidator();
        $validator->shouldReceive('setRules')->once()->with([])->andReturnSelf();
        $validator->shouldReceive('setData')->once()->with([])->andReturnSelf();
        $validator->shouldReceive('validate')->once()->withNoArgs()->andReturn(null);

        $object = (new InterfaceAndGetRulesStub())->setRules();

        self::assertNull((new ValidateEventSubscriber($validator))->preUpdate($this->mockLifeCycleEvent($object)));
    }
}
