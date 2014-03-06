<?php
/**
 * Cell renderer. Just outputs value
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column_Cell_Renderer_TextEdit extends Kps_Grid_Column_Cell_Renderer{

    /**
     * Outputs value
     * @param string $value
     * @return string
     */
    public function render($value, $row = null){
       
        if(is_null($value)){
            $styleInput = 'display:block;';
            $styleValue = 'display:none;';
        }else{
            //$html = $value;
            $styleInput = 'display:none;';
            $styleValue = 'display:block;';
        }
        
        
        if(isset($this->_params['fieldGroupName'])){
            $fieldName = $this->_params['fieldGroupName'].'['.$this->_params['fieldName'].'][]';
        }else{
            $fieldName = $this->_params['fieldName'].'[]';
        }
        
        $html = '<div style="'.$styleInput.'">';
        $html .= '<input class="'.(isset($this->_params['class'])?$this->_params['class']:'').'" type="text" name="'.$fieldName.'" value="'.$value.'">';
        $html .= '</div>';
        
        $html .= '<div style="'.$styleValue.'">';
        $html .= $value;
        $html .= '</div>';        

        return $html;
    }
}