<?php

namespace App\Support;

use Locale;
use ResourceBundle;

class CountryList
{
    public static function options(): array
    {
        static $countries;

        if (isset($countries)) {
            return $countries;
        }

        if (! class_exists(Locale::class) || ! class_exists(ResourceBundle::class)) {
            return $countries = ['ID' => 'Indonesia'];
        }

        $countries = [];
        foreach (ResourceBundle::getLocales('') as $locale) {
            $region = Locale::getRegion($locale);
            if (strlen($region) !== 2 || ! ctype_alpha($region)) {
                continue;
            }

            $name = Locale::getDisplayRegion('en_'.$region, 'id');
            if ($name !== '' && $name !== $region) {
                $countries[$region] = $name;
            }
        }

        asort($countries, SORT_NATURAL | SORT_FLAG_CASE);

        return $countries;
    }
}
