<?php
class Kps_View_Helper_Image extends Zend_View_Helper_FormElement {


    public function image($name, $value = null, $attribs = null) {
        return '<img src="'.$this->view->escape($value).'"/>';
    }
}
