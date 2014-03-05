<?php
/**
 * Cell renderer. Renders value as link
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column_Cell_Renderer_Link extends Kps_Grid_Column_Cell_Renderer {

    /**
     * Outputs value as link
     * @param string $value
     * @return string
     */
    public function render($value, $row = null) {
        if(!isset ($this->_params['link_text'])) {
            throw new Kps_Grid_Exception('link_text param for '.__CLASS__.' is not defined');
        }
        if (trim($value)!='') {
            return '<a '.(isset($this->_params['target'])?'target="'.$this->_params['target'].'"':'').'href="'.$value.'">'.
                ((isset($this->_params['type']) && $this->_params['type']=='button') ? '<button>' : '').
                $this->_params['link_text'].
                ((isset($this->_params['type']) && $this->_params['type']=='button') ? '</button>' : '').
                '</a>';
        } else {
            return '';
        }
    }
}