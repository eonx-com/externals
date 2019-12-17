<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\IlluminateValidator;
use EoneoPay\Externals\Bridge\Laravel\Validator;
use EoneoPay\Externals\Validator\Interfaces\ValidatorInterface;
use Illuminate\Contracts\Validation\Factory as FactoryInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

final class ValidationServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton('validator_cache', static function () {
            return new ArrayAdapter();
        });

        // Overload validator factory to add our own rules in
        $this->app->extend(
            FactoryInterface::class,
            static function (FactoryInterface $factory): FactoryInterface {
                if ($factory instanceof Factory === true) {
                    $factory->resolver(static function ($translator, $data, $rules, $messages, $customAttributes) {
                        // @codeCoverageIgnoreStart
                        // Hack to return our validator
                        return new IlluminateValidator(
                            $this->app->make('validator_cache'),
                            $translator,
                            $data,
                            $rules,
                            $messages,
                            $customAttributes
                        );
                        // @codeCoverageIgnoreEnd
                    });
                }

                return $factory;
            }
        );

        $this->app->bind(ValidatorInterface::class, Validator::class);
    }
}
