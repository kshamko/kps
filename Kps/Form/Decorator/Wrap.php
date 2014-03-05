<?php

class Kps_Form_Decorator_Wrap extends Zend_Form_Decorator_Abstract {

    private $_open = '';
    private $_close = '';
    
    /**
     * @param array $options
     * @todo add validations for params
     */
    public function  __construct($options = null) {
        $this->_open = $options['open'];
        $this->_close = $options['close'];
    }

    public function render($content) {
        return $this->_open.$content.$this->_close;
    }

}
