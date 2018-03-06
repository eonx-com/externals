<?php
declare(strict_types=1);

namespace Tests\EoneoPay\External\Bridge\Laravel;

use EoneoPay\External\Bridge\Laravel\Translator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator as ContractedTranslator;
use Tests\EoneoPay\External\TestCase;

/**
 * @covers \EoneoPay\External\Bridge\Laravel\Translator
 */
class TranslatorTest extends TestCase
{
    /**
     * Test translator can retrieve messages
     *
     * @return void
     */
    public function testTranslatorRetrievesMessages(): void
    {
        $english = ['message' => 'message received'];
        $french = ['message' => 'message reçu'];

        $loader = new ArrayLoader();
        $loader->addMessages('en', 'test', $english);
        $loader->addMessages('fr', 'test', $french);

        $translator = new Translator(new ContractedTranslator($loader, 'en'));

        // Test default, english, french and unknown
        self::assertSame($english['message'], $translator->get('test.message'));
        self::assertSame($english['message'], $translator->get('test.message', 'en'));
        self::assertSame($french['message'], $translator->get('test.message', 'fr'));
        self::assertSame('test.message', $translator->get('test.message', 'invalid'));
    }
}
