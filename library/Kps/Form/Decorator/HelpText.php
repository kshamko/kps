<?php

class Kps_Form_Decorator_HelpText extends Zend_Form_Decorator_Abstract {
    
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
        if(!isset($options['text'])){
            throw new Bel_Form_Decorator_Exception('text is not passed');
        }
        
        parent::__construct($options);
    }

    public function render($content) {
        $tooltipHtml = null;
        $translate = $this->getElement()->getTranslator();
        if($translate){
            $tooltipHtml = $translate->_($this->_options['text']);
        }else{
            $tooltipHtml = $this->_options['text'];
        }

        $html = $content.'<span class="help-block">'.$tooltipHtml.'</span>';
        return $html;
    }

}
