<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Validator\Constraints\DateEqualToValidator;
use EoneoPay\Externals\Validator\Constraints\DateGreaterThanOrEqualValidator;
use EoneoPay\Externals\Validator\Constraints\DateGreaterThanValidator;
use EoneoPay\Externals\Validator\Constraints\DateLessThanOrEqualValidator;
use EoneoPay\Externals\Validator\Constraints\DateLessThanValidator;
use EoneoPay\Externals\Validator\Constraints\DateNotEqualToValidator;
use EoneoPay\Externals\Validator\Constraints\FilterValidator;
use Illuminate\Support\ServiceProvider;

final class ValidationConstraintServiceProvider extends ServiceProvider
{
    /**
     * @noinspection PhpMissingParentCallCommonInspection Parent implementation is empty
     *
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->bind(DateEqualToValidator::class);
        $this->app->bind(DateGreaterThanValidator::class);
        $this->app->bind(DateGreaterThanOrEqualValidator::class);
        $this->app->bind(DateLessThanValidator::class);
        $this->app->bind(DateLessThanOrEqualValidator::class);
        $this->app->bind(DateNotEqualToValidator::class);
        $this->app->bind(FilterValidator::class);
    }
}
