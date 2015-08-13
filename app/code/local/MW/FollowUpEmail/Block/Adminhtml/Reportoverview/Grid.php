<?php
/**
 * Created by PhpStorm.
 * User: kz
 * Date: 12/15/14
 * Time: 10:14 AM
 */

class MW_FollowUpEmail_Block_Adminhtml_Reportoverview_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_followupemail/report/overview.phtml');
    }
}