<?php
class Kps_Form_Element_ExpireDate extends Zend_Form_Element_Xhtml {

    public function init() {
        $view = $this->getView();
        $view->addHelperPath(APPLICATION_PATH.'/../library/We/View/Helper/', 'Kps_View_Helper');
        $this->helper = 'expireDate';
    }
    
    public function isValid ($value, $context = null)
    {
        if (is_array($value)) {
            if(strlen($value['month'])==1) {
                $value['month'] = '0'.$value['month'];
            }
            $value = $value['month'] . '/' .
                    $value['year'];

            if($value == '00/0') {
                $value = null;
            }
        }

        return parent::isValid($value, $context);
    }
    
    public function getValue()
    {
        if(is_array($this->_value)) {
            if(strlen($value['month'])==1) {
                $value['month'] = '0'.$value['month'];
            }
            $value = $value['month'] . '/' .
                    $value['year'];
    
            if($value == '00/0') {
                $value = null;
            }
            $this->setValue($value);
        }
    
        return parent::getValue();
    }
    

    public function setValue($value){
        if(!$value){
            return $this;
        }
        return parent::setValue($value);
    }
}