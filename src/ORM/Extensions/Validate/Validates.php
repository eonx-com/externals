<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Extensions\Validate;

use EoneoPay\External\ORM\Exceptions\ModelValidationException;

trait Validates
{
    /**
     * Validate an entity prior to saving
     *
     * @return void
     *
     * @throws \EoneoPay\External\ORM\Exceptions\ModelValidationException If validation fails
     */
    public function validate(): void
    {
        /** @var \Illuminate\Validation\Factory $factory */
        $factory = \app()->make('validator');

        // Build data array to validate as serializer will remap fields
        $data = [];

        foreach ($this->getRules() ?? [] as $field => $rules) {
            $method = \sprintf('get%s', $field);
            $data[$field] = $this->{$method}();
        }

        /** @var \Illuminate\Validation\Validator $validator */
        $validator = $factory->make($data, $this->getRules() ?? []);

        if ($validator->fails()) {
            throw new ModelValidationException($validator->getMessageBag());
        }
    }

    /**
     * Get validation rules for this model
     *
     * @return array
     */
    abstract protected function getRules(): array;
}
