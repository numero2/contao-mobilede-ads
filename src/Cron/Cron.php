<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\MobileDeAdsBundle\Cron;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use numero2\MobileDeAdsBundle\EventListener\Importer\AdListener;
use Contao\CoreBundle\ServiceAnnotation\CronJob;


/**
 * @CronJob("daily")
 */
class Cron {


    /**
     * @var ContaoFramework
     */
    private $framework;

    /**
     * @var AdListener
     */
    private $adListener;


    public function __construct( ContaoFramework $framework, AdListener $adListener ) {

        $this->framework = $framework;
        $this->adListener = $adListener;
    }


    /**
     * {@inheritdoc}
     */
    public function __invoke( string $scope ): void {

        $this->framework->initialize();

        /** @var System $system */
        $system = $this->framework->getAdapter(System::class);

        $system->log('Daily cron for importing ads from mobile.de started', __METHOD__, TL_CRON);

        if( $this->adListener->importAds() ) {
            $system->log('Import successfully finished.', __METHOD__, TL_CRON);
        } else {
            $system->log('No ads found to import.', __METHOD__, TL_CRON);
        }
    }
}
