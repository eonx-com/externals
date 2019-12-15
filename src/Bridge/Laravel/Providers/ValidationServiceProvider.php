<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;

final class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Interface for validating adhoc objects, depends on translator
        $this->app->bind(ValidatorInterface::class, static function (Container $app): ValidatorInterface {
            $factory = new Factory(
                $app->make('translator'),
                $app
            );
            $factory->resolver(static function ($translator, $data, $rules, $messages, $customAttributes) {
                return new \Illuminate\Validation\Validator();
                return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);

            });

            return new Validator($factory, $presence);
        } );
    }
}
