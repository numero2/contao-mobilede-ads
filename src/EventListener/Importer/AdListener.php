<?php

/**
 * mobile.de Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL
 * @copyright Copyright (c) 2021, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\MobileDeAdsBundle\EventListener\Importer;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\System;
use Doctrine\DBAL\Connection;
use numero2\MobileDeAdsBundle\AdModel;
use SimpleXMLElement;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpOptions;


class AdListener {


    /**
     * @var string
     */
    const API_SEARCH_ENDPOINT = "https://services.mobile.de/search-api/";

    /**
     * @var string
     */
    const API_SELLER_ENDPOINT = "https://services.mobile.de/seller-api/";

    /**
     * @var Contao\CoreBundle\Framework\ContaoFramework
     */
    private $framework;

    /**
     * @var Doctrine\DBAL\Connection
     */
    private $connection;


    public function __construct( ContaoFramework $framework, Connection $connection, string $username, string $password ) {

        $this->framework = $framework;
        $this->connection = $connection;
        $this->username = $username;
        $this->password = $password;

        if( empty($this->username) || empty($this->username) ) {
            throw new InvalidArgumentException('Missing credentials for mobile.de API, please configure in global parameters.yml.');
        }
    }


    /**
     * Gets a list of all available ads
     *
     * @return array
     */
    public function getAvailableAds( $size=100 ): array {

        $aParams = [];
        $aParams['page.size'] = $size ? $size : 100;
        $aParams['sort.field'] = 'makeModel';
        $aParams['sort.order'] = 'ASCENDING';

        $oOptions = new HttpOptions();
        $oOptions->setAuthBasic($this->username, $this->password);
        $oOptions->setHeaders(['Accept-Language'=>'de']);

        $oClient = HttpClient::create();
        $aOptions = $oOptions->toArray();

        $aResults = [];

        $maxPages = 1;

        for( $page = 1; $page <= $maxPages; $page++ ) {

            $aParams['page.number'] = $page;
            $url = self::API_SEARCH_ENDPOINT . 'search?'.str_replace("&amp;",'&',http_build_query($aParams));
            $oResponse = $oClient->request('GET', $url, $aOptions);

            $data = [];
            if( $oResponse->getStatusCode() === 200 ) {
                $data = self::parseNamespacedXML($oResponse->getContent());

                if( !empty($data['ads']['ad']) ) {
                    $aResults = array_merge($aResults, $data['ads']['ad']);
                }
            }

            $maxPages = $data['max-pages'];
        }

        return $aResults;
    }


    /**
     * Returns all details for the given ad
     *
     * @param string $id
     *
     * @return array
     *
     * @see https://services.mobile.de/schema/ad-1.0.xsd
     */
    private function getAdDetails( string $id ): array {

        $oOptions = new HttpOptions();
        $oOptions->setAuthBasic($this->username, $this->password);
        $oOptions->setHeaders(['Accept-Language'=>'de']);

        $oClient = HttpClient::create();
        $aOptions = $oOptions->toArray();

        $url = self::API_SEARCH_ENDPOINT . 'ad/'.$id;
        $oResponse = $oClient->request('GET', $url, $aOptions);

        $data = [];

        if( $oResponse->getStatusCode() === 200 ) {
            $data = self::parseNamespacedXML($oResponse->getContent());
        }

        return $data;
    }


    /**
     * Returns all details for the given seller id and ad id
     *
     * @param string $sellerId
     * @param string $adId
     *
     * @return array
     *
     * https://services.mobile.de/docs/seller-api.html
     */
    private function getAdDetailsForSeller( string $sellerId, string $adId ): array {

        // As we don't have access to this endpoint, we could not yet implement this
        return [];
        $oOptions = new HttpOptions();
        $oOptions->setAuthBasic($this->username, $this->password);
        // $oOptions->setHeaders(['Accept-Language'=>'de', 'Accept' => 'application/vnd.de.mobile.api+json']);
        $oOptions->setHeaders(['Accept' => 'application/vnd.de.mobile.api+json']);

        $oClient = HttpClient::create();
        $aOptions = $oOptions->toArray();

        $url = self::API_SELLER_ENDPOINT . 'sellers/';//.$sellerId.'/ads/'.$adId;
        $oResponse = $oClient->request('GET', $url, $aOptions);
        // echo $url ."\n";
        $data = [];

        if( $oResponse->getStatusCode() === 200 ) {
            $data = $oResponse->getContent();
            // $data = self::parseNamespacedXML($oResponse->getContent());
            // echo print_r($data,1)."\n";
        }
        // echo print_r($oResponse,1)."\n";
        // echo $oResponse->getStatusCode()."\n";

        return $data;
    }


    /**
     * Parses a namespaced XML to an array
     *
     * @param string The namespaced XML document
     *
     * @return array
     */
    private function parseNamespacedXML( string $xml ): array {

        // fix add casting to array
        $xml = str_replace(' xml-lang="de"', '', $xml);

        // Get list of all namespaces used in document
        $sxe = new SimpleXMLElement($xml);
        $namespaces = $sxe->getNamespaces(true);

        // This is part of a regex I will use to remove the namespace declaration from string
        $nameSpaceDefRegEx = '(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?';

        foreach( array_keys($namespaces) as $remove ) {

            // First remove the namespace from the opening of the tag
            $xml = str_replace('<' . $remove . ':', '<', $xml);
            // Now remove the namespace from the closing of the tag
            $xml = str_replace('</' . $remove . ':', '</', $xml);
            // Complete the pattern for RegEx to remove this namespace declaration
            $pattern = "/xmlns:{$remove}{$nameSpaceDefRegEx}/";
            // Remove the actual namespace declaration using the Pattern
            $xml = preg_replace($pattern, '', $xml, 1);
        }

        return json_decode(json_encode(simplexml_load_string($xml)), true);
    }


    /**
     * Finds the main image for the given ad
     *
     * @param array adImage
     *
     * @return string
     **/
    private function getLargestImage( $adImage ): string {

        $aImages = [];

        if( !empty($adImage['representation']) ) {

            foreach( $adImage['representation'] as $im ) {
                $aImages[$im['@attributes']['size']] = $im['@attributes']['url'];
            }
        }

        if( !empty($aImages['XL']) ) {
            return $aImages['XL'];
        }
        if( !empty($aImages['L']) ) {
            return $aImages['L'];
        }
        if( !empty($aImages['M']) ) {
            return $aImages['M'];
        }

        return '';
    }


    /**
     * Checks if the given attribute is true
     *
     * @param array $adAttribute
     *
     * @return boolean
     */
    private function attributeIsTrue( $adAttribute=[] ): bool {

        if( !is_array($adAttribute) ) {
            $adAttribute = (array)$adAttribute;
        }

        return !empty($adAttribute['@attributes']['value']) && $adAttribute['@attributes']['value'] === 'true';
    }


    /**
     * Parse the structure from specifics part
     *
     * @param array|string $adAttribute
     * @param boolean $isArray
     *
     * @return array|string
     */
    private function getValueFromSpecifics( $adAttribute, bool $isArray=false ) {

        if( $adAttribute === null ) {
            return '';
        }
        if( is_string($adAttribute) ) {
            return $adAttribute;
        }

        if( !$isArray ) {
            if ( !empty($adAttribute['local-description']) ) {
                return $adAttribute['local-description'];
            }
            if ( !empty($adAttribute['@attributes']['value']) ) {
                return $adAttribute['@attributes']['value'];
            }
        } else {

            $aResult = [];
            $aArr = $adAttribute[array_keys($adAttribute)[0]];

            foreach( $aArr as $item ) {
                if( !empty($item['local-description']) ) {
                    $aResult[] = $item['local-description'];
                }
            }

            return $aResult;
        }

        return '';
    }


    /**
     * Import all available ads
     *
     * @return bool
     */
    public function importAds(): bool {

        $this->framework->initialize();

        // unpublish all ads, all imports will be published again
        $this->unpublishAll();

        $aAds = $this->getAvailableAds();

        if( !empty($aAds) ) {

            foreach( $aAds as $aAd ) {
                $this->importAd($aAd);
            }

            System::log(sprintf('Imported %d ads from mobile.de', count($aAds)), __METHOD__, TL_CRON);

            return true;
        }

        System::log('Could not find any ads to import from mobile.de', __METHOD__, TL_ERROR);

        return false;
    }


    /**
     * import the given ad array
     *
     * @param array $ad
     *
     * @return int inserted id
     */
    private function importAd( $ad ): int {

        $aAd = [
            'tstamp'                    => time()
        ,   'mobile_id'                 => $ad['@attributes']['key']
        ,   'seller_id'                 => $ad['seller']['@attributes']['key']
        ,   'creation'                  => strtotime($ad['creation-date']['@attributes']['value'])
        ,   'modification'              => strtotime($ad['modification-date']['@attributes']['value'])
        ,   'url'                       => $ad['detail-page']['@attributes']['url']
        ,   'class'                     => $ad['vehicle']['class']['local-description']
        ,   'category'                  => $ad['vehicle']['category']['local-description']
        ,   'make'                      => $ad['vehicle']['make']['local-description']
        ,   'model'                     => $ad['vehicle']['model']['local-description'] ?? ''
        ,   'model_description'         => $ad['vehicle']['model-description']['@attributes']['value']
        ,   'damage_and_unrepaired'     => ($this->attributeIsTrue($ad['vehicle']['damage-and-unrepaired'])) ? '1' : ''
        ,   'accident_damaged'          => ($this->attributeIsTrue($ad['vehicle']['accident-damaged'])) ? '1' : ''
        ,   'roadworthy'                => ($this->attributeIsTrue($ad['vehicle']['roadworthy'])) ? '1' : ''
        ,   'features'                  => array_column($ad['vehicle']['features']['feature'], 'local-description')
        ,   'exterior_color'            => $this->getValueFromSpecifics($ad['vehicle']['specifics']['exterior-color'])
        ,   'exterior_color_metalic'    => ($this->attributeIsTrue($ad['vehicle']['specifics']['exterior-color']['metalic'])) ? '1' : ''
        ,   'exterior_color_name'       => $this->getValueFromSpecifics($ad['vehicle']['specifics']['exterior-color']['manufacturer-color-name'])
        ,   'mileage'                   => $this->getValueFromSpecifics($ad['vehicle']['specifics']['mileage'])
        ,   'general_inspection'        => $this->getValueFromSpecifics($ad['vehicle']['specifics']['general-inspection'])
        ,   'door_count'                => $this->getValueFromSpecifics($ad['vehicle']['specifics']['door-count'])
        ,   'first_registration'        => $this->getValueFromSpecifics($ad['vehicle']['specifics']['first-registration'])
        ,   'emission_class'            => $this->getValueFromSpecifics($ad['vehicle']['specifics']['emission-class'])
        ,   'efc_envkv_compliant'       => ((bool)$ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['envkv-compliant']) ? '1' : ''
        ,   'efc_energy_efficiency_class' => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['energy-efficiency-class'] ?? ''
        ,   'efc_co2_emission'          => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['co2-emission'] ?? ''
        ,   'efc_inner'                 => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['inner'] ?? ''
        ,   'efc_outer'                 => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['outer'] ?? ''
        ,   'efc_combined'              => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['combined'] ?? ''
        ,   'efc_unit'                  => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['unit'] ?? ''
        ,   'efc_petrol_type'           => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['petrol-type'] ?? ''
        ,   'efc_combined_power_consumption' => $ad['vehicle']['specifics']['emission-fuel-consumption']['@attributes']['combined-power-consumption'] ?? ''
        ,   'emission_sticker'          => $this->getValueFromSpecifics($ad['vehicle']['specifics']['emission-sticker'])
        ,   'fuel'                      => $this->getValueFromSpecifics($ad['vehicle']['specifics']['fuel'])
        ,   'wltp_consumption_fuel_combined' => $ad['vehicle']['specifics']['wltp-values']['@attributes']['consumption-fuel-combined'] ?? ''
        ,   'wltp_co2_emission_combined' => $ad['vehicle']['specifics']['wltp-values']['@attributes']['co2-emission-combined'] ?? ''
        ,   'wltp_consumption_power_combined' => $ad['vehicle']['specifics']['wltp-values']['@attributes']['consumption-power-combined'] ?? ''
        ,   'wltp_electric_range'       => $ad['vehicle']['specifics']['wltp-values']['@attributes']['electric-range'] ?? ''
        ,   'wltp_consumption_fuel_combined_weighted' => $ad['vehicle']['specifics']['wltp-values']['@attributes']['consumption-fuel-combined-weighted'] ?? ''
        ,   'wltp_consumption_power_combined_weighted' => $ad['vehicle']['specifics']['wltp-values']['@attributes']['consumption-power-combined-weighted'] ?? ''
        ,   'wltp_co2_emission_combined_weighted' => $ad['vehicle']['specifics']['wltp-values']['@attributes']['co2-emission-combined-weighted'] ?? ''
        ,   'power'                     => $this->getValueFromSpecifics($ad['vehicle']['specifics']['power'])
        ,   'gearbox'                   => $this->getValueFromSpecifics($ad['vehicle']['specifics']['gearbox'])
        ,   'climatisation'             => $this->getValueFromSpecifics($ad['vehicle']['specifics']['climatisation'])
        ,   'num_seats'                 => $this->getValueFromSpecifics($ad['vehicle']['specifics']['num-seats'])
        ,   'cubic_capacity'            => $this->getValueFromSpecifics($ad['vehicle']['specifics']['cubic-capacity'])
        ,   'condition'                 => $this->getValueFromSpecifics($ad['vehicle']['specifics']['condition'])
        ,   'interior_color'            => $this->getValueFromSpecifics($ad['vehicle']['specifics']['interior-color'])
        ,   'interior_type'             => $this->getValueFromSpecifics($ad['vehicle']['specifics']['interior-type'])
        ,   'airbag'                    => $this->getValueFromSpecifics($ad['vehicle']['specifics']['airbag'])
        ,   'number_of_previous_owners' => $this->getValueFromSpecifics($ad['vehicle']['specifics']['number-of-previous-owners'])
        ,   'speed_control'             => $this->getValueFromSpecifics($ad['vehicle']['specifics']['speed-control'])
        ,   'radio'                     => $this->getValueFromSpecifics($ad['vehicle']['specifics']['radio'], true)
        ,   'daytime_running_lamps'     => $this->getValueFromSpecifics($ad['vehicle']['specifics']['daytime-running-lamps'])
        ,   'headlight_type'            => $this->getValueFromSpecifics($ad['vehicle']['specifics']['headlight-type'])
        ,   'highlights'                => is_array($ad['highlights']['highlight']) ? array_values($ad['highlights']['highlight']) : []
        ,   'image'                     => $this->getLargestImage($ad['images']['image'])
        ,   'price'                     => $ad['price']['consumer-price-amount']['@attributes']['value']
        ,   'price_type'                => strtolower($ad['price']['@attributes']['type'])
        ,   'currency'                  => strtolower($ad['price']['@attributes']['currency'])
        ,   'vatable'                   => ($this->attributeIsTrue($ad['price']['vatable'])) ? '1' : ''
        ,   'vat_rate'                  => $ad['price']['vat-rate']['@attributes']['value'] ?? ''
        ,   'published'                 => '1'
        ];

        // $aAdDetailsSeller = $this->getAdDetailsForSeller($aAd['seller_id'], $aAd['mobile_id']);

        $oAd = AdModel::findOneBy(['mobile_id=?'], [$aAd['mobile_id']]);

        if( $oAd && $aAd['modification'] == $oAd->modification ) {

            $oAd->published = '1';
            $oAd->save();

            return $oAd->id;
        }

        // use xml api
        $aAdDetails = $this->getAdDetails($aAd['mobile_id']);
        $aAd['description'] = $aAdDetails['description'];
        $aAd['description_enriched'] = $aAdDetails['enrichedDescription'];
        $aAd['images'] = $aAdDetails['images']['image'];

        foreach( $aAd['images'] as $key => $image) {
            $aAd['images'][$key] = $this->getLargestImage($image);
        }

        $aAd['exterior_color_name'] = html_entity_decode($aAd['exterior_color_name']);

        if( !$oAd ) {

            $oAd = new AdModel();
            $oAd->setRow($aAd);

        } else {

            foreach( $aAd as $field => $value ) {
                $oAd->{$field} = $value;
            }
        }

        if( $oAd->save() ) {
            return $oAd->id;
        }

        return 0;
    }


    /**
     * Truncate the table
     */
    public function truncateTable(): void {
        $this->connection->query("TRUNCATE ".AdModel::getTable());
    }


    /**
     * Set all ads to "unpublished"
     */
    public function unpublishAll(): void {
        $this->connection->query("UPDATE ".AdModel::getTable()." SET published=''");
    }
}
