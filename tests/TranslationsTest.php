<?php

declare(strict_types=1);

namespace Hschottm\FaqExtensionsBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class TranslationsTest extends TestCase
{
    private const LOCALES = ['cs', 'de', 'en', 'es', 'fa', 'fr', 'it', 'ja', 'lv', 'nl', 'pl', 'pt', 'ru', 'sk', 'sl', 'sr', 'sv', 'tr'];

    private function catalogue(string $locale): array
    {
        $previous = $GLOBALS['TL_LANG'] ?? [];
        $GLOBALS['TL_LANG'] = [];
        try {
            foreach (['modules', 'tl_faq', 'tl_module'] as $domain) {
                require __DIR__.'/../src/Resources/contao/languages/'.$locale.'/'.$domain.'.php';
            }
            return $GLOBALS['TL_LANG'];
        } finally {
            $GLOBALS['TL_LANG'] = $previous;
        }
    }

    private function flatten(array $values, string $prefix = ''): array
    {
        $flat = [];
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $flat += $this->flatten($value, $prefix.$key.'.');
            } else {
                $flat[$prefix.$key] = $value;
            }
        }
        return $flat;
    }

    public function testEveryOfficialCoreAndFaqLocaleHasCompleteCatalogues(): void
    {
        foreach (['core-bundle', 'faq-bundle'] as $bundle) {
            foreach (glob(__DIR__.'/../vendor/contao/'.$bundle.'/contao/languages/*', GLOB_ONLYDIR) as $directory) {
                self::assertContains(basename($directory), self::LOCALES, 'Official locale requires translations: '.basename($directory));
            }
        }
        $reference = $this->flatten($this->catalogue('en'));
        $voteReference = Yaml::parseFile(__DIR__.'/../src/Resources/translations/faqextensions.en.yaml')['vote'];
        foreach (self::LOCALES as $locale) {
            $catalogue = $this->flatten($this->catalogue($locale));
            self::assertSame(array_keys($reference), array_keys($catalogue), $locale.': backend translation keys');
            foreach ($catalogue as $key => $value) {
                self::assertIsString($value);
                self::assertNotSame('', trim($value), $locale.': '.$key);
                self::assertTrue(mb_check_encoding($value, 'UTF-8'), $locale.': '.$key);
            }
            $message = $catalogue['tl_faq.nOutofmFoundThisArticleHelpful'];
            $formatted = sprintf($message, 17, 29);
            self::assertStringContainsString('17', $formatted, $locale);
            self::assertStringContainsString('29', $formatted, $locale);
            self::assertStringNotContainsString('%', $formatted, $locale);
            $votes = Yaml::parseFile(__DIR__.'/../src/Resources/translations/faqextensions.'.$locale.'.yaml')['vote'];
            self::assertSame(array_keys($voteReference), array_keys($votes), $locale.': voting translation keys');
            foreach ($votes as $key => $value) {
                self::assertIsString($value);
                self::assertNotSame('', trim($value), $locale.': vote.'.$key);
                self::assertTrue(mb_check_encoding($value, 'UTF-8'));
            }
        }
    }
}
