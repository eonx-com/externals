<?php
declare(strict_types=1);

namespace EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Validator\Interfaces\ValidatorInterface;
use Illuminate\Validation\Factory;

class Validator implements ValidatorInterface
{
    /**
     * Validation factory instance
     *
     * @var \Illuminate\Validation\Factory
     */
    private $factory;

    /**
     * Validation instance
     *
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * Create new validation instance
     *
     * @param \Illuminate\Validation\Factory $factory Validation factory instance
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get messages from the last validation attempt
     *
     * @return array
     */
    public function getFailures(): array
    {
        return $this->validator->getMessageBag()->toArray();
    }

    /**
     * Validate the given data against the provided rules
     *
     * @param array $data Data to validate
     * @param array $rules Rules to validate against
     *
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        $this->validator = $this->factory->make($data, $rules);

        return $this->validator->passes();
    }
}
