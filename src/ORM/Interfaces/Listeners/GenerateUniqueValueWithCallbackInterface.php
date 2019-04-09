<?php
declare(strict_types=1);

namespace EoneoPay\Externals\ORM\Interfaces\Listeners;

interface GenerateUniqueValueWithCallbackInterface extends GenerateUniqueValueInterface
{
    /**
     * Handle the fields on Ewallet endpoint that should not be user controlled.
     * This method is called from the event listener after a unique value has been generated.
     *
     * @param string $generatedValue
     *
     * @return void
     */
    public function getGeneratedPropertyCallback(string $generatedValue): void;
}
