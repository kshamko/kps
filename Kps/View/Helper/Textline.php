<?php
class Kps_View_Helper_Textline extends Zend_View_Helper_FormElement {


    public function textline($name, $value = null, $attribs = null) {
        return $this->view->escape($value);
    }
}
