<?php
class MW_FollowUpEmail_Block_Itemscart extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
	protected $_items = null;
	protected $_subtotal = 0;
	protected $_discount = null;
	protected $_grandtotal = null;
	
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mw_followupemail/itemscart.phtml');
    }
	
	public function setItems($items)
	{
		$this->_items = $items;
	}
	
	public function setSubtotal($cartSubtotal)
	{
		$this->_subtotal = $cartSubtotal;
	}
	
	public function setDiscount($discount)
	{
		$this->_discount = $discount;
	}
	
	public function setGrandTotal($cartgrand_total)
	{
		$this->_grandtotal = $cartgrand_total;
	}
	
	public function getSubtotal()
	{
		return $this->_subtotal;
	}
	
	public function getDiscount()
	{
		return $this->_discount;
	}
	
	public function getGrandTotal()
	{
		return $this->_grandtotal;
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