<?php

class Kps_Form_Decorator_Tooltip extends Zend_Form_Decorator_Abstract {
    
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
        if(!isset($options['tooltip_html'])){
            throw new Bel_Form_Decorator_Exception('tooltip_html is not passed');
        }
        
        parent::__construct($options);
    }

    public function render($content) {
        $tooltipHtml = null;
        $translate = $this->getElement()->getTranslator();
        if($translate){
            $tooltipHtml = $translate->_($this->_options['tooltip_html']);
        }else{
            $tooltipHtml = $this->_options['tooltip_html'];
        }
        
        $id = $this->getElement()->getId().'_tooltip';
        $html = $content.' <a href="javascript:void(0)" id="'.$id.'"><i class="icon-info-sign"></i></a>';
        $html .= '<script>';
        $html .= '$("#'.$id.'").popover({content:"'.addslashes($tooltipHtml).'", trigger: "hover", html: true}).click(function(e) {
       e.preventDefault();
       $(this).focus();
   });';
        $html .='</script>';
        return $html;
    }

}
