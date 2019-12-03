<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\ORM\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use EoneoPay\Externals\ORM\Exceptions\UniqueValueNotGeneratedException;
use EoneoPay\Externals\ORM\Listeners\GenerateUniqueValue;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\EntityStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\GenerateUniqueValueStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\GenerateUniqueValueWithCallbackStub;
use Tests\EoneoPay\Externals\Stubs\ORM\Entities\GenerateUniqueValueWithCheckDigitStub;
use Tests\EoneoPay\Externals\Stubs\Translator\TranslatorStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\EoneoPay\Utils\GeneratorStub;
use Tests\EoneoPay\Externals\TestCases\ORMTestCase;

/**
 * @covers \EoneoPay\Externals\ORM\Listeners\GenerateUniqueValue
 */
class GenerateUniqueValueTest extends ORMTestCase
{
    /**
     * Test callback is invoked if it exists.
     *
     * @return void
     */
    public function testGenerationWithCallbackInvokesCallback(): void
    {
        $entity = new GenerateUniqueValueWithCallbackStub();

        $this->getEntityManager()->persist($entity);

        // Callback should overwrite generated value
        self::assertSame('callback', $entity->getGeneratedValue());
    }

    /**
     * Test generation with check digit consitently adds the same check digit.
     *
     * @return void
     */
    public function testGenerationWithCheckDigit(): void
    {
        $stub = new GeneratorStub();
        $entity = new GenerateUniqueValueWithCheckDigitStub();

        // Invoke generate with known 'random' string
        $generator = new GenerateUniqueValue($stub, new TranslatorStub());
        $generator->prePersist(new LifecycleEventArgs($entity, $this->getDoctrineEntityManager()));

        self::assertSame('notrandom3', (string)$entity->getGeneratedValue());
    }

    /**
     * Ensure generator will only execute against entities that have the interface implemented.
     *
     * @return void
     */
    public function testGeneratorSkipsNoInterface(): void
    {
        $entity = new EntityStub();

        $expected = $this->getEntityContents($entity);

        $this->getEntityManager()->persist($entity);

        // Remove entity id
        $actual = $this->getEntityContents($entity);
        $actual['entityId'] = null;

        self::assertSame($expected, $actual);
    }

    /**
     * Test pre-persist method works as expeceted.
     *
     * @return void
     */
    public function testPrePersist(): void
    {
        $entity = new GenerateUniqueValueStub();

        self::assertNull($entity->getGeneratedValue());

        $this->getEntityManager()->persist($entity);

        self::assertSame(9, \mb_strlen((string)$entity->getGeneratedValue()));
    }

    /**
     * Ensure generator will throw an Exception after one hundred attempts of generating a unique random value.
     *
     * @return void
     */
    public function testPrePersistThrowsExceptionIfAttemptsExhausted(): void
    {
        $stub = new GeneratorStub();

        $entity = new GenerateUniqueValueStub();
        $this->getEntityManager()->persist($entity);

        // Set key to match generator stub, since stub returns the same value every time a
        // unique value will never be found and the exception should be thrown
        $entity->setGeneratedValue($stub->randomString());
        $this->getEntityManager()->flush();

        $this->expectException(UniqueValueNotGeneratedException::class);

        $generator = new GenerateUniqueValue($stub, new TranslatorStub());
        $generator->prePersist(new LifecycleEventArgs($entity, $this->getDoctrineEntityManager()));
    }
}
