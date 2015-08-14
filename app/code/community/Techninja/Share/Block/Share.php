<?php
class Techninja_Share_Block_Share
    extends Mage_Core_Block_Abstract
    implements Mage_Widget_Block_Interface
{

    /**
     * 
     *
     * @return string
     */
    protected function _toHtml()
    {
        $pageTitle = '';
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $pageTitle = $headBlock->getTitle();
        }

if(Mage::getStoreConfig('share/share_group/button_size') == "large")
	$size = "_large";
else
	$size = "";
		

if(Mage::getStoreConfig('share/share_group/st_button'))
{
	$html .= "<span class='st_sharethis".$size."' displayText='ShareThis'></span>";
}
if(Mage::getStoreConfig('share/share_group/fb_button'))
{
	$html .= "<span class='st_facebook".$size."' displayText='Facebook'></span>";
}
if(Mage::getStoreConfig('share/share_group/tweet_button'))
{
	$html .= "<span class='st_twitter".$size."' displayText='Tweet'></span>";
}
if(Mage::getStoreConfig('share/share_group/li_button'))
{
	$html .= "<span class='st_linkedin".$size."' displayText='LinkedIn'></span>";
}
if(Mage::getStoreConfig('share/share_group/pin_button'))
{
	$html .= "<span class='st_pinterest".$size."' displayText='Pinterest'></span>";
}
if(Mage::getStoreConfig('share/share_group/email_button'))
{
	$html .= "<span class='st_email".$size."' displayText='Email'></span>";
}

		$html = $html . '<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "'.$this->getPubKey().'", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>';

        return $html;
    }

}

