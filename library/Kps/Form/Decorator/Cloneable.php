<?php
/**
 * @todo remove hardcoded styles, refactor js
 */
class Kps_Form_Decorator_Cloneable extends Zend_Form_Decorator_Abstract {

    /**
     * Options:
     * - count - number of lines to show first time
     * - max_count - maximum number of row cloned
     * 
     * @param array $options
     */
    public function __construct($options = null) {
        if(!isset($options['count'])){
            $options['count'] = 1;
        }
        
        if(!isset($options['max_count'])){
            $options['max_count'] = 10;
        }
        
        parent::__construct($options);
    }

    public function render($content) {

        $html = '<ul id="' . $this->getElement()->getId() . '_list" class="unstyled">';

        if (!$content) {
            $value = $this->getElement()->getValue();
            if(!$value){
                for( $i=0; $i < $this->getOption('count'); $i++ ){
                    $value[] = '';
                }
            }
            $renderer = ($this->getOption('renderer'))?$this->getOption('renderer'):new Zend_Form_Decorator_ViewHelper();

            if (!is_array($value)) {
                $value = (array) $value;
            }
            
            foreach ($value as $i=>$v) {
                $renderer->setOption('row', $i);
                $html .= '<li style="padding: 0 0 5px 0">';
                $html .= $renderer->setElement($this->getElement()->setValue($v))->render('');
                $html .= ' <a href="#"><i class="icon-remove"></i></a>';
                $html .= '</li>';
            }
            $renderer->setOption('row', null);
            $content = $renderer->setElement($this->getElement()->setValue(null))->render('');
        } else {
            $html .= '<li style="padding: 0 0 5px 0">';
            $html .= $content;
            $html .= ' <a href="#"><i class="icon-remove"></i></a>';
            $html .= '</li>';
        }

        $html .= '<li style="padding: 0 0 5px 0">';
        $html .= '<a class="dev-add" href="#"><i class="icon-plus-sign"></i></a>';
        $html .= '</li>';
        $html .='</ul>';

        $html .= '<div id="' . $this->getElement()->getId() . '_clone" style="display:none">';
        $html .= $content;
        $html .= ' <a href="#"><i class="icon-remove"></i></a>';
        $html .= '</div>';

        $html .= '<script>';

        $disabled = $this->getElement()->getAttrib('disabled');        
        if (!$disabled) {
            $html .= '$(function() {
                        $("#' . $this->getElement()->getId() . '_clone").find("input").attr("disabled", "disabled");

                        var rows = $("#' . $this->getElement()->getId() . '_list").find("li");
                        var addButton = $("#' . $this->getElement()->getId() . '_list").find("a.dev-add");
                        if((rows.length -1) >= '.$this->getOption('max_count').'){
                            addButton.hide();
                        }   

                        rows.find("i.icon-remove").parent().click(function(){
                            $(this).parent().remove();
                            var rows = $("#' . $this->getElement()->getId() . '_list").find("li");

                            if( (rows.length - 1) < '.$this->getOption('max_count').'){
                                addButton.show();
                            }

                            return false;                            
                        })
                        
        		addButton.click(function(){
                            var rows = $("#' . $this->getElement()->getId() . '_list").find("li");
                            if( (rows.length) <= '.$this->getOption('max_count').'){
                            
                                var row = $("<li style=\"padding-bottom:5px\">"+$("#' . $this->getElement()->getId() . '_clone").html()+"</li>");
                                row.find("input").attr("disabled", null);
                                $(this).before(row);  

                                row.find("a").click(function(){
                                    $(this).parent().remove();
                                    var rows = $("#' . $this->getElement()->getId() . '_list").find("li");

                                    if( (rows.length - 1) < '.$this->getOption('max_count').'){
                                        addButton.show();
                                    }

                                    return false;
                                }); 
                                
                                if((rows.length) ==  '.$this->getOption('max_count').'){
                                    $(this).hide();
                                }
                            }
                            
                            return false;
                        })
                  })';
        } else {
            $html .= '$(function() {
                        var rows = $("#' . $this->getElement()->getId() . '_list").find("li");
                        rows.find("i.icon-remove").parent().click(function(){
                             return false;                            
                        });
                        
        		$("#' . $this->getElement()->getId() . '_list").find("a.dev-add").click(function(){
                            return false;
                        })
                  })';
        }

        $html .= '</script>';
        return $html;
    }

}