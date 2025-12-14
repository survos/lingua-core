<?php
declare(strict_types=1);

namespace Survos\Lingua\Core\Identity;

/**
 * Canonical, shared identity functions.
 *
 * Rules:
 * - Source key is derived from (normalized text + source locale)
 * - Translation key is derived from (source hash + target locale + engine)
 * - Engine is REQUIRED for translation keys (no legacy/implicit omission)
 */
final class HashUtil
{
    private function __construct() {}

    public static function calcSourceKey(string $text, string $sourceLocale): string
    {
        $normText = self::normalizeText($text);
        $loc      = self::normalizeLocale($sourceLocale);

        // include locale as a namespace separator; stable across apps
        return hash('xxh3', $loc."\n".$normText);
    }

    public static function calcTranslationKey(string $sourceHash, string $targetLocale, string $engine): string
    {
        $hash   = strtolower(trim($sourceHash));
        $loc    = self::normalizeLocale($targetLocale);
        $engine = self::normalizeEngine($engine);

        if ($engine === '') {
            throw new \InvalidArgumentException('Engine is required for calcTranslationKey().');
        }

        return hash('xxh3', $hash."\n".$loc."\n".$engine);
    }

    public static function normalizeLocale(string $locale): string
    {
        // normalize to a consistent, low-entropy representation (e.g. "es", "pt-br")
        $locale = strtolower(trim($locale));
        $locale = str_replace('_', '-', $locale);

        // collapse repeated separators
        $locale = preg_replace('/-+/', '-', $locale) ?? $locale;

        return $locale;
    }

    public static function normalizeEngine(string $engine): string
    {
        // normalize engine identifiers; keep short and stable
        $engine = strtolower(trim($engine));

        // optional aliases (keep minimal; add deliberately)
        return match ($engine) {
            'libretranslate', 'libre-translate' => 'libre',
            default => $engine,
        };
    }

    private static function normalizeText(string $text): string
    {
        // normalize newlines and trim but keep internal spacing intact
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        return trim($text);
    }
}
