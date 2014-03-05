<?php

/**
 * Class to handle data grids. Renders grid with paging and filters.
 * Grid could be rendred in any way you want just inmlement grid renderer.
 * Only table renderer is implemented yet (data will be displayed in table).
 *
 * @author Kantantsin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @abstract
 * @package Kps_Grid
 * @todo implement filters support and data sorting, create unit tests, think about grid templates
 */
abstract class Kps_Grid {

    /**
     * Is used as name if record id field to be able
     * to delete and edit record. Name of POST/GET param.
     */
    public $RECORD_ID = 'ID';

    /**
     * Used by paging. Name of POST/GET param.
     */
    public $pageKey = 'page';

    /**
     * Used by filter. Name of POST/GET param.
     */

    const FILTER_KEY = 'filter';

    /**
     * Array with Kps_Grid_Column elements
     * @var array
     */
    protected $_columns;

    /**
     * Object which knows how to render grid
     * @var Kps_Grid_Renderer
     */
    protected $_renderer;

    /**
     * Caption of a grid
     * @var string
     */
    protected $_caption;

    /**
     * Array of associative arrays which represents rows.
     * Each key from row array should match one of $_columns names
     * @var array
     */
    protected $_data = array();

    /**
     * Array with paging data
     * @var array
     */
    protected $_paging;

    /**
     * Number of current page
     * @var integer
     */
    protected $_currentPage = 1;

    /**
     * Number of entries on page
     * @var integer
     */
    protected $_itemsOnPage = 10;

    /**
     * Url to add new entry
     * @var string
     */
    protected $_addUrl;

    /**
     * Array of actions on grid data
     * @var array
     */
    protected $_actions = array();

    /**
     * Name of the id field. Used in grid template to find id
     * value of concrete entry.
     * @var string
     */
    protected $_idKey;

    /**
     * Array with conditions for data select
     * @var array
     * @todo depends on filter
     */
    protected $_where = null;

    /**
     * Order of data output
     * @var array
     * @todo depends on data sorting
     */
    protected $_order;

    /**
     * Paging rendered
     * @todo think about paging rendering (use Zend_Paginator view helpers or create custom renderer?)
     */
    protected $_pagingRenderer = null;

    /**
     * Url of the page with grid
     * @var string
     */
    protected $_baseUrl = null;

    /**
     *
     * @var Zend_Translate
     */
    protected static $_translator = null;

    /**
     *
     * @var type 
     */
    public $addButtonLabel = 'Add';

    /**
     *
     * @var type 
     */
    protected $_uriParts = null;
    
    /**
     * Initialization
     * @abstract
     */
    abstract function init();

    /**
     * Construct.
     * @param Zend_Controller_Request_Abstract $request
     * @todo add filter and sort functionality
     */
    public function __construct(Zend_Controller_Request_Abstract $request = null) {
        if ($request) {
            $this->_currentPage = (int) $request->getParam($this->pageKey, '1');

            $urlData = array();

            $order = $request->getParam('sort');
            if ($order && isset($order['field']) && isset($order['order'])) {
                $this->_order = $order;

                /*foreach ($order as $k => $v) {
                    $urlData['order'][$k] = $v;
                }*/
            }

            $filter = $request->getParam('filter');
            if ($filter) {
                foreach ($filter as $field => $value) {
                    $this->_where[] = array('field' => $field, 'condition' => 'LIKE', 'value' => '%' . $value . '%');
                    $urlData['filter'][$field] = $value;
                }
            }

            $this->_uriParts = $urlData;
        }
        $this->init();
    }

    /**
     *
     * @param Zend_Translate $translator
     * @return <type> 
     */
    public static function setTranslator(Zend_Translate $translator) {
        self::$_translator = $translator;
        //return $this;
    }

    /**
     * Sets class and method to fetch data for grid.
     * @param string $class
     * @param string $method
     * @return Kps_Grid
     */
    public function setDataGetter($class, $method, $params = null) {

        if (count($this->_data)) {
            throw new Kps_Grid_Exception('setData() method was used before.');
        }

        $this->_class = $class;
        $this->_method = $method;
        $this->_params = $params;

        return $this;
    }

    public function addDataParam($name, $value) {
        $this->_params[$name] = $value;
        return $this;
    }

