<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\ValidationServiceProvider;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Translation\Translator as IlluminateTranslatorContract;
use Illuminate\Contracts\Validation\Factory as IlluminateValidatorContract;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator as IlluminateTranslator;
use Illuminate\Validation\Factory as IlluminateValidator;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\ValidationServiceProvider
 */
class ValidatorServiceProviderTest extends TestCase
{
    /**
     * Test provider bind translator and validator into container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();

        // Bind illuminate translator to key
        $application->bind('translator', static function () {
            return new IlluminateTranslator(new ArrayLoader(), 'en');
        });

        // Bind illuminate validator
        $application->bind(IlluminateValidatorContract::class, static function () {
            return new IlluminateValidator(new IlluminateTranslator(new ArrayLoader(), 'en'));
        });

        // Run registration
        (new ValidationServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(IlluminateTranslator::class, $application->get(IlluminateTranslatorContract::class));
        self::assertInstanceOf(Validator::class, $application->get(ValidatorInterface::class));
    }
}
