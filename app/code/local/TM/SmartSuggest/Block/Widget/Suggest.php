<?php

class TM_SmartSuggest_Block_Widget_Suggest extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (isset($this->_data['wrapper'])) {
            $template = $this->_getData('wrapper');
        } else {
            $template = 'tm/smartsuggest/wrapper/content.phtml';
        }
        return $template;
    }

    /**
     * Create Suggest child block and fill it's data
     *
     * @return TM_SmartSuggest_Block_Widget_Suggest
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'smartsuggest',
            $this->getLayout()
                ->createBlock(
                    'smartsuggest/suggest', null, $this->getData()
                )
        );
        return parent::_prepareLayout();
    }
}
