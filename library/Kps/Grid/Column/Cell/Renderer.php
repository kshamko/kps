<?php
/**
 * Cell renderer class
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @abstract
 * @package Kps_Grid
 */
abstract class Kps_Grid_Column_Cell_Renderer{

    /**
     * Renderer params
     * @var array
     */
    protected $_params;

    /**
     * Init.
     * @param array $params
     */
    public function  __construct($params = array()) {
        $this->_params = $params;
    }

    /**
     * Renders $value according to renderer type
     * @param string $value
     */
    public abstract function render($value, $row = null);
}