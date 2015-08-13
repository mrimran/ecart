<?php

class MW_FollowUpEmail_Block_Itemsproductorder extends Mage_Sales_Block_Items_Abstract

{

	protected $_order = null;

	

	protected function _construct()

    {

        parent::_construct();

        $this->setTemplate('mw_followupemail/itemsproductorder.phtml');

    }

	

	public function setOrder($order)

	{

		$this->_order = $order;

	}

	

	



	public function getOrder()

	{

		$order = $this->_order;

		return $order;

	}

	

	/**

     * Retrieve order items collection

     *

     * @return unknown

     */

    public function getItemsCollection()

    {

        return $this->getOrder()->getItemsCollection();

    }

}