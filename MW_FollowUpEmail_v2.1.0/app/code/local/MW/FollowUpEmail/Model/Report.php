<?php

class MW_FollowUpEmail_Model_Report extends Mage_Core_Model_Abstract
{
    protected $all_months = 0;
    protected $use_type = array(1, 2, 3, 4, 5, 6, 14, 8, 30, 15, 16, 12, 18, 21, 32, 25, 29, 26, 27, 19, 50, 51, 52, 53);
    protected $group_signup = array(1);
    protected $group_review = array(2);
    protected $group_order = array(3, 8, 30);
    protected $group_birthday = array(26);
    protected $group_newsletter = array(16);
    protected $group_tag = array();
    protected $group_social = array();
    protected $group_referal = array(4, 5, 6, 14);
    protected $group_other = array(25, 29, 15, 12, 18, 21, 32, 27, 19, 50, 51, 53, 52);

    public function prepareCollection($data)
    {
        $resource           = Mage::getModel('core/resource');
        //$readed_order_table = $resource->getTableName('rewardpoints/rewardpointsorder');
        //$customer_table = $resource->getTableName('rewardpoints/customer');

        if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM)
        {
            if($this->_validationDate($data) == false)
            {
                return;
            }
            /** Get all month between two dates */
            $this->all_months = $this->_get_months( $data['from'], $data['to']);
        }

        /** Query to get email send */

        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection->addFieldToFilter('status',2);
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_email_sent', 'count(queue_id)', 'total_email_sent');

        $this->_buildCollection($collection, $data,true);
        $collection_email_sent = $collection;

        /** Query to get readed */
        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        //$collection->addFieldToFilter('customer_response',2);
        $collection->addFieldToFilter('customer_response',array('in'=>array(2,3,4)));
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_email_readed','count(queue_id)','total_email_readed');

        $this->_buildCollection($collection, $data,true);
        $collection_email_readed = $collection;

    /** Query to statistic */

        /* Total email created */
        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_email', 'count(queue_id)', 'total_email');
        $this->_buildCollectionCreated($collection, $data, false);



        $collection_total_email = $collection->getData();


        /* Total email sent */
        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection->addFieldToFilter('status',2);
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_email_sent', 'count(queue_id)', 'total_email_sent');
        $this->_buildCollection($collection, $data, false);


        $collection_total_email_sent = $collection->getData();

        /* Total email readed */
        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection->addFieldToFilter('customer_response',array('in'=>array(2,3,4)));
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_email_readed', 'count(queue_id)', 'total_email_readed');
        $this->_buildCollection($collection, $data, false);

        $collection_total_email_readed = $collection->getData();

