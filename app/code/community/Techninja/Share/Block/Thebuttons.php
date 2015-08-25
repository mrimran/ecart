<?php

class Techninja_Share_Block_Thebuttons
    extends Mage_Core_Block_Template
{
    public function isEnabled()
    {
        return Mage::getStoreConfig('share/share_group/enabled');
    }
	
	public function getPubKey()
    {
        return Mage::getStoreConfig('share/share_group/pub_key');
    }
	
	public function showSTbutton()
    {
        return Mage::getStoreConfig('share/share_group/st_button');
    }
	
	public function showFBbutton()
    {
        return Mage::getStoreConfig('share/share_group/fb_button');
    }
	
	public function showTweetbutton()
    {
        return Mage::getStoreConfig('share/share_group/tweet_button');
    }
	
	public function showLIbutton()
    {
        return Mage::getStoreConfig('share/share_group/li_button');
    }
	
	public function showPinbutton()
    {
        return Mage::getStoreConfig('share/share_group/pin_button');
    }
	
	public function showEmailbutton()
    {
        return Mage::getStoreConfig('share/share_group/email_button');
    }
}