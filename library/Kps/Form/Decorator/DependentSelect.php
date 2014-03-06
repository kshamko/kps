<?php

class Kps_Form_Decorator_DependentSelect extends Zend_Form_Decorator_Abstract {

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

        $options = $this->getOptions();

        $append = '<script type="text/javascript">';
        $append .= '$("#'.$options['dependentFrom'].'").bind("change", function(){';
        $append .= '$.ajax({';
        $append .= 'url: "'.$options['url'].'",';
        $append .= 'data: {id:$(this).val()},';
        $append .= 'dataType: "json",';
        $append .= 'type: "POST",';
        $append .= 'success: function(response){';
        $append .= '$("#'.$this->getElement()->getId().'").find("option").remove();';
        $append .= 'for(var i=0; i<response.length; i++){';
        $append .= '$("<option value=\'"+response[i].'.$options['valueKey'].'+"\'>"+response[i].'.$options['labelKey'].'+"</option>").appendTo("#'.$this->getElement()->getId().'");';
        $append .= '}';
        $append .= '}';
        $append .= '})';
        $append .= '})';
        $append .= '</script>';

        return $content.$append;
    }

}
