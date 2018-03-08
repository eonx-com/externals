<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel\Providers;

use EoneoPay\External\Bridge\Laravel\Providers\TranslatorServiceProvider;
use EoneoPay\External\Translator\Interfaces\TranslatorInterface;
use Illuminate\Contracts\Translation\Translator as IlluminateTranslatorContract;
use Tests\EoneoPay\External\LaravelBridgeProvidersTestCase;

class TranslatorServiceProviderTest extends LaravelBridgeProvidersTestCase
{
    /**
     * Test service provider register translators into Laravel application.
     *
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegister(): void
    {
        (new TranslatorServiceProvider($this->getApplication()))->register();

        self::assertInstanceOf(IlluminateTranslatorContract::class, $this->getApplication()->get('translator'));
        self::assertInstanceOf(TranslatorInterface::class, $this->getApplication()->get(TranslatorInterface::class));
    }
}
