<?php

class MW_FollowUpEmail_Block_Itemsproductcart extends Mage_Sales_Block_Items_Abstract

{

	protected $_items = null;

	

	protected function _construct()

    {

        parent::_construct();

        $this->setTemplate('mw_followupemail/itemsproductcart.phtml');

    }

	

	public function setItems($items)

	{

		$this->_items = $items;

	}



	public function getItems()

	{

		$items = $this->_items;

		return $items;

	}	

	

	public function getItemById($item){

		return Mage::getModel("sales/quote_item")->load($item);

	}

}