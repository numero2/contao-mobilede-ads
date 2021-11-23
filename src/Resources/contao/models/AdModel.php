<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\MobileDeAdsBundle;

use Contao\Model;


class AdModel extends Model {


    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_mobile_ad';

}
