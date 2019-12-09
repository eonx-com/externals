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
     * Provides valid urls.
     *
     * @return mixed[]
     */
    public function provideValidUrls(): iterable
    {
        return [
            ['//example.com'],
            ['//examp_le.com'],
            ['//laravel.fake/blog/'],
            ['//laravel.com/search?type=&q=url+validator'],
            ['http://a.pl'],
            ['http://www.example.com'],
            ['http://www.example.com.'],
            ['http://www.example.museum'],
            ['https://example.com/'],
            ['https://example.com:80/'],
            ['http://examp_le.com'],
            ['http://www.sub_domain.examp_le.com'],
            ['http://www.example.coop/'],
            ['http://www.test-example.com/'],
            ['http://www.laravel.com/'],
            ['http://laravel.fake/blog/'],
            ['http://laravel.com/?'],
            ['http://laravel.com/search?type=&q=url+validator'],
            ['http://laravel.com/#'],
            ['http://laravel.com/#?'],
            ['http://www.laravel.com/doc/current/book/validation.html#supported-constraints'],
            ['http://very.long.domain.name.com/'],
            ['http://localhost/'],
            ['http://myhost123/'],
            ['http://127.0.0.1/'],
            ['http://127.0.0.1:80/'],
            ['http://[::1]/'],
            ['http://[::1]:80/'],
            ['http://[1:2:3::4:5:6:7]/'],
            ['http://sãopaulo.com/'],
            ['http://xn--sopaulo-xwa.com/'],
            ['http://sãopaulo.com.br/'],
            ['http://xn--sopaulo-xwa.com.br/'],
            ['http://пример.испытание/'],
            ['http://xn--e1afmkfd.xn--80akhbyknj4f/'],
            ['http://مثال.إختبار/'],
            ['http://xn--mgbh0fb.xn--kgbechtv/'],
            ['http://例子.测试/'],
            ['http://xn--fsqu00a.xn--0zwm56d/'],
            ['http://例子.測試/'],
            ['http://xn--fsqu00a.xn--g6w251d/'],
            ['http://例え.テスト/'],
            ['http://xn--r8jz45g.xn--zckzah/'],
            ['http://مثال.آزمایشی/'],
            ['http://xn--mgbh0fb.xn--hgbk6aj7f53bba/'],
            ['http://실례.테스트/'],
            ['http://xn--9n2bp8q.xn--9t4b11yi5a/'],
            ['http://العربية.idn.icann.org/'],
            ['http://xn--ogb.idn.icann.org/'],
            ['http://xn--e1afmkfd.xn--80akhbyknj4f.xn--e1afmkfd/'],
            ['http://xn--espaa-rta.xn--ca-ol-fsay5a/'],
            ['http://xn--d1abbgf6aiiy.xn--p1ai/'],
            ['http://☎.com/'],
            ['http://username:password@laravel.com'],
            ['http://user.name:password@laravel.com'],
            ['http://user_name:pass_word@laravel.com'],
            ['http://username:pass.word@laravel.com'],
            ['http://user.name:pass.word@laravel.com'],
            ['http://user-name@laravel.com'],
            ['http://user_name@laravel.com'],
            ['http://laravel.com?'],
            ['http://laravel.com?query=1'],
            ['http://laravel.com/?query=1'],
            ['http://laravel.com#'],
            ['http://laravel.com#fragment'],
            ['http://laravel.com/#fragment'],
            ['http://laravel.com/#one_more%20test'],
            ['http://example.com/exploit.html?hello[0]=test'],
        ];
    }

    /**
     * Provides invalid urls.
     *
     * @return array
     */
    public function provideInvalidUrls(): array
    {
        return [
            ['example.com'],
            ['://example.com'],
            ['http ://example.com'],
            ['http:/example.com'],
            ['http://example.com::aa'],
            ['http://example.com:aa'],
            ['ftp://example.fr'],
            ['faked://example.fr'],
            ['http://127.0.0.1:aa/'],
            ['ftp://[::1]/'],
            ['http://[::1'],
            ['http://hello.☎/'],
            ['http://:password@laravel.com'],
            ['http://:password@@laravel.com'],
            ['http://username:laravel.com'],
            ['http://usern@me:password@laravel.com'],
            ['http://example.com/exploit.html?<script>alert(1);</script>'],
            ['http://example.com/exploit.html?hel lo'],
            ['http://example.com/exploit.html?not_a%hex'],
            ['http://'],
            [null],
        ];
    }

    /**
     * Test `extended_url` rule succeeds.
     *
     * @param string $validUrl
     *
     * @return void
     *
     * @dataProvider provideValidUrls
     */
    public function testValidatorCustomRuleExtendedUrlSucceeds(string $validUrl): void
    {
        $validator = new Validator(new Factory(new Translator(new ArrayLoader(), 'en')));

        $actualResult = $validator->validate(['url' => $validUrl], ['url' => 'extended_url']);

        self::assertTrue($actualResult);
        self::assertSame($validator->getFailures(), []);
    }

    /**
     * Test `extended_url` rule fails.
     *
     * @param mixed $invalidUrl
     *
     * @return void
     *
     * @dataProvider provideInvalidUrls
     */
    public function testValidatorCustomRuleExtendedUrlFails($invalidUrl): void
    {
        $loader = new ArrayLoader();
        $loader->addMessages('en', 'validation', ['extended_url' => 'The :attribute format is invalid.']);
        $validator = new Validator(new Factory($translator = new Translator($loader, 'en')));

        $actualResult = $validator->validate(['url' => $invalidUrl], ['url' => 'extended_url']);

        self::assertFalse($actualResult);
        self::assertSame(['url' => ['The url format is invalid.']], $validator->getFailures());
    }
}
