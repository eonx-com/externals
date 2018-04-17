<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Providers\ValidationServiceProvider;
use EoneoPay\External\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Tests\EoneoPay\External\LaravelBridgeProvidersTestCase;

/**
 * @covers \EoneoPay\External\Bridge\Laravel\Providers\ValidationServiceProvider
 * @covers \EoneoPay\External\Bridge\Laravel\Validation\CustomRules
 */
class ValidatorServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test EmptyWith custom validation rule has been successfully loaded
     *
     * @return void
     */
    public function testEmptyWithCustomValidationRule(): void
    {
        $this->registerValidator();

        $validator = $this->getApplication()->get(ValidatorInterface::class);

        self::assertFalse($validator->validate(
            ['key' => 'value', 'value' => 'value'],
            ['key' => 'empty_with:value|string']
        ));
        self::assertTrue($validator->validate(
            ['value' => 'value'],
            ['key' => 'empty_with:value|string']
        ));
    }

    /**
     * Test provider bind translator and validator into container.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        $this->registerValidator();

        self::assertInstanceOf(Translator::class, $this->getApplication()->get(TranslatorContract::class));
        self::assertInstanceOf(ValidatorInterface::class, $this->getApplication()->get(ValidatorInterface::class));
    }

    /**
     * Register validator
     *
     * @return void
     */
    private function registerValidator(): void
    {
        $this->getApplication()->singleton('translator', function () {
            return new Translator(new ArrayLoader(), 'en');
        });

        (new ValidationServiceProvider($this->getApplication()))->register();
    }
}
