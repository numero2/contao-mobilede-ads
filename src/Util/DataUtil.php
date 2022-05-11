<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\MobileDeAdsBundle\Util;

use softark\creole\Creole;


class DataUtil {


    /**
     * Converts kW to PS
     *
     * @param float kw
     *
     * @return float
     **/
    public static function convertKwToPS( float $kw ): float {

        if( empty($kw) )
            return 0;

        return ceil($kw*1.358620689655172);
    }


    /**
     * Converts the given text in wikisyntax to html
     *
     * @param string $text
     *
     * @return string
     **/
    public static function wikitext2Html( string $text ): string {

        $parser = new Creole();

        $text = html_entity_decode(str_replace('\\\\', "\\\\\r\n", $text));

        return $parser->parse($text);
    }
}
