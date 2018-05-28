<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EoneoPay\Externals\ORM\Subscribers\ValidateEventSubscriber;
use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Mockery;
use Mockery\MockInterface;

abstract class SubscribersTestCase extends TestCase
{
    /**
     * Mock Doctrine life cycle event with getObject expectation returning given object.
     *
     * @param mixed $object
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function mockLifeCycleEvent($object): MockInterface
    {
        $event = Mockery::mock(LifecycleEventArgs::class);
        $event->shouldReceive('getObject')->once()->withNoArgs()->andReturn($object);

        return $event;
    }

    /**
     * Mock TranslatorInterface.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function mockTranslator(): MockInterface
    {
        return Mockery::mock(TranslatorInterface::class);
    }

    /**
     * Mock ValidatorInterface.
     *
     * @return \Mockery\MockInterface
     *
     * @SuppressWarnings(PHPMD.StaticAccess) Inherited from Mockery
     */
    protected function mockValidator(): MockInterface
    {
        return Mockery::mock(ValidatorInterface::class);
    }

    /**
     * Process test when subscriber should not validate.
     *
     * @param mixed $object
     *
     * @return void
     *
     * @throws \EoneoPay\Externals\ORM\Exceptions\EntityValidationFailedException
     */
    protected function processNotValidateTest($object): void
    {
        $validator = $this->mockValidator();
        $validator->shouldNotReceive('validate');

        $translator = $this->mockTranslator();
        $translator->shouldNotReceive('trans');

        /** @var \Doctrine\ORM\Event\LifecycleEventArgs $event */
        $event = $this->mockLifeCycleEvent($object);
        /** @var \EoneoPay\Externals\Validator\Interfaces\ValidatorInterface $validator */
        /** @var \EoneoPay\Externals\Translator\Interfaces\TranslatorInterface $translator */
        (new ValidateEventSubscriber($validator, $translator))->prePersist($event);

        self::assertTrue(true);
    }
}
