<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\IlluminateValidator;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;

final class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        // Overload validator factory to add our own rules in
        $this->app->extend(Factory::class, static function (Factory $factory): Factory {
            $factory->resolver(static function ($translator, $data, $rules, $messages, $customAttributes) {
                // @codeCoverageIgnoreStart
                // Hack to return our validator
                return new IlluminateValidator(
                    $translator,
                    $data,
                    $rules,
                    $messages,
                    $customAttributes
                );
                // @codeCoverageIgnoreEnd
            });

            return $factory;
        });

        $this->app->bind(ValidatorInterface::class, Validator::class);
    }
}
