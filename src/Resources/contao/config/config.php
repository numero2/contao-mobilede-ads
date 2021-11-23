<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


use numero2\MobileDeAdsBundle\AdModel;


/**
 * MODELS
 */
$GLOBALS['TL_MODELS'][AdModel::getTable()] = AdModel::class;


/**
 * PURGE JOBS
 */
$GLOBALS['TL_PURGE']['tables']['mobile_ads'] = [
    'callback' => ['numero2_mobile.listener.import.ad', 'importAds']
,   'affected' => ['tl_mobile_ad']
];
