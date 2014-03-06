<?php
/**
 * Column renderer for table grid
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 * @todo addSetTemplate method, replace renderer interface with abstract class
 */
class Kps_Grid_Renderer_Table_Column implements Kps_Grid_Renderer{

    /**
     * Template to render
     * @var string
     */
    protected $_template = 'column.phtml';

    /**
     * Renders column
     * 
     * @param Kps_Grid_Column $column
     * @return string
     */
    public function render($column){
        if(!($column instanceof Kps_Grid_Column)){
            throw new Kps_Grid_Exception('Passed param should instanced of Kps_Grid_Column');
        }
        $view = new Zend_View();
        $view->setScriptPath(dirname(__FILE__).'/views/');
        $view->column = $column;
        return $view->render($this->_template);
    }
}