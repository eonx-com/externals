<?php
declare(strict_types=1);

namespace EoneoPay\Externals\Bridge\Laravel;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator as BaseValidator;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Overridden so that we can cache rule parsing.
 */
class IlluminateValidator extends BaseValidator
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * Constructor
     *
     * @param CacheItemPoolInterface $cache
     * @param Translator $translator
     * @param mixed[] $data
     * @param mixed[] $rules
     * @param mixed[] $messages
     * @param mixed[] $customAttributes
     */
    public function __construct(
        CacheItemPoolInterface $cache,
        Translator $translator,
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        parent::__construct(
            $translator,
            $data,
            $rules,
            $messages,
            $customAttributes
        );

        $this->cache = $cache;
    }

    /**
     * Validate a given attribute against a rule.
     *
     * This method is fully overridden so we can intercept the rule parsing process
     * and resolve it from a cache.
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
        if (($keys = $this->getExplicitKeys($attribute)) &&
            $this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
        }

        $value = $this->getValue($attribute);

        // If the attribute is a file, we will verify that the file upload was actually successful
        // and if it wasn't we will add a failure for the attribute. Files may not successfully
        // upload if they are too large based on PHP's settings so we will bail in this case.
        if ($value instanceof UploadedFile && ! $value->isValid() &&
            $this->hasRule($attribute, array_merge($this->fileRules, $this->implicitRules))
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

        if ($validatable && ! $this->$method($attribute, $value, $parameters, $this)) {
            $this->addFailure($attribute, $rule, $parameters);
        }
    }

    /**
     * Get a rule and its parameters for a given attribute.
     *
     * This method is fully overridden so we can intercept the rule parsing process
     * and resolve it from a cache.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     *
     * @param string $attribute
     * @param string|mixed[] $rules
     *
     * @return array|mixed[]
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
            [$rule, $parameters] = $this->getParsedRule($rule);($rule);

            if (\in_array($rule, $rules, true) === true) {
                return [$rule, $parameters];
            }
        }

        return null;
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
    private function getParsedRule($rule): array
    {
        $key = $this->getCacheKey($rule);

        $item = $this->cache->getItem($key);
        if ($item->isHit() === true) {
            return $item->get();
        }

        $parsed = ValidationRuleParser::parse($rule);

        $item->set($parsed);

        return $parsed;
    }

    /**
     * Returns a cache key for a rule.
     *
     * @param string|mixed[] $rule
     *
     * @return string
     */
    private function getCacheKey($rule): string
    {
        if (\is_string($rule) === true) {
            return $rule;
        }

        $key = [];

        foreach ($rule as $item) {
            if (\is_string($item) === true) {
                $key[] = $item;

                continue;
            }

            if (\is_object($item) === true) {
                $key[] = \get_class($item);

                continue;
            }

            $key[] = \gettype($item);
        }

        return \implode('|', $key);
    }
}
