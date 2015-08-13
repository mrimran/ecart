<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Audit
 */


class Amasty_Audit_Block_Adminhtml_Visit_Edit_Tab_History  extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amaudit/tab/history.phtml');
    }

    public function getHistory()
    {
        $log = Mage::registry('current_session_history');
        $logHistory = $this->_getHistoryByLog($log);

        return $logHistory;
    }

    protected function _getHistoryByLog($log)
    {
        $historyCollection = Mage::getModel('amaudit/visit_detail')->getCollection();
        $historyCollection->getSelect()
            ->where("session_id = ?", $log->getSessionId())
        ;

        $history = $historyCollection->getData();

        for ($i = 0; $i < count($history); $i++) {
            $history[$i]['numb'] = $i + 1;
            $history[$i]['stay_duration'] = $this->_secondsToTime($history[$i]['stay_duration']);
        }

        return $history;
    }

    protected function _secondsToTime($seconds)
    {
        $timeString = '';
        $minute = 60;
        $hour = 3600;

        $hours = floor($seconds / $hour);
        $minutes = floor(($seconds - $hour * $hours) / $minute);
        $seconds = $seconds - ($hours * $hour) - ($minutes * $minute);

        if ($hours > 0) {
            $hoursText = 'hours';
            if ($hours == 1) {
                $hoursText = 'hour';
            }
            $timeString = $timeString . ' ' . $hours . ' ' . Mage::helper('amaudit')->__($hoursText);
        }

        if ($minutes > 0) {
            $minutesText = 'minutes';
            if ($minutes == 1) {
                $minutesText = 'minute';
            }
            $timeString = $timeString . ' ' . $minutes . ' ' . Mage::helper('amaudit')->__($minutesText);
        }

        if ($seconds > 0) {
            $secondsText = 'seconds';
            if ($seconds == 1) {
                $secondsText = 'second';
            }
            $timeString = $timeString . ' ' . $seconds . ' ' . Mage::helper('amaudit')->__($secondsText);
        }

        return $timeString;
    }
}
