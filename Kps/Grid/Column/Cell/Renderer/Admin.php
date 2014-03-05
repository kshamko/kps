<?php
/**
 * Cell renderer. Renders value as 
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column_Cell_Renderer_Admin extends Kps_Grid_Column_Cell_Renderer {

    /**
     * Outputs value as link
     * @param string $value
     * @return string
     */
    public function render($value, $row = null) {
        if($value){
            return '<a href="/admin/users/login/uid/'.$value['id'].'">'.$value['user_first_name'].' '.$value['user_last_name'].' ('.$value['id'].')</a>';
        }
        
        return '-';
    }
}