<?php
/**
 * Interface of renderer
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 * @todo temporary solution 
 */
interface Kps_Grid_Renderer{

    /**
     *
     * @param Kps_Grid*
     */
    public function render($data);
}