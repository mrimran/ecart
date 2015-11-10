<?php

/**
 * Description of ProductFeed
 *
 * @author imran
 */
require_once __DIR__.'/../abstract.php';
require_once __DIR__.'/../../lib/Varien/Io/Interface.php';
require_once __DIR__.'/../../lib/Varien/Io/Abstract.php';
require_once __DIR__.'/../../lib/Varien/Io/File.php';

class WTS_ProductFeedSouqXml extends Mage_Shell_Abstract
{
    protected $io;
    protected $path;//project root directory
    protected $fileName = "souqmobi.xml";
    
    public function __construct()
    {
        $this->path = __DIR__.'/../../';
        $this->io = new Varien_Io_File();
        parent::__construct();
    }

    public function run()
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')
                                ->addAttributeToFilter('status', 1)
                                ->addAttributeToFilter('visibility', array(
                                        'neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE))
                                ->addUrlRewrite();//get rewritten url
        $productCollection->setPageSize(500);//load 500 products at a time to not reach the memory limit :)
        $pages = $productCollection->getLastPageNumber();
        //open target file
        $this->io->open(array('path' => $this->path));
        $this->io->streamOpen($this->fileName);
        $this->io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>'."\r\n<products>\r\n");
        $currentPage = 1;
        while($currentPage <= $pages) {
            print "Writing...".$currentPage."\r\n";
            //get product from collections using foreach
            foreach($productCollection as $product) {
                //print_r($product->getData());
                $attrMan = $product->getResource()->getAttribute("manufacturer");
                $this->io->streamWrite("<item>\r\n");
                //TODO: get brand of the product
                $this->io->streamWrite("<brand>".$attrMan->getSource()->getOptionText($product->getManufacturer())."</brand>\r\n");
                $this->io->streamWrite("<datePublished>".date("Y-m-d", strtotime($product->getUpdatedAt()))."</datePublished>\r\n");
                $this->io->streamWrite("<pname><![CDATA[".$product->getName()."]]></pname>\r\n");
                $this->io->streamWrite("<loc><![CDATA[".dirname(Mage::getBaseUrl())."/".$product->getUrlPath()."]]></loc>\r\n");
                //TODO: get published date of the product
                $this->io->streamWrite("<price>".$product->getFinalPrice()."</price>\r\n");
                $this->io->streamWrite("</item>\r\n");
                print "Writing...".$product->getName()."\r\n";
            }
            $currentPage++;
            $productCollection->clear();
            print "Writing ends...".$currentPage."\r\n";
        }
        $this->io->streamWrite('</products>');
        print "Now closing...\r\n";
        $this->io->streamClose();
    }

}

$shell = new WTS_ProductFeedSouqXml();
$shell->run();
exit();