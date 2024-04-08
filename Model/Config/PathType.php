<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Model\Config;

use Magento\Framework\Phrase;

enum PathType: string
{
    private const STORE_CODE = 'store_code';
    private const COUNTRY_CODE = 'country_code';
    private const LANGUAGE_CODE = 'language_code';
    private const LOCALE_UNDERSCORE = 'locale_underscore';
    private const LOCALE_HYPHEN = 'locale_hyphen';
    private const CUSTOM = 'custom';
    
    private const PHRASES = [
        self::STORE_CODE => 'Store Code',
        self::COUNTRY_CODE => 'Country Code',
        self::LANGUAGE_CODE => 'Language Code',
        self::LOCALE_UNDERSCORE => 'Locale Code (_)',
        self::LOCALE_HYPHEN => 'Locale Code (-)',
        self::CUSTOM => 'Custom',
    ];

    case StoreCode = self::STORE_CODE;
    case CountryCode = self::COUNTRY_CODE;
    case LanguageCode = self::LANGUAGE_CODE;
    case LocaleUnderscore = self::LOCALE_UNDERSCORE;
    case LocaleHyphen = self::LOCALE_HYPHEN;
    case Custom = self::CUSTOM;

    public function getLabel(): Phrase
    {
        return new Phrase(self::PHRASES[$this->value]);
    }
}
