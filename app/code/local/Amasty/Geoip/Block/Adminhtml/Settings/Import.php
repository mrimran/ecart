<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Block_Adminhtml_Settings_Import extends Mage_Adminhtml_Block_System_Config_Form_Field
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
            $startUrl = $this->getUrl('amgeoip/adminhtml_import/start', array(
                'type' => $type,
                'action' => 'import'
            ))
            ;

            $processUrl = $this->getUrl('amgeoip/adminhtml_import/process', array(
                'type' => $type,
                'action' => 'import'
            ))
            ;

            $commitUrl = $this->getUrl('amgeoip/adminhtml_import/commit', array(
                'type' => $type,
                'action' => 'import'
            ))
            ;

            $onclick .= 'window.setTimeout(function(){ amImportObj.run(\'' . $startUrl . '\', \'' . $processUrl . '\', \'' . $commitUrl . '\', inputCaller);}, 100); ';
        }

        $import = Mage::getSingleton('amgeoip/import');
        $importAvailable = $import->filesAvailable();
        $fileBlockPath = $import->getFilePath('block', 'import');
        $fileLocationPath = $import->getFilePath('location', 'import');

        $imageAvailablePath = $this->getSkinUrl('images/amgeoip/accept.png');
        $imageUnavailablePath = $this->getSkinUrl('images/amgeoip/delete.png');

        if ($import->isFileExist($fileBlockPath)) {
            $blockImagePath = $imageAvailablePath;
        } else {
            $blockImagePath = $imageUnavailablePath;
        }

        if ($import->isFileExist($fileLocationPath)) {
            $locationImagePath = $imageAvailablePath;
        } else {
            $locationImagePath = $imageUnavailablePath;
        }

        if (Mage::getModel('amgeoip/import')->isDone()) {
            $width = 100;
            $importedClass = 'end_imported';
            $importDate = $this->__('Last Imported: ') . Mage::getStoreConfig('amgeoip/import/date');
        } else {
            $width = 0;
            $importedClass = 'end_not_imported';
            $importDate = $this->__('');
        }

        $commmentBlock = $this->getLayout()->createBlock('core/template')->setTemplate('amgeoip/import.phtml');
        $commmentBlock
            ->setWidth($width)
            ->setImportedClass($importedClass)
            ->setBlockImagePath($blockImagePath)
            ->setLocationImagePath($locationImagePath)
            ->setImportDate($importDate)
        ;
        $element->setComment($commmentBlock->toHtml());

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel($this->__('Import'))
            ->setOnClick($onclick)
            ->setDisabled(!$importAvailable)
            ->toHtml()
        ;

        return $html;
    }
}