    public function addCondition($field, $condition, $value) {
        $this->_where[] = array('field' => $field, 'condition' => $condition, 'value' => $value);
        return $this;
    }

    public function setBaseUrl($url) {
        $this->_baseUrl = $url;
        return $this;
    }

    public function getBaseUrl() {
        return $this->_baseUrl;
    }

    /**
     * Sets paging data
     * @param array 
     */
    public function setPaging($paging) {
        $this->_paging = $paging;
        return $this;
    }

    /**
     * Return paging object
     * @return Zend_Paginator
     */
    public function getPaging() {
        return $this->_paging; //Zend_Paginator::factory($this->_paging);
    }

    /**
     * Sets number of records on page
     * @param integer $items
     * @return Kps_Grid
     */
    public function setItemsOnPage($items) {
        $this->_itemsOnPage = (int) $items;
        return $this;
    }

    /**
     * Sets add url
     * @param string $url
     * @return Kps_Grid
     */
    public function setUrlAdd($url) {
        $this->_addUrl = $url;
        return $this;
    }

    /**
     * Returns add url
     * @return string
     */
    public function getUrlAdd() {
        return $this->_addUrl;
    }

    /**
     * Defines what could be done with grid data (i.e. edit/delete)
     * @param string $action
     * @param string $url 
     */
    public function setGridAction($action, $url, $requireConfirm = false) {
        $this->_actions[] = array(
            'url' => rtrim($url, '/') . '/' . $this->RECORD_ID . '/',
            'action' => $action,
            'requireConfirm' => $requireConfirm
        );
        return $this;
    }

    /**
     * Returns actions on grid data
     * @return array
     */
    public function getGridActions() {
        return $this->_actions;
    }

    /**
     * Sets grid data
     * @param array $data
     * @return Kps_Grid
     */
    public function setData(array $data) {
        if (count($this->_data)) {
            throw new Kps_Grid_Exception('setData() or setDataSetter() method was used before.');
        }
        $this->_data = $data;
        return $this;
    }

    /**
     * Returns grid data
     * @return array
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * Sets name of id field
     * @param string $key
     * @return Kps_Grid
     */
    public function setIdKey($key) {
        $this->_idKey = $key;
        return $this;
    }

    /**
     * Returns name of id field
     * @return string
     */
    public function getIdKey() {
        return $this->_idKey;
    }

    /**
     * Adds column to grid
     * @param Kps_Grid_Column $col
     */
    public function addColumn(Kps_Grid_Column $col) {
        $this->_columns[/* $col->getDataKey() */] = $col;
        $col->setUriParts($this->_uriParts);
        
        if ($this->_order) {
            $col->setOrder($this->_order['field'], $this->_order['order']);
        }
    }

    /**
     * Returns grid columns (array of Kps_Grid_Column)
     * @return array
     */
    public function getColumns() {
        return $this->_columns;
    }

    /**
     * Sets caption of a grid
     * @param string $caption
     * @return Kps_Grid
     */
    public function setCaption($caption) {
        $this->_caption = $caption;
        return $this;
    }

    /**
     * Returns caption of a grid
     * @return string
     */
    public function getCaption() {
        return $this->_caption;
    }

    /**
     *
     * @param type $id
     * @return \Kps_Grid 
     */
    public function setGridId($id) {
        $this->_gridId = $id;
        return $this;
    }

    public function getGridId() {
        return $this->_gridId;
    }

    /**
     * Sets grid renderer
     * @param Kps_Grid_Renderer $renderer
     * @return Kps_Grid
     */
    public function setRenderer(Kps_Grid_Renderer $renderer) {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * Returns rendered grid
     * @return string
     */
    public function render() {

        $object = new $this->_class();

        if (is_array($this->_order)) {
            $order = $this->_order['field'] . ' ' . $this->_order['order'];
        } else {
            $order = null;
        }

        $data = $object->{$this->_method}($this->_where, $order, $this->_currentPage, $this->_itemsOnPage, $this->_params);

        $this->setData($data['data']);
        $this->setPaging($data['paging']);


        if (!$this->_renderer) {
            throw new Kps_Grid_Exception('Renderer is not set');
        }
        return $this->_renderer->render($this);
    }

    /**
     *
     * @param <type> $text
     * @return <type> 
     */
    public static function translate($text) {
        if (self::$_translator) {
            return self::$_translator->_($text);
        }

        return $text;
    }

}