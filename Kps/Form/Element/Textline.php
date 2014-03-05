<?php
class Kps_Form_Element_Textline extends Zend_Form_Element_Xhtml {

    public function init() {
        $view = $this->getView();
        $view->addHelperPath(APPLICATION_PATH.'/../library/We/View/Helper/', 'Kps_View_Helper');
        $this->helper = 'textline';
    }
    
    public function setValue($value){
        if(!$value){
            return $this;
        }
        return parent::setValue($value);
    }
}
