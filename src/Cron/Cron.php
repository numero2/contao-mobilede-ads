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


/**
 * @CronJob("daily")
 */
class Cron {


    private $framework;


    public function __construct( ContaoFramework $framework ) {
        $this->framework = $framework;
    }


    /**
     * {@inheritdoc}
     */
    public function __invoke( string $scope ): void {

        $this->framework->initialize();
    }
}
