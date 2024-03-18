<?php
/**
 * Copyright © OpenGento, All rights reserved.
 */
declare(strict_types=1);

namespace Opengento\StorePathUrl\Model\Config;

use Magento\Framework\Phrase;

enum PathType: string
{
    private const PHRASES = [
        self::StoreCode->value => 'Store Code',
        self::CountryCode->value => 'Country Code',
        self::LanguageCode->value => 'Language Code',
        self::LocaleUnderscore->value => 'Locale Code (_)',
        self::LocaleHyphen->value => 'Locale Code (-)',
        self::Custom->value => 'Custom',
    ];

    case StoreCode = 'store_code';
    case CountryCode = 'country_code';
    case LanguageCode = 'language_code';
    case LocaleUnderscore = 'locale_underscore';
    case LocaleHyphen = 'locale_hyphen';
    case Custom = 'custom';

    public function getLabel(): Phrase
    {
        return new Phrase(self::PHRASES[$this->value]);
    }
}
