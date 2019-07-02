<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Providers;

use EoneoPay\Externals\Bridge\Laravel\Providers\ValidationConstraintServiceProvider;
use EoneoPay\Externals\Validator\Constraints\DateEqualToValidator;
use EoneoPay\Externals\Validator\Constraints\DateGreaterThanOrEqualValidator;
use EoneoPay\Externals\Validator\Constraints\DateGreaterThanValidator;
use EoneoPay\Externals\Validator\Constraints\DateLessThanOrEqualValidator;
use EoneoPay\Externals\Validator\Constraints\DateLessThanValidator;
use EoneoPay\Externals\Validator\Constraints\DateNotEqualToValidator;
use EoneoPay\Externals\Validator\Constraints\FilterValidator;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Foundation\ApplicationStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Providers\ValidationConstraintServiceProvider
 */
class ValidationConstraintServiceProviderTest extends TestCase
{
    /**
     * Test provider register container.
     *
     * @return void
     */
    public function testRegister(): void
    {
        $application = new ApplicationStub();
        $application->bind(PropertyAccessorInterface::class);

        // Run registration
        (new ValidationConstraintServiceProvider($application))->register();

        // Ensure services are bound
        self::assertInstanceOf(
            DateEqualToValidator::class,
            $application->get(DateEqualToValidator::class)
        );
        self::assertInstanceOf(
            DateGreaterThanValidator::class,
            $application->get(DateGreaterThanValidator::class)
        );
        self::assertInstanceOf(
            DateGreaterThanOrEqualValidator::class,
            $application->get(DateGreaterThanOrEqualValidator::class)
        );
        self::assertInstanceOf(
            DateLessThanValidator::class,
            $application->get(DateLessThanValidator::class)
        );
        self::assertInstanceOf(
            DateLessThanOrEqualValidator::class,
            $application->get(DateLessThanOrEqualValidator::class)
        );
        self::assertInstanceOf(
            DateNotEqualToValidator::class,
            $application->get(DateNotEqualToValidator::class)
        );
        self::assertInstanceOf(
            FilterValidator::class,
            $application->get(FilterValidator::class)
        );
    }
}
