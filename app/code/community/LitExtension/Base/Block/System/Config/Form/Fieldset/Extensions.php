<?php
/**
 * @project: Base
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

class LitExtension_Base_Block_System_Config_Form_Fieldset_Extensions
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $info = unserialize(Mage::app()->loadCache('lebase_extensions'));
        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        sort($modules);
        $html = '<link type="text/css" rel="stylesheet" media="screen" href="'.$this->getSkinUrl('litextension/base/css/style.css').'"/>';
        $html .= $this->_getTitleHtml();
        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'LitExtension') === FALSE) {
                continue;
            }
            if ($moduleName == 'LitExtension_Base') {
                continue;
            }
            $html.=  $this->_getHtml($moduleName, $info);
        }
        return $html;
    }

    protected function _getHtml($code, $info){
        $html = '<div class="le-module-install">
                    <div class="le-check">'.$this->_getStatus($code, $info).' </div>
                    <div class="le-name">'.$this->_getModuleName($code,$info).'</div>
                    <div class="le-version">'.$this->_getVersion($code).'</div>
                    <div class="le-version-update">'.$this->_getVersionUpdate($code, $info).'</div>
                 </div>';
        return $html;
    }

    protected function _getTitleHtml(){
        $html = '<div class="le-module-install title">
                    <div class="le-check">'.$this->__('Status').'</div>
                    <div class="le-name">'.$this->__('Module Name').'</div>
                    <div class="le-version">'.$this->__('Installed Version').'</div>
                    <div class="le-version-update">'.$this->__('Latest Version').'</div>
                 </div>';
        return $html;
    }

    protected function _checkModuleEnable($code){
        return Mage::helper('core')->isModuleEnabled($code);
    }

    protected function _getStatus($code, $info){
        $html = '';
        if($this->_checkModuleEnable($code) == false){
            $html .= '<image src="'.$this->getSkinUrl('litextension/base/img/disable.png').'" title="'.$this->__('Disable').'">';
        } else {
            if($info && isset($info[$code]) && ($this->_convertVersion(Mage::getConfig()->getModuleConfig($code)->version) < $this->_convertVersion($info[$code]['version']))){
                $html .= '<image src="'.$this->getSkinUrl('litextension/base/img/update.png').'" title="'.$this->__('Update available').'">';
            } else{
                $html .= '<image src="'.$this->getSkinUrl('litextension/base/img/ok.png').'" title="'.$this->__('Installed').'">';
            }
        }
        return $html;
    }

    protected function _getVersion($code){
        $version = Mage::getConfig()->getModuleConfig($code)->version;
        if($version){
            return $version;
        } else{
            return '-------';
        }
    }

    protected function _getVersionUpdate($code, $info){
        $version = '';
        if($info && isset($info[$code])){
            $version = $info[$code]['version'];
        }
        if(!$info || !isset($info[$code])){
            $version = $this->_getVersion($code);
        }
        return $version;
    }

    protected function _getModuleName($code, $info){
        $html = '';
        if(isset($info[$code]) && $info[$code]['name']){
            $name = $info[$code]['name'];
        } else {
            $name = substr($code, strpos($code, '_') + 1);
        }
        if($this->_checkModuleEnable($code) == false){
            $html .= $name;
        } else {
            if($info && isset($info[$code]) && ($this->_convertVersion(Mage::getConfig()->getModuleConfig($code)->version) < $this->_convertVersion($info[$code]['version']))){
                $html .= '<a href="'.$info[$code]['url'].'" target="_blank">'.$name.'</a>';
            } else{
                $html .= $name;
            }
        }
        return $html;
    }

    protected function _convertVersion($v) {
        $digits = @explode(".", $v);
        $version = 0;
        if (is_array($digits)) {
            foreach ($digits as $k => $v) {
                $version += ($v * pow(10, max(0, (3 - $k))));
            }
        }
        return $version;
    }

}
