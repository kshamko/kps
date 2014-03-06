<?php

class Kps_Form_Decorator_Timepicker extends Zend_Form_Decorator_Abstract {


    public function render($content) {
        return str_replace('datepicker', 'datetimepicker', $content);
    }

}
