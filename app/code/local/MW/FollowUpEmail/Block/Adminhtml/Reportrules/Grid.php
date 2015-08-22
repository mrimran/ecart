<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml bestsellers products report grid block
 *
 * @deprecated after 1.4.0.1
 */
class MW_FollowUpEmail_Block_Adminhtml_Reportrules_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('gridReportrules');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('followupemail/reportrules_collection');		
		return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header'    =>Mage::helper('followupemail')->__('Follow Up Email'),
            'index'     =>'title'
        ));
		
		$this->addColumn('sent', array(
            'header'    =>Mage::helper('followupemail')->__('Sent'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'sent',	
        ));

        $this->addColumn('unread', array(
            'header'    =>Mage::helper('followupemail')->__('UnRead'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'unread',
			/*'renderer' => 'MW_FollowUpEmail_Block_Adminhtml_Reportrules_Grid_Column_Unread',*/
        ));
		
		$this->addColumn('readtotal', array(
            'header'    =>Mage::helper('followupemail')->__('Read'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'readtotal',
        ));
		
		$this->addColumn('clicked', array(
            'header'    =>Mage::helper('followupemail')->__('Clicked'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'clicked',
        ));
		
		$this->addColumn('purchased', array(
            'header'    =>Mage::helper('followupemail')->__('Purchased'),
            'width'     =>'150px',
            'align'     =>'left',
            'index'     =>'purchased',
        ));

        $this->addExportType('*/*/exportRulesCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportRulesExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
