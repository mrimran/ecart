<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Geoip
 */
class Amasty_Geoip_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    protected $_geoipFiles = array(
        'block'    => 'GeoLiteCity-Blocks.csv',
        'location' => 'GeoLiteCity-Location.csv'
    );

    protected $_geoipIgnoredLines = array(
        'block'    => 2,
        'location' => 2
    );

    public function startAction()
    {
        $result = array();
        try {
            $type = $this->getRequest()->getParam('type');
            $action = $this->getRequest()->getParam('action');

            /* @var $geoIpModel Amasty_Geoip_Model_Import */
            $geoIpModel = Mage::getSingleton('amgeoip/import');
            $geoIpModel->resetDone();
            $filePath = $geoIpModel->getFilePath($type, $action);
            $ret = $geoIpModel->startProcess($type, $filePath, $this->_geoipIgnoredLines[$type]);
            $result['position'] = ceil($ret['current_row'] / $ret['rows_count'] * 100);
            $result['status'] = 'started';
            $result['file'] = $this->_geoipFiles[$type];

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function processAction()
    {
        $result = array();
        try {
            $type = $this->getRequest()->getParam('type');
            $action = $this->getRequest()->getParam('action');
            $import = Mage::getSingleton('amgeoip/import');
            $filePath = $import->getFilePath($type, $action);
            $ret = Mage::getModel('amgeoip/import')->doProcess($type, $filePath);
            $result['type'] = $type;
            $result['status'] = 'processing';
            $result['position'] = ceil($ret['current_row'] / $ret['rows_count'] * 100);

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function commitAction()
    {
        $result = array();

        try {
            /* @var $geoIpModel Amasty_Geoip_Model_Import */
            $geoIpModel = Mage::getModel('amgeoip/import');
            $type = $this->getRequest()->getParam('type');
            $isDownload = Mage::app()->getRequest()->getParam('is_download');
            $geoIpModel->commitProcess($type, $isDownload);
            $result['status'] = 'done';
            $result['full_import_done'] = $geoIpModel->isDone() ? "1" : "0";
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function startDownloadingAction()
    {
        $result = array();
        try {
            $actionType = 'download_and_import';
            $type = $this->getRequest()->getParam('type');
            $import = Mage::getSingleton('amgeoip/import');
            $url = $this->_getFileUrl($type);
            $dir = $import->getDirPath($actionType);
            $newFilePath = $import->getFilePath($type, $actionType);

            if (file_exists($newFilePath)) {
                unlink($newFilePath);
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0770, true);
            }

            $source = fopen($url, 'r');
            $metaData = stream_get_meta_data($source);
            $this->_fileSize[$type] = $metaData['unread_bytes'];
            $dest = fopen($newFilePath, 'w');
            stream_copy_to_stream($source, $dest);
            $result['status'] = 'finish_downloading';
            $result['file'] = $this->_geoipFiles[$type];

        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getFileUrl($type)
    {
        $url = '';
        if ($type == 'block') {
            $url = Mage::getStoreConfig('amgeoip/general/block_file_url');
        } elseif ($type == 'location') {
            $url = Mage::getStoreConfig('amgeoip/general/location_file_url');
        }

        return $url;
    }
}
