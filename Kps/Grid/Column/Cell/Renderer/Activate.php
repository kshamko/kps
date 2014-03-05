<?php

/**
 * Cell renderer. Renders activate/deactivate button
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column_Cell_Renderer_Activate extends Kps_Grid_Column_Cell_Renderer {

    /**
     * Outputs value
     * @param string $value
     * @return string
     */
    public function render($value, $row = null) {
        $html = '<a href="' . $this->_params['url'] .  $row[$this->_params['key']]. '">';
        if ($value) {
            $html .= 'Deactivate';
        } else {
            $html .= '<strong>Activate</strong>';
        }
        $html .= '</a>';
        
        return $html;
    }

}