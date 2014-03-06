<?php

/**
 * Class of grid's column
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @package Kps_Grid
 */
class Kps_Grid_Column {

    /**
     * Label of a column
     * @var string
     */
    protected $_label = null;

    /**
     * Defines if column sortable
     * @var bool
     */
    protected $_sortable = null;

    /**
     * Defines if column could be used in column
     * @var bool
     */
    protected $_filterable = null;

    /**
     * Name of a field from row array
     * @var string
     */
    protected $_dataKey = null;

    /**
     * Column width
     * @var integer
     */
    protected $_width = null;

    /**
     * Array of cell renderers. Defines how a cell should be rendered
     * @var array
     */
    protected $_cellRenderers = array();

    /**
     * Renderer of column header
     * @var Kps_Grid_Renderer
     */
    protected $_renderer;

    /**
     *
     * @var type 
     */
    protected $_order;

    /**
     *
     * @var type 
     */
    protected $_uriParts;
    
    /**
     * Initialization. Sets default cell renderer
     */
    public function __construct() {
        $this->_cellRenderers[] = new Kps_Grid_Column_Cell_Renderer_Text();
    }

    /**
     *
     * @param type $uriParts
     * @return \Kps_Grid_Column 
     */
    public function setUriParts($uriParts){
        if($uriParts){
            $this->_uriParts = $uriParts;
        }
        return $this;            
    }
    
    public function getCurrentUri(){
        if($this->_uriParts){
            return '?'.urldecode(http_build_query($this->_uriParts)).'&';
        }
        
        return '?';
    }
    
    /**
     * 
     */
    public function setOrder($field, $order) {
        if ($field == $this->getDataKey()) {
            
            if (strtolower($order) == 'asc') {
                $nextOrder = 'desc';
            } else {
                $nextOrder = 'asc';
            }

            $this->_order = array('field' => $field, 'order' => $order, 'nextOrder' => $nextOrder);
        }
        return $this;
    }

    /**
     *
     * @return type 
     */
    public function getOrder() {
        if (!$this->_order) {
            $this->_order = array('nextOrder' => 'asc');
        }
        return $this->_order;
    }

    /**
     * Sets column renderer
     * @param Kps_Grid_Renderer $renderer
     * @return Kps_Grid_Column
     */
    public function setRenderer(Kps_Grid_Renderer $renderer) {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * Adds cell renderers
     * @param Kps_Grid_Column_Cell_Renderer $renderer
     * @return Kps_Grid_Column
     */
    public function setCellRenderer(Kps_Grid_Column_Cell_Renderer $renderer) {
        $this->_cellRenderers[] = $renderer;
        return $this;
    }

    /**
     * Sets row key
     * @param string $key
     * @return Kps_Grid_Column
     */
    public function setDataKey($key) {
        $this->_dataKey = $key;
        return $this;
    }

    /**
     * Sets column width
     * @param integer $width
     * @return Kps_Grid_Column
     */
    public function setWidth($width) {
        $this->_width = $width;
        return $this;
    }

    /**
     * Set column label
     * @param string $label
     * @return Kps_Grid_Column
     */
    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }

    /**
     * Sets sortable flag for column
     * @param boolean $sortable
     * @return Kps_Grid_Column
     */
    public function setSortable($sortable) {
        $this->_sortable = $sortable;
        return $this;
    }

    /**
     * Sets filterable flaf for column
     * @param <type> $filterable
     * @return Kps_Grid_Column
     */
    public function setFilterable($filterable) {
        $this->_filterable = $filterable;
        return $this;
    }

    /**
     * Return column renderer
     * @return Kps_Grid_Renderer
     */
    public function getRenderer() {
        return $this->_renderer;
    }

    /**
     * Return array of cell renderers
     * @return array
     */
    public function getCellRenderers() {
        return $this->_cellRenderers;
    }

    /**
     * Return column label
     * @return string
     */
    public function getLabel() {
        return $this->_label;
    }

    /**
     * Returns row key
     * @return string
     */
    public function getDataKey() {
        if (!$this->_dataKey) {
            throw new Kps_Grid_Exception('Data key for ' . $this->_label . ' field was not set in grid class');
        }
        return $this->_dataKey;
    }

    /**
     * Return column width
     * @return integer
     */
    public function getWidth() {
        return (int) $this->_width;
    }

    /**
     * Return sortable flag
     * @return bool
     */
    public function getSortable() {
        return (bool) $this->_sortable;
    }

    /**
     * Returns filterable flag
     * @return bool
     */
    public function getFilterable() {
        return $this->_filterable;
    }

    /**
     * Renders column header
     * @return string
     */
    public function render() {
        if (!$this->_renderer) {
            throw new Kps_Grid_Exception('Renderer is not set');
        }

        return $this->_renderer->render($this);
    }

}