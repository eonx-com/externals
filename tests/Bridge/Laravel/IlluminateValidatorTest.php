<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use Illuminate\Validation\Rules\RequiredIf;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Contracts\Translator\TranslatorStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Illuminate\Validator\IlluminateValidatorStub;
use Tests\EoneoPay\Externals\Stubs\Vendor\Symfony\Cache\CacheStub;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\IlluminateValidator
 */
class IlluminateValidatorTest extends TestCase
{
    /**
     * Data for tests.
     *
     * @return mixed[]
     */
    public function getParsedRuleData(): array
    {
        return [
            'simple' => ['required|string', ['Required|string', []]],
            'array' => [['required', 'string'], ['Required', ['string']]],
            'vars' => ['required|max:50', ['Required|max', ['50']]],
            'obj' => [[new RequiredIf(true)], ['Required', []]],
        ];
    }

    /**
     * Tests that getParsedRule behaves correctly.
     *
     * @param mixed $rule
     * @param mixed $expected
     *
     * @return void
     *
     * @dataProvider getParsedRuleData
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testGetParsedRule($rule, $expected): void
    {
        $validator = new IlluminateValidatorStub(
            new CacheStub([]),
            new TranslatorStub(),
            [],
            [],
            [],
            []
        );

        $result = $validator->getParsedRule($rule);

        self::assertSame($expected, $result);
    }

    /**
     * Tests that the cache is used.
     *
     * @return void
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testCache(): void
    {
        $validator = new IlluminateValidatorStub(
            new CacheStub([
                'required|string' => ['EXISTING']
            ]),
            new TranslatorStub(),
            [],
            [],
            [],
            []
        );

        $result = $validator->getParsedRule('required|string');

        self::assertSame(['EXISTING'], $result);
    }
}
