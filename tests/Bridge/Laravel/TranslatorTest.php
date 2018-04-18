<?php
declare(strict_types=1);

namespace Tests\EoneoPay\Externals\Bridge\Laravel;

use EoneoPay\Externals\Bridge\Laravel\Translator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator as ContractedTranslator;
use Tests\EoneoPay\Externals\TestCase;

/**
 * @covers \EoneoPay\Externals\Bridge\Laravel\Translator
 */
class TranslatorTest extends TestCase
{
    /**
     * Test translator handles replacements
     *
     * @return void
     */
    public function testTranslatorReplacesVariablesInMessages(): void
    {
        $language = ['message' => 'message received: :variable'];

        $loader = new ArrayLoader();
        $loader->addMessages('en', 'test', $language);

        $translator = new Translator(new ContractedTranslator($loader, 'en'));

        self::assertSame($language['message'], $translator->get('test.message'));
        self::assertSame(
            \str_replace(':variable', 'replacement', $language['message']),
            $translator->get('test.message', ['variable' => 'replacement'])
        );
    }

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
        self::assertSame($english['message'], $translator->get('test.message', [], 'en'));
        self::assertSame($french['message'], $translator->get('test.message', [], 'fr'));
        self::assertSame('test.message', $translator->get('test.message', [], 'invalid'));
    }

    /**
     * Test translator string method
     *
     * @return void
     */
    public function testTranslatorStringAlwaysReturnsString(): void
    {
        $language = [
            'messages' => [
                'one' => 'First message',
                'two' => 'Second message'
            ]
        ];

        $loader = new ArrayLoader();
        $loader->addMessages('en', 'test', $language);

        $translator = new Translator(new ContractedTranslator($loader, 'en'));

        self::assertSame($language['messages']['one'], $translator->trans('test.messages.one'));
        self::assertSame(\implode(', ', $language['messages']), $translator->trans('test.messages'));
    }
}
