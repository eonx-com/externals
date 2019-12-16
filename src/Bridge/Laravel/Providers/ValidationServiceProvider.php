<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\IlluminateValidator;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use Illuminate\Validation\PresenceVerifierInterface;

final class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(ValidatorInterface::class, static function (Container $app): ValidatorInterface {
            $factory = new Factory(
                $app->make('translator'),
                $app
            );
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

            $verifier = $app->bound(PresenceVerifierInterface::class) === true
                ? $app->make(PresenceVerifierInterface::class)
                : null;

            return new Validator(
                $factory,
                $verifier
            );
        });
    }
}
