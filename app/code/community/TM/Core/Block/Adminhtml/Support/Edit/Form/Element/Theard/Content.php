<?php
class TM_Core_Block_Adminhtml_Support_Edit_Form_Element_Theard_Content extends Mage_Adminhtml_Block_Widget
{
    /**
     *
     * @var TM_Helpmate_Model_Ticket
     */
    protected $_ticket;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tmcore/ticket/edit/form/element/theard/content.phtml');

        $this->_ticket = Mage::registry('tmcore_support');
    }

    /**
     *
     * @return TM_Helpmate_Model_Ticket
     */
    public function getTicket()
    {
        return $this->_ticket;
    }

    /**
     *
     * @return array()
     */
    public function getTheards()
    {
        return $this->getTicket()->getTheards();
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardOwnerTitle(array $theard)
    {
//        Zend_Debug::dump($theard);
        if (empty($theard['user_name'])) {
            return 'Your said';
        }

        return $this->helper('helpmate')->__('%s said (admin)', $theard['user_name']);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardCreatedAt(array $theard, $dateType = 'date', $format = 'medium')
    {
        if (!isset($theard['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($theard['created_at'], $format);
        }
        return $this->helper('core')->formatTime($theard['created_at'], $format);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardModifiedAt(array $theard, $dateType = 'date', $format = 'medium')
    {
        if (!isset($theard['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($theard['modified_at'], $format);
        }
        return $this->helper('core')->formatTime($theard['modified_at'], $format);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardStatus(array $theard)
    {
        $collection = $this->getTicket()->getStatuses();
        $item = $collection->getItemById($theard['status']);
        return $item ? $item->getName() : '';
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardDepartment(array $theard)
    {
        $collection = $this->getTicket()->getDepartmets();
        $item = $collection->getItemById($theard['department_id']);
        return $item ? $item->getName() : '';
    }

    public function getTheardPriority(array $theard)
    {
        $collection = $this->getTicket()->getPriorities();
        $item = $collection->getItemById($theard['priority']);
        return $item ? $item->getName() : '';
    }

    public function getTheardText(array $theard)
    {
        if (empty($theard['text'])) {
            return '';
        }

//        if ($isSecure) {
//            return Mage::helper('purify')->purify(nl2br($theard['text']));
//        }

        $content = $theard['text'];

        // text/html convert pseudo text/palin
        $tags = array (
            0 => '~<h[123][^>]+>~si',
            1 => '~<h[456][^>]+>~si',
            2 => '~<table[^>]+>~si',
            3 => '~<tr[^>]+>~si',
            4 => '~<li[^>]+>~si',
            5 => '~<br[^>]+>~si',
            6 => '~<p[^>]+>~si',
            7 => '~<div[^>]+>~si',
        );
        $content = preg_replace($tags, "\n", $content);
        $content = preg_replace('~</t(d|h)>\s*<t(d|h)[^>]+>~si', ' - ', $content);
        $content = preg_replace('~<[^>]+>~s', '', $content);
        // reducing spaces
        $content = preg_replace('~ +~s', ' ', $content);
        $content = preg_replace('~^\s+~m', '', $content);
        $content = preg_replace('~\s+$~m', '', $content);
        // reducing newlines
        $content = preg_replace('~\n+~s', "\n", $content);

        $_content = '';
        $isOld = false;
        $content = wordwrap($content, 170, "\n");
        foreach (explode("\n", $content) as $_line) {
            $_isOld = ('>' === $_line[0]) ? true : false;
            if ($_isOld && !$isOld) {
                $isOld = true;
                $_content .= '<span>' . $this->escapeHtml($_line) . "</span><div>";
                continue;
            }
            if (!$_isOld && $isOld) {
                $isOld = false;
                $_content .= "</div>\n";
            }
            $_content .= $this->escapeHtml($_line) . "\n";
        }
//        $content = $this->escapeHtml($content, array('div', 'span', 'hr'));
        return "<pre class=\"theard_content\" style=\"white-space:pre-wrap\">" .
            "<code>" .
                $_content .
            '</code>' .
        '</pre>';
    }

    public function getTheardFileUrl(array $theard)
    {
        $path = Mage::getBaseUrl('media') . 'helpmate' . DS;
        $files = array_filter(explode(';', $theard['file']));

        foreach ($files as &$file) {
            $file = $path . $file;
        }

        return $files;
    }
}
