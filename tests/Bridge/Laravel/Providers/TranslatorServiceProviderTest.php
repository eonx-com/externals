<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\TranslatorServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Translator;
use EoneoPay\Externals\Translator\Interfaces\TranslatorInterface;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator as IlluminateTranslator;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\TranslatorServiceProvider
 */
class TranslatorServiceProviderTest extends TestCase
{
    /**
     * Test service provider register translators into Laravel application.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Bind illuminate translator to key
        $application->bind('translator', static function (): IlluminateTranslator {
            return new IlluminateTranslator(new ArrayLoader(), 'en');
        });

        // Register services
        (new TranslatorServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(IlluminateTranslator::class, $application->get('translator'));
        self::assertInstanceOf(Translator::class, $application->get(TranslatorInterface::class));
    }
}
