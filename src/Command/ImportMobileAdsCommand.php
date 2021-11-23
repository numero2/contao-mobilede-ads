<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\MobileDeAdsBundle\Command;

use numero2\MobileDeAdsBundle\EventListener\Importer\AdListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ImportMobileAdsCommand extends Command {


    protected static $defaultName = 'contao:mobileads:import';

    /**
     * @var AdListener
     */
    private $adListener;


    public function __construct( AdListener $adListener ) {

        $this->adListener = $adListener;

        parent::__construct();
    }


    protected function configure(): void {
        $this
            ->setDescription('Peforms an import or update of your mobile.de ads')
        ;
    }


    protected function execute( InputInterface $input, OutputInterface $output ): int {

        $io = new SymfonyStyle($input, $output);

        if( $this->adListener->importAds() ) {
            $io->success('Import successfully finished.');
            return 0;
        } else {
            $io->error('No ads found to import (see log).');
            return 1;
        }
    }
}
