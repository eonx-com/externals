<?php
declare(strict_types=1);

namespace EoneoPay\External\ORM\Exceptions;

use EoneoPay\External\ORM\Interfaces\ModelValidationExceptionInterface;
use Illuminate\Support\MessageBag;

class ModelValidationException extends ORMException implements ModelValidationExceptionInterface
{
    /**
     * The message bag containing validation errors
     *
     * @param \Illuminate\Support\MessageBag $messageBag
     */
    private $messageBag;

    /**
     * Create validation error
     *
     * @param \Illuminate\Support\MessageBag $messageBag
     */
    public function __construct(MessageBag $messageBag)
    {
        // Save message bag
        $this->messageBag = $messageBag;

        // Create string from message bag
        $messages = [];
        foreach ($messageBag->getMessages() as $field => $errors) {
            $messages[] = \sprintf('  %s:', $field);

            /** @var array $errors */
            foreach ($errors as $error) {
                $messages[] = \sprintf('   - %s', $error);
            }
        }

        parent::__construct(
            trans('api_messages.validation_failed', ['messages' => PHP_EOL . \implode(PHP_EOL, $messages)])
        );
    }

    /**
     * Return the validation message bag
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getMessageBag(): MessageBag
    {
        return $this->messageBag;
    }
}
