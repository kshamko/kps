<?php
/**
 * Cell renderer. Just outputs value
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column_Cell_Renderer_Schedule extends Kps_Grid_Column_Cell_Renderer{

    /**
     * Outputs value
     * @param string $value
     * @return string
     */
    public function render($value, $row = null){
        
        if(!$value){
            $styleInput = 'display:block;';
            $styleValue = 'display:none;';
            $value = '0-0-0-0-0-0-0';
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
        
        $dow = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        $html = '<div style="'.$styleInput.'">';
        
        
        $valueArray = explode('-', $value);
        for($i=0; $i<7; $i++){
            if($valueArray[$i]){
                $checked = 'checked="checked"';
            }else{
                $checked = '';
            }
            $html .= '<div style="width:30px" class="pull-left"><small>'.$dow[$i].'</small><br/> <input type="checkbox" '.$checked.' onchange="calculateSchedule(this)" value="1"></div>';
        }
        $html .= '<input type="hidden" name="'.$fieldName.'" value="'.$value.'">';
        $html .= '</div>';
        
        $html .= '<div style="'.$styleValue.'">';
        $html .= $value;
        $html .= '</div>';        

        //$html
        return $html;
    }
}