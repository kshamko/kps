<?php

class Kps_Form_Decorator_InputAppend extends Zend_Form_Decorator_Abstract {

    /**
     * Options:
     * - dependentFrom - instance of Zend_Form_Element - parent element
     * - url - url to request data
     * - valueKey - array key from response to use as value
     * - labelKey - array key from response to use as label
     *
     * @param array $options
     * @todo add validations for params
     */
    public function  __construct($options = null) {
        parent::__construct($options);
    }

    public function render($content) {
        return '<div class="input-append">'.$content.'</div>';
    }

}
