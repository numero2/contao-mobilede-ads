<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


$GLOBALS['TL_DCA']['tl_mobile_ad'] = [

    'config' => [
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ]
,   'fields' => [
        'id' => [
            'sql'           => "int(10) unsigned NOT NULL auto_increment"
        ]
    ,   'tstamp' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'mobile_id' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'creation' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'modification' => [
            'sql'           => "int(10) unsigned NOT NULL default '0'"
        ]
    ,   'url' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'class' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'category' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'make' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'model' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'model_description' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'damage_and_unrepaired' => [
            'sql'           => "char(1) NOT NULL default ''"
        ]
    ,   'accident_damaged' => [
            'sql'           => "char(1) NOT NULL default ''"
        ]
    ,   'roadworthy' => [
            'sql'           => "char(1) NOT NULL default ''"
        ]
    ,   'features' => [
            'sql'           => "blob NULL"
        ]
    ,   'exterior_color' => [
            'sql'           => "varchar(64) NOT NULL default ''"
        ]
    ,   'exterior_color_metalic' => [
            'sql'           => "char(1) NOT NULL default ''"
        ]
    ,   'exterior_color_name' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'mileage' => [
            'sql'           => "varchar(16) NOT NULL default ''"
        ]
    ,   'general_inspection' => [
            'sql'           => "varchar(16) NOT NULL default ''"
        ]
    ,   'door_count' => [
            'sql'           => "varchar(16) NOT NULL default ''"
        ]
    ,   'first_registration' => [
            'sql'           => "varchar(16) NOT NULL default ''"
        ]
    ,   'emission_class' => [
            'sql'           => "varchar(64) NOT NULL default ''"
        ]
    ,   'emission_sticker' => [
            'sql'           => "varchar(64) NOT NULL default ''"
        ]
    ,   'fuel' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'power' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'gearbox' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'climatisation' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'num_seats' => [
            'sql'           => "varchar(8) NOT NULL default ''"
        ]
    ,   'cubic_capacity' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'condition' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'interior_color' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'interior_type' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'airbag' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'number_of_previous_owners' => [
            'sql'           => "varchar(8) NOT NULL default ''"
        ]
    ,   'speed_control' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'radio' => [
            'sql'           => "blob NULL"
        ]
    ,   'daytime_running_lamps' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'headlight_type' => [
            'sql'           => "varchar(128) NOT NULL default ''"
        ]
    ,   'highlights' => [
            'sql'           => "blob NULL"
        ]
    ,   'description' => [
            'sql'           => "blob NULL"
        ]
    ,   'image' => [
            'sql'           => "varchar(255) NOT NULL default ''"
        ]
    ,   'images' => [
            'sql'           => "blob NULL"
        ]
    ,   'price' => [
            'sql'           => "varchar(16) NOT NULL default ''"
        ]
    ,   'price_type' => [
            'sql'           => "varchar(32) NOT NULL default ''"
        ]
    ,   'currency' => [
            'sql'           => "varchar(8) NOT NULL default ''"
        ]
    ,   'vatable' => [
            'sql'           => "char(1) NOT NULL default ''"
        ]
    ,   'vat_rate' => [
            'sql'           => "varchar(8) NOT NULL default ''"
        ]
    ,   'published' => [
            'sql'           => "char(1) NOT NULL default ''"
        ]
    ]
];
