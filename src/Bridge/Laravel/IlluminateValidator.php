<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator as BaseValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\Cache\CacheInterface;

// phpcs:disable
/**
 * Overridden so that we can cache rule parsing.
 *
 * @SuppressWarnings(PHPMD) This file is almost a direct copy from Lumen.
 */
class IlluminateValidator extends BaseValidator
{
    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private $cache;

    /**
     * Create a new Validator instance.
     *
     * @param \Symfony\Contracts\Cache\CacheInterface $cache
     * @param \Illuminate\Contracts\Translation\Translator $translator
     * @param mixed[] $data
     * @param mixed[] $rules
     * @param mixed[] $messages
     * @param mixed[] $customAttributes
     */
    public function __construct(
        CacheInterface $cache,
        Translator $translator,
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        $this->cache = $cache;

        parent::__construct($translator, $data, $rules, $messages, $customAttributes);
    }

    /**
     * Returns a cache key for a rule.
     *
     * @param string|mixed[] $rule
     *
     * @return string
     */
    protected function getCacheKey($rule): string
    {
        if (\is_string($rule) === true) {
            return $rule;
        }

        $key = ['__array'];

        foreach ($rule as $item) {
            if (\is_string($item) === true) {
                $key[] = $item;

                continue;
            }

            if (\is_object($item) === true) {
                $key[] = \get_class($item);

                continue;
            }

            // @codeCoverageIgnoreStart
            // Catch all behaviour for lumen
            $key[] = \gettype($item);
            // @codeCoverageIgnoreEnd
        }

        return \implode('|', $key);
    }

    /**
     * Parses a rule.
     *
     * @param mixed $rule
     *
     * @return mixed[]
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function getParsedRule($rule): array
    {
        $key = $this->getCacheKey($rule);
        $saneKey = \str_replace(['{', '}', '(', ')', '/', '\\', '@', ':'], '__', $key);

        return $this->cache->get($saneKey, static function () use ($rule): array {
            return ValidationRuleParser::parse($rule);
        });
    }

    /**
     * Get a rule and its parameters for a given attribute.
     *
     * This method is fully overridden so we can intercept the rule parsing process
     * and resolve it from a cache.
     *
     * @codeCoverageIgnore The code in this method is a direct copy and paste from Laravel.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @param string $attribute
     * @param string|mixed[] $rules
     *
     * @return mixed[]
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function getRule($attribute, $rules): ?array
    {
        if (\array_key_exists($attribute, $this->rules) === false) {
            return null;
        }

        $rules = (array) $rules;

        foreach ($this->rules[$attribute] as $rule) {
            [$rule, $parameters] = $this->getParsedRule($rule);

            if (\in_array($rule, $rules, true) === true) {
                return [$rule, $parameters];
            }
        }

        return null;
    }

    /**
     * Validate a given attribute against a rule.
     *
     * This method is fully overridden so we can intercept the rule parsing process
     * and resolve it from a cache.
     *
     * @codeCoverageIgnore The code in this method is a direct copy and paste from Laravel.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @param string $attribute
     * @param string $rule
     *
     * @return void
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function validateAttribute($attribute, $rule): void
    {
        $this->currentRule = $rule;

        [$rule, $parameters] = $this->getParsedRule($rule);

        if ($rule === '') {
            return;
        }

        // First we will get the correct keys for the given attribute in case the field is nested in
        // an array. Then we determine if the given rule accepts other field names as parameters.
        // If so, we will replace any asterisks found in the parameters with the correct keys.
        $keys = $this->getExplicitKeys($attribute);
        if (\count($keys) > 0 && $this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        }

        $value = $this->getValue($attribute);

        // If the attribute is a file, we will verify that the file upload was actually successful
        // and if it wasn't we will add a failure for the attribute. Files may not successfully
        // upload if they are too large based on PHP's settings so we will bail in this case.
        if ($value instanceof UploadedFile === true && $value->isValid() === true &&
            $this->hasRule($attribute, \array_merge($this->fileRules, $this->implicitRules)) === true
        ) {
            $this->addFailure($attribute, 'uploaded', []);

            return;
        }

        // If we have made it this far we will make sure the attribute is validatable and if it is
        // we will call the validation method with the attribute. If a method returns false the
        // attribute is invalid and we will add a failure message for this failing attribute.
        $validatable = $this->isValidatable($rule, $attribute, $value);

        if ($rule instanceof RuleContract) {
            $this->validateUsingCustomRule($attribute, $value, $rule);

            return;
        }

        $method = "validate{$rule}";

        if ($validatable === true && $this->$method($attribute, $value, $parameters, $this) !== true) {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }
}
// phpcs:enable