        switch($data['report_range'])
        {
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                $_time = $this->getPreviousDateTime(24);
                $start_24h_time = Mage::helper('core')->formatDate(date('Y-m-d h:i:s', $_time), 'medium', true);
                $start_24h_time = strtotime($start_24h_time);

                $start_time = array(
                    'h'   => (int)date('H', $start_24h_time),
                    'd'   => (int)date('d', $start_24h_time),
                    'm'   => (int)date('m', $start_24h_time),
                    'y'   => (int)date('Y', $start_24h_time),
                );

                $rangeDate = $this->_buildArrayDate(MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_24H, $start_time['h'], $start_time['h'] + 24, $start_time);

                $_data = $this->_buildResult($collection_email_sent, $collection_email_readed, 'hour', $rangeDate);
                $_data['report']['date_start'] = $start_time;
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                $start_time = strtotime("-6 day", strtotime("Sunday Last Week"));
                $startDay = date('d', $start_time);
                $endDay = date('d',strtotime("Sunday Last Week"));
                $rangeDate = $this->_buildArrayDate(MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_WEEK, $startDay, $rangeDate);
                $_data = $this->_buildResult($collection_email_sent, $collection_email_readed, 'day', $rangeDate);
                $_data['report']['date_start'] = array(
                    'd'   => (int)date('d', $start_time),
                    'm'   => (int)date('m', $start_time),
                    'y'   => (int)date('Y', $start_time),
                );

                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                $last_month_time = strtotime($this->_getLastMonthTime());
                $last_month = date('m', $last_month_time);
                $start_day = 1;
                $end_day = $this->_days_in_month($last_month);
                $rangeDate = $this->_buildArrayDate(MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_MONTH, $start_day, $end_day);

                $_data = $this->_buildResult($collection_email_sent, $collection_email_readed, 'day', $rangeDate);
                $_data['report']['date_start'] = array(
                    'd'   => $start_day,
                    'm'   => (int)$last_month,
                    'y'   => (int)date('Y', $last_month_time),
                    'total_day' => $end_day
                );

                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS:
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS:
                if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS)
                {
                    $last_x_day = 7;
                }
                else if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS)
                {
                    $last_x_day = 30;
                }

                $start_day = date('Y-m-d h:i:s', strtotime('-'.$last_x_day.' day', Mage::getModel('core/date')->gmtTimestamp()));
                $end_day = date('Y-m-d h:i:s', strtotime("-1 day"));

                $original_time = array(
                    'from'  => $start_day,
                    'to'    => $end_day
                );

                $rangeDate = $this->_buildArrayDate(MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM, 0, 0, $original_time);

                //$_data = $this->_buildResult($collection_email_sent, $collection_email_readed, $collection_order, 'multiday', $rangeDate, $original_time);
                $_data = $this->_buildResult($collection_email_sent, $collection_email_readed, 'multiday', $rangeDate, $original_time);

                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                $original_time = array(
                    'from'  => $data['from'],
                    'to'    => $data['to']
                );
                $rangeDate = $this->_buildArrayDate(MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM, 0, 0, $original_time);

                $_data = $this->_buildResult($collection_email_sent, $collection_email_readed, 'multiday', $rangeDate, $original_time);
                break;
        }
        $_data['title'] = Mage::helper('followupemail')->__('Email Send / Email Readed');

        $_data['template_statistics'] = $this->preapareCollectionStatistics($data);

        $_data['report_activities'] = $this->preapareCollectionPieChart($data);

        $_data['statistics']['total_email'] =  $collection_total_email[0]['total_email'];
        $_data['statistics']['total_email_sent'] =  $collection_total_email_sent[0]['total_email_sent'];
        $_data['statistics']['total_email_readed'] =  $collection_total_email_readed[0]['total_email_readed'];

        return json_encode($_data);
    }

    public function preapareCollectionStatistics($data) {
        if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM){
            if($this->_validationDate($data) == false){
                return;
            }
            $this->all_months = $this->_get_months($data['from'],$data['to']);
        }

        //getAllEmail
        $collection_email = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection_email->addFieldToFilter('main_table.status',MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_SENT);
        $this->_buildCollection($collection_email,$data,false);

        // get All Template Email
        $collection_template = Mage::getModel('core/email_template')->getCollection();

        $statistic_email = array();

        foreach($collection_template as $template){
            $statistic_email[$template->getData('template_id')]['name'] = $template->getData('template_code');
            $statistic_email[$template->getData('template_id')]['sent'] = 0;
            $statistic_email[$template->getData('template_id')]['readed'] = 0;
            $statistic_email[$template->getData('template_id')]['percent'] = 0;
        }

        foreach($collection_email as $email){

            foreach($collection_template as $template){

                if($template->getData('template_code') == $email->getData('emailtemplate_id')){
                    $statistic_email[$template->getData('template_id')]['sent'] += 1;
                    if($email->getData('customer_response') == 2 || $email->getData('customer_response') == 3 || $email->getData('customer_response') == 4){
                        $statistic_email[$template->getData('template_id')]['readed'] += 1;
                    }
                }
            }
        }

        foreach($collection_template as $template){
            if($statistic_email[$template->getData('template_id')]['sent'] != 0){
                $statistic_email[$template->getData('template_id')]['percent'] = round($statistic_email[$template->getData('template_id')]['readed']/$statistic_email[$template->getData('template_id')]['sent'] * 100,2);
            }
        }

        return json_encode($statistic_email);

    }

    public function preapareCollectionPieChart($data)
    {
        if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM)
        {
            if($this->_validationDate($data) == false)
            {
                return;
            }
            /** Get all month between two dates */
            $this->all_months = $this->_get_months( $data['from'], $data['to']);
        }

        /** Query to get total email have sent */

        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection->addFieldToFilter('main_table.status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_SENT);
        $collection->removeAllFieldsFromSelect();
        $collection->addExpressionFieldToSelect('total_email_sent', 'count(queue_id)', 'total_email_sent');
        $this->_buildCollection($collection, $data, false);

        $collection_email_sent = $collection;
        //total email sent
        $total_email_sent = $collection_email_sent->getFirstItem()->getData('total_email_sent');

        /** Query to get total email sent per rule */
        $collection = Mage::getModel('followupemail/emailqueue')->getCollection();
        $collection->addFieldToFilter('main_table.status', MW_FollowUpEmail_Model_System_Config_Status::QUEUE_STATUS_SENT);
//        $collection->removeAllFieldsFromSelect();

        $group = false;
        $this->_buildCollection($collection, $data,$group);

        $email_sent_collection = $collection;

        /* get All rule */
        $collection_rules = Mage::getModel('followupemail/rules')->getCollection();

        $array_temp = array();
        $rule_name = array();

        foreach($collection_rules as $rules){
            $array_temp[$rules->getData('rule_id')] = 0;
            $rule_name[$rules->getData('rule_id')] = $rules->getData('title');
        }

        $temp = array();
        foreach($email_sent_collection as $email_sent){
            foreach($collection_rules as $rules){
                if($email_sent->getData('rule_id') == $rules->getData('rule_id')){
                    $array_temp[$rules->getData('rule_id')] = $array_temp[$rules->getData('rule_id')] + 1;
                }
            }
        }

        $data = array();

        foreach($array_temp as $key => $value)
        {
            $percent = $value/$total_email_sent * 100;
            if($percent > 0.1)
            {
                $data[]= array(Mage::helper('followupemail')->__(ucfirst($rule_name[$key])), $percent);
            }
        }
        return json_encode($data);
    }

    public function prepareCollectionMostUserPoint()
    {
        /**
         * Get the resource model
         */
        $resource = Mage::getSingleton('core/resource');

        /**
         * Retrieve the read connection
         */
        $readConnection = $resource->getConnection('core_read');

        $query = "
                SELECT
                    `rwc`.`mw_reward_point`, `customer_id`, @curRank := @curRank + 1 AS `rank`
                FROM ".$resource->getTableName('rewardpoints/customer')." AS rwc
                LEFT JOIN ".$resource->getTableName('customer/entity')." AS ce ON rwc.customer_id = ce.entity_id, (SELECT @curRank := 0) r
                WHERE ce.entity_id > 0
                ORDER BY `mw_reward_point` DESC
                LIMIT 0, 5";

        /**
         * Execute the query and store the results in $results
         */
        $results = $readConnection->fetchAll($query);

        return $results;
    }

    //protected function _buildResult($collection_email_sent, $collection_email_readed, $collection_order, $type, $rangeDate, $original_time = null)
    protected function _buildResult($collection_email_sent, $collection_email_readed, $type, $rangeDate, $original_time = null)
    {
        $_data = array();
        try
        {
            if($type == 'multiday')
            {
                foreach($rangeDate as $year => $months)
                {
                    foreach($months as $month => $days)
                    {
                        foreach($days as $day)
                        {
                            $_data['report']['sent'][$year."-".$month."-".$day]  = array($year, $month, $day, 0);
                        }
                        foreach($days as $day)
                        {
                            $_data['report']['readed'][$year."-".$month."-".$day]  = array($year, $month, $day, 0);
                        }

                        foreach($collection_email_sent as $sent)
                        {
                            if($sent->getMonth() == $month)
                            {
                                foreach($days as $day)
                                {
                                    if($sent->getDay() == $day)
                                    {
                                        $_data['report']['sent'][$year."-".$month."-".$day]  = array($year, $month, $day, (int)$sent->getTotalEmailSent());
                                    }
                                }
                            }
                        }

                        foreach($collection_email_readed as $readed)
                        {
                            if($readed->getMonth() == $month)
                            {
                                foreach($days as $day)
                                {
                                    if($readed->getDay() == $day)
                                    {
                                        $_data['report']['readed'][$year."-".$month."-".$day]  = array($year, $month, $day, (int)$readed->getTotalEmailReaded());
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                switch($type )
                {
                    case 'hour':
                        $rangeTempDate = reset($rangeDate);
                        $i = $rangeTempDate['incr_hour'];
                        break;
                    case 'day':
                        $rangeTempDate = reset($rangeDate);
                        $i = $rangeTempDate['count_day'];
                        break;
                }

                foreach($rangeDate as $date)
                {
                    switch($type )
                    {
                        case 'hour':
                            $count = $date['native_hour'];
                            break;
                        case 'day':
                            $count = $date['native_day'];
                            break;
                    }

                    $_data['report']['sent'][$i] = 0;
                    $_data['report']['readed'][$i] = 0;
                    $_data['report']['order'][$i] = 0;


                    foreach($collection_email_sent as $sent)
                    {
                        if((int)$sent->{"get$type"}() == $count)
                        {
                            if(isset($date['day']) && $date['day'] == (int)$sent->getDay())
                            {
                                $_data['report']['sent'][$i] = (int)$sent->getTotalEmailSent();
                            }
                            else if(!isset($date['day']))
                            {
                                $_data['report']['sent'][$i] = (int)$sent->getTotalEmailSent();
                            }
                        }
                    }

                    foreach($collection_email_readed as $readed)
                    {
                        if((int)$readed->{"get$type"}() == $count)
                        {
                            if(isset($date['day']) && $date['day'] == (int)$readed->getDay())
                            {
                                $_data['report']['readed'][$i] = (int)$readed->getTotalEmailReaded();
                            }
                            else if(!isset($date['day']))
                            {
                                $_data['report']['readed'][$i] = (int)$readed->getTotalEmailReaded();
                            }
                        }
                    }

                    $i++;
                }
            }

            $_data['report']['sent'] = array_values($_data['report']['sent']);
            $_data['report']['readed'] = array_values($_data['report']['readed']);

        }
        catch(Exception $e){}

        return $_data;
    }
//    protected function _buildCollection(&$collection, $data, $group = true)
    protected function _buildCollection(&$collection, $data, $group)
    {
        switch($data['report_range'])
        {
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                /* Last 24h */
                $_hour = date('Y-m-d h:i:s', strtotime('-1 day', Mage::getModel('core/date')->gmtTimestamp()));
                $start_hour = Mage::helper('core')->formatDate($_hour, 'medium', true);
                $_hour = date('Y-m-d h:i:s', strtotime("now"));
                $end_hour = Mage::helper('core')->formatDate($_hour, 'medium', true);

                if($group != false)
                {
                    $collection->addExpressionFieldToSelect('hour', 'HOUR(CONVERT_TZ(sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\'))', 'hour');
                    $collection->addExpressionFieldToSelect('day', 'DAY(CONVERT_TZ(sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\'))', 'day');
                    $collection->getSelect()->group(array('hour'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_hour, 'to' => $end_hour, 'datetime' => true));
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                /* Last week */
                $start_day = date('Y-m-d',strtotime("-7 day", strtotime("Sunday Last Week")));
                $end_day = date('Y-m-d',strtotime("Sunday Last Week"));

                $collection->addExpressionFieldToSelect('day', 'DAY(sent_at)', 'day');
                if($group != false)
                {
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
//                var_dump((string)$collection->getSelect()); die();
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                /* Last month */
                $last_month_time = $this->_getLastMonthTime();
                $last_month = date('m', strtotime($last_month_time));
                $start_day = date('Y', strtotime($last_month_time))."-".$last_month."-1";
                $end_day = date('Y', strtotime($last_month_time))."-".$last_month."-".$this->_days_in_month($last_month);

                /** Fix bug next one day */
                $end_day = strtotime($end_day.' +1 day');
                $end_day = date('Y', $end_day)."-".date('m', $end_day)."-".date('d', $end_day);


                if($group != false)
                {

                    $collection->addExpressionFieldToSelect('day', 'DAY(sent_at)', 'day');
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS:
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS:
                /** Last X days */

                if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS)
                {
                    $last_x_day = 7;
                }
                else if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS)
                {
                    $last_x_day = 30;
                }

                $start_day = date('Y-m-d h:i:s', strtotime('-'.$last_x_day.' day', Mage::getModel('core/date')->gmtTimestamp()));
                $end_day = date('Y-m-d h:i:s', strtotime("-1 day"));

                if($group != false)
                {
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addExpressionFieldToSelect('month', 'MONTH(sent_at)', 'month');
                $collection->addExpressionFieldToSelect('day', 'DAY(sent_at)', 'day');
                $collection->addExpressionFieldToSelect('year', 'YEAR(sent_at)', 'year');

                $collection->addFieldToFilter('CONVERT_TZ(main_table.sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                /* Custom range */

                if($group != false)
                {
                    $collection->addExpressionFieldToSelect('month', 'MONTH(sent_at)', 'month');
                    $collection->addExpressionFieldToSelect('day', 'DAY(sent_at)', 'day');
                    $collection->addExpressionFieldToSelect('year', 'YEAR(sent_at)', 'year');
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.sent_at, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $data['from'], 'to' => $data['to'], 'datetime' => true));
                break;
        }
    }
    protected function _buildCollectionCreated(&$collection, $data, $group)
    {
        switch($data['report_range'])
        {
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                /* Last 24h */
                $_hour = date('Y-m-d h:i:s', strtotime('-1 day', Mage::getModel('core/date')->gmtTimestamp()));
                $start_hour = Mage::helper('core')->formatDate($_hour, 'medium', true);
                $_hour = date('Y-m-d h:i:s', strtotime("now"));
                $end_hour = Mage::helper('core')->formatDate($_hour, 'medium', true);

                if($group != false)
                {
                    $collection->addExpressionFieldToSelect('hour', 'HOUR(CONVERT_TZ(create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\'))', 'hour');
                    $collection->addExpressionFieldToSelect('day', 'DAY(CONVERT_TZ(create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\'))', 'day');
                    $collection->getSelect()->group(array('hour'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_hour, 'to' => $end_hour, 'datetime' => true));
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                /* Last week */
                $start_day = date('Y-m-d',strtotime("-7 day", strtotime("Sunday Last Week")));
                $end_day = date('Y-m-d',strtotime("Sunday Last Week"));

                $collection->addExpressionFieldToSelect('day', 'DAY(create_date)', 'day');
                if($group != false)
                {
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
//                var_dump((string)$collection->getSelect()); die();
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                /* Last month */
                $last_month_time = $this->_getLastMonthTime();
                $last_month = date('m', strtotime($last_month_time));
                $start_day = date('Y', strtotime($last_month_time))."-".$last_month."-1";
                $end_day = date('Y', strtotime($last_month_time))."-".$last_month."-".$this->_days_in_month($last_month);

                /** Fix bug next one day */
                $end_day = strtotime($end_day.' +1 day');
                $end_day = date('Y', $end_day)."-".date('m', $end_day)."-".date('d', $end_day);


                if($group != false)
                {

                    $collection->addExpressionFieldToSelect('day', 'DAY(create_date)', 'day');
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS:
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS:
                /** Last X days */

                if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_7DAYS)
                {
                    $last_x_day = 7;
                }
                else if($data['report_range'] == MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_30DAYS)
                {
                    $last_x_day = 30;
                }

                $start_day = date('Y-m-d h:i:s', strtotime('-'.$last_x_day.' day', Mage::getModel('core/date')->gmtTimestamp()));
                $end_day = date('Y-m-d h:i:s', strtotime("-1 day"));

                if($group != false)
                {
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addExpressionFieldToSelect('month', 'MONTH(create_date)', 'month');
                $collection->addExpressionFieldToSelect('day', 'DAY(create_date)', 'day');
                $collection->addExpressionFieldToSelect('year', 'YEAR(create_date)', 'year');

                $collection->addFieldToFilter('CONVERT_TZ(main_table.create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $start_day, 'to' => $end_day, 'datetime' => true));
                break;
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                /* Custom range */

                if($group != false)
                {
                    $collection->addExpressionFieldToSelect('month', 'MONTH(create_date)', 'month');
                    $collection->addExpressionFieldToSelect('day', 'DAY(create_date)', 'day');
                    $collection->addExpressionFieldToSelect('year', 'YEAR(create_date)', 'year');
                    $collection->getSelect()->group(array('day'));
                }

                $collection->addFieldToFilter('CONVERT_TZ(main_table.create_date, \'+00:00\', \'+'.$this->_calOffsetHourGMT().':00\')', array('from' => $data['from'], 'to' => $data['to'], 'datetime' => true));
                break;
        }
    }

    protected function _getLastMonthTime()
    {
        return  date('Y-m-d', strtotime("-1 month"));
    }
    protected function _buildArrayDate($type, $from = 0, $to = 23, $original_time = null)
    {
        switch($type)
        {
            case MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_24H:
                $start_day = $original_time['d'];
                for($i = $from; $i <= $to; $i++)
                {
                    $data[$i]['incr_hour'] = $i;
                    $data[$i]['native_hour'] = ($i > 24) ? $i - 24 : $i;
                    $data[$i]['day'] = $start_day;

                    if($i == 23)
                    {
                        $start_day++;
                    }
                }
                break;
            case  MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_WEEK:
                $data = array();
                $day_in_month = $this->_days_in_month(date('m'), date('Y'));
                $clone_from = $from;
                $reset = false;
                for($i = 1; $i <=7; $i++)
                {
                    if($from > $day_in_month && !$reset){
                        $clone_from = 1;
                        $reset = true;
                    }
                    $data[$i]['count_day'] = $from;
                    $data[$i]['native_day'] = $clone_from;
                    $from++;
                    $clone_from++;
                }

                break;
            case  MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_LAST_MONTH:
                for($i = (int)$from; $i <= $to; $i++)
                {
                    $data[$i]['native_day'] = (int)$i;
                }
                break;
            case  MW_FollowUpEmail_Model_Admin_Const::REPORT_RAGE_CUSTOM:
                $total_days = $this->_dateDiff($original_time['from'], $original_time['to']);
                    if($total_days > 365)
                    {

                    }
                    else
                    {
                        $all_months = $this->_get_months($original_time['from'], $original_time['to']);
                        $start_time = strtotime($original_time['from']);
                        $start_day  = (int)date('d', $start_time);
                        $count      = 0;
                        $data       = array();

                        $end_day_time = strtotime($original_time['to']);

                        $end_day = array(
                            'm' => (int)date('m', $end_day_time),
                            'd' => (int)date('d', $end_day_time),
                            'y' => (int)date('Y', $end_day_time)
                        );
                        
                        foreach($all_months as $month)
                        {
                            $day_in_month = $this->_days_in_month($month['m'], $month['y']);
                            for($day = ($count == 0 ? $start_day : 1); $day <= $day_in_month; $day++)
                            {
                                if($day > $end_day['d'] && $month['m'] == $end_day['m'] && $month['y'] == $end_day['y']){
                                    continue;
                                }
                                $data[$month['y']][$month['m']][$day] = $day;
                            }
                            $count++;
                        }
                    }
            break;
        }
        return $data;
    }
    protected function _days_in_month($month, $year)
    {
        $year = (!$year) ? date('Y', Mage::getSingleton('core/date')->gmtTimestamp()) : $year;
        return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
    }
    protected function _dateDiff($d1, $d2)
    {
        // Return the number of days between the two dates:
        return round(abs(strtotime($d1) - strtotime($d2))/86400);
    }
    protected function _validationDate($data)
    {
        if(strtotime($data['from']) > strtotime($data['to']))
            return false;
        return true;
    }
    protected function _get_months($start, $end){
        $start = $start=='' ? time() : strtotime($start);
        $end = $end=='' ? time() : strtotime($end);
        $months = array();
        $data = array();
        
        for ($i = $start; $i <= $end; $i = $this->get_next_month($i)) {
            $data['m'] = (int)date('m', $i);
            $data['y'] = (int)date('Y', $i);
            array_push($months,$data);
        }

        return $months;
    }
    protected function get_next_month($tstamp) {
        return (strtotime('+1 months', strtotime(date('Y-m-01', $tstamp))));
    }
    protected function getPreviousDateTime($hour)
    {
        return Mage::getModel('core/date')->gmtTimestamp() - (3600 * $hour);
    }
    protected function convertNumberToMOnth($num)
    {
        $months = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec');
        return $months[$num];
    }
    protected function _returnTextType($type)
    {
        $data = array();
        if(count($this->const_type_rw) == 0)
        {
            $ref = new ReflectionClass ('MW_RewardPoints_Model_Type');
            $this->const_type_rw = $ref->getConstants();
        }

        foreach($this->const_type_rw as $const => $value)
        {
            if($type == $value)
            {
                $text = str_replace("_", " ", $const);
                $text = ucwords(strtolower($text));
                return $text;
            }
        }
    }
    protected function _calOffsetHourGMT()
    {
        $old_offset = Mage::getSingleton('core/date')->calculateOffset(Mage::app()->getStore()->getConfig('general/locale/timezone'))/60/60;
//        $new_offset = abs($old_offset);
//        return $new_offset;
        return $old_offset;
    }
}