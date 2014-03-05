<?php
/**
 * Grid renderer for table grid
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 * @todo addSetTemplate method, replace renderer interface with abstract class
 */
class Kps_Grid_Renderer_Table_Grid implements Kps_Grid_Renderer{

    /**
     * Grid template
     * @var string
     */
    protected $_template = 'grid.phtml';

    /**
     * 
     */
    public function __construct(){
        $this->_template = dirname(__FILE__).'/views/'.$this->_template;
    }
    
    /**
     *
     * @param type $template
     * @return type 
     */
    public function setTemplate($template){
        $this->_template = $template;
        return $this->_template;
    }
    
    /**
     * Renders table grid
     * @param Kps_Grid $grid
     * @return string
     */
    public function render($grid){
        if(!($grid instanceof Kps_Grid)){
            throw new Kps_Grid_Exception('Passed param should instanced of Kps_Grid');
        }
        
       
        $view = new Zend_View();
        $view->setScriptPath(dirname($this->_template));
        $view->grid = $grid;
        return $view->render(basename($this->_template));
    }
}