<?php
/**
 * Cell renderer. Just outputs value
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column_Cell_Renderer_Text extends Kps_Grid_Column_Cell_Renderer{

    /**
     * Outputs value
     * @param string $value
     * @return string
     */
    public function render($value, $row = null){
        return $value;
    }
}