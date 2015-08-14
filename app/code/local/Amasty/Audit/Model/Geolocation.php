<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */
class Amasty_Audit_Model_Geolocation extends Varien_Object
{
    public function getLocation($ip)
    {
        $geoIpModel = Mage::getModel('amgeoip/geolocation');
        $location = $geoIpModel->locate($ip);
        $countryCode = $location->getCountry();
        $countryLabel = ' ';
        $cityLabel = '';
        $countryId = '';
        $locationString = '';
        if ($countryCode) {
            $country_name = Mage::app()->getLocale()->getCountryTranslation($countryCode);
            $countryLabel = $country_name ? $country_name : $countryCode;
            $countryId = $countryCode;
            $cityLabel = $location->getCity();
            $locationString = $countryLabel;
            if ($cityLabel != '') {
                $locationString = $locationString . ', ' . $cityLabel;
            }
        }

        return array('locationString' => $locationString, 'countryId' => $countryId, 'countryLabel' => $countryLabel);
    }
}
