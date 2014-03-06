<?php

class Kps_Form_Decorator_After extends Zend_Form_Decorator_Abstract {

    private $_afterHTML = '';
    
    /**
     * @param array $options
     * @todo add validations for params
     */
    public function  __construct($options = null) {
        $this->_afterHTML = $options['html'];
    }

    public function render($content) {
        return $content.$this->_afterHTML;
    }

}
