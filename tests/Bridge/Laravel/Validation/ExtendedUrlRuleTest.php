<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel\Validation;

use EoneoPay\Externals\Bridge\Laravel\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Validation\ExtendedUrlRule
 */
final class ExtendedUrlRuleTest extends TestCase
{
    /**
     * Data provider for testing extended_url rule.
     *
     * @return mixed[]
     */
    public function getValidationData(): iterable
    {
        yield 'Not a string' => [
            'data' => ['value' => 123],
            'result' => ['value' => ['The value format is invalid.']],
            'rules' => ['value' => 'extended_url'],
        ];

        yield 'Not an url' => [
            'data' => ['value' => 'not-an-url/'],
            'result' => ['value' => ['The value format is invalid.']],
            'rules' => ['value' => 'extended_url'],
        ];

        yield 'Url without protocol' => [
            'data' => ['value' => '//example.com/'],
            'result' => [],
            'rules' => ['value' => 'extended_url'],
        ];

        yield 'Url including protocol' => [
            'data' => ['value' => 'https://example.com'],
            'result' => [],
            'rules' => ['value' => 'extended_url'],
        ];

        yield 'Url with ipv4 including protocol' => [
            'data' => ['value' => 'http://127.0.0.1/'],
            'result' => [],
            'rules' => ['value' => 'extended_url'],
        ];

        yield 'Url with ipv6 including protocol' => [
            'data' => ['value' => 'http://[2001:db8:a0b:12f0::1]/'],
            'result' => [],
            'rules' => ['value' => 'extended_url'],
        ];
    }

    /**
     * Test custom rule to extended_url.
     *
     * @param mixed[] $data
     * @param mixed[] $result
     * @param mixed[] $rules
     *
     * @return void
     *
     * @dataProvider getValidationData()
     */
    public function testValidatorCustomRuleCustomUrl(array $data, array $result, array $rules): void
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['extended_url' => 'The :attribute format is invalid.']);

        $validator = new Validator(new Factory(new Translator($loader, 'en')));

        self::assertSame($result, $validator->validate($data, $rules));
    }
}
