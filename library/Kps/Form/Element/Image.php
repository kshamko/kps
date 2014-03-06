<?php
class Kps_Form_Element_Image extends Zend_Form_Element_Xhtml {

    public function init() {
        $view = $this->getView();
        $view->addHelperPath(APPLICATION_PATH.'/../library/We/View/Helper/', 'Kps_View_Helper');
        $this->helper = 'image';
    }
    
    public function setValue($value){
        if(!$value){
            return $this;
        }
        return parent::setValue($value);
    }
}
