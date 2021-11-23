<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\MobileDeAdsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;


class MobileDeAdsExtension extends Extension {


    /**
     * {@inheritdoc}
     */
    public function load(array $mergedConfig, ContainerBuilder $container): void {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('commands.yml');
        $loader->load('listener.yml');
        $loader->load('services.yml');

        $params = Yaml::parseFile(__DIR__.'/../Resources/config/parameters.yml');

        foreach( $params['parameters'] as $key => $value ) {
            if( $container->hasParameter($key) ) {
                $container->setParameter('mobile.'.$key, $container->getParameter($key));
            } else {
                $container->setParameter('mobile.'.$key, $value);
            }
        }
    }
}
