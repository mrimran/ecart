<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Block_Adminhtml_Settings_DownloadNImport extends Amasty_Geoip_Block_Adminhtml_Settings_Import
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $onclick = 'var inputCaller = this;';

        $importTypes = array(
            'location',
            'block'
        );

        foreach ($importTypes as $type) {
            $startDownloadingUrl = $this->getUrl('amgeoip/adminhtml_import/startDownloading', array(
                'type' => $type,
                'action' => 'download_and_import'
            ))
            ;

            $startUrl = $this->getUrl('amgeoip/adminhtml_import/start', array(
                'type' => $type,
                'action' => 'download_and_import'
            ))
            ;

            $processUrl = $this->getUrl('amgeoip/adminhtml_import/process', array(
                'type' => $type,
                'action' => 'download_and_import'
            ))
            ;

            $commitUrl = $this->getUrl('amgeoip/adminhtml_import/commit', array(
                'type' => $type,
                'action' => 'download_and_import',
                'is_download' => true,
            ))
            ;

            $onclick .= 'window.setTimeout(function(){ amImportObj.runDownloading(\'' . $startUrl . '\', \'' . $processUrl . '\', \'' . $commitUrl . '\', \'' . $startDownloadingUrl . '\',  inputCaller);}, 100);

             ';
        }

        if (Mage::getModel('amgeoip/import')->isDone()) {
            $width = 100;
            $completedClass = "end_downloading_completed";
            $importedClass = "end_imported";
            $importDate = Mage::getStoreConfig('amgeoip/import/date_download');
            if (!empty($importDate))
                $importDate = $this->__('Last Imported: ') . $importDate;
        } else {
            $width = 0;
            $completedClass = "end_downloading_not_completed";
            $importedClass = "end_not_imported";
            $importDate = '';
        }

        $commmentBlock = $this->getLayout()->createBlock('core/template')->setTemplate('amgeoip/download_n_import.phtml');
        $commmentBlock
            ->setWidth($width)
            ->setCompletedClass($completedClass)
            ->setImportedClass($importedClass)
            ->setImportDate($importDate)
        ;
        $element->setComment($commmentBlock->toHtml());

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel($this->__('Download and Import'))
            ->setOnClick($onclick)
            ->toHtml()
        ;

        return $html;
    }
}
