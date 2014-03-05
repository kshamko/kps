<?php
abstract class Kps_Model extends Zend_Db_Table_Abstract {

    protected $_db;

    /**
     * Constructor. Sets table name & primary key
     *
     * @param array $array - see Zend_Db_Table_Abstract for details
     */
    public function __construct($array = array()) {
        if (isset($array['db'])) {
            $this->_db = $array['db'];
        } else {
            $this->_db = Zend_Registry::get('db');
        }

        try {
            $this->_db->query('SELECT 1');
        } catch (Exception $e) {
            //reconnect if connection lost due the timeout
            if (strstr($e->getMessage(), 'server has gone away')) {
                $config = $this->_db->getConfig();
                $this->_db = Zend_Db::factory('PDO_MYSQL', $config);
                $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
                Zend_Registry::set('db', $this->_db);
            }
        }
        self::setDefaultAdapter($this->_db);
    }

    /**
     *
     * @param <type> $id
     * @return <type>
     */
    public function getById($id) {
        $entry = $this->find($id);
        if ($entry) {
            $entry = $entry->current();
            if ($entry) {
                return $entry->toArray();
            } else {
                return null;
                //throw new Bel_Model_Exception('Entry (id='.$id.') was not found in "'.$this->_name.'" table');
            }
        } else {
            return null;
        }
    }

    /**
     *
     * @param array $ids
     * @return null 
     */
    public function getByIds(array $ids){
        if(!count($ids)) return null;
        
        $id = isset($this->_primary[0])?$this->_primary[0]:$this->_primary[1];
        $res = $this->fetchAll(array($id.' IN (?)'=>$ids))->toArray();
        
        if(count($res)){
            $data = array();
            foreach($res as $key=>$val){
                $data[$val[$id]] = $val;
            }
            return $data;
        }
        
        return null;
    }
    /**
     *
     * @param <type> $where
     * @param <type> $order
     * @param <type> $page
     * @param <type> $itemsOnPage
     * @return <type>
     */
    public function getAll($where, $order=null, $page=null, $itemsOnPage=null, $params = null) {
        $select = $this->_db->select()->from($this->_name)->order($order);
        if ($where) {
            foreach ($where as $w) {
                $select->where($w['field'] . ' ' . $w['condition'] . ' ?', $w['value']);
            }
        }
       
        $paging = $this->_setPaging($select, $page, $itemsOnPage);
        $data = (array) $paging->getItemsByPage($page);
        return array('data' => $data, 'paging' => $paging->getPages());
    }

    /**
     *
     * @param array $data
     * @return <type>
     */
    public function addEntry(array $data) {
        $data = $this->_mapArrayToCols($data);
        if (count($data)) {
            $row = $this->createRow();
            foreach ($data as $col => $value) {
                $row->$col = $value;
            }
            return $row->save();
        }

        return false;
    }

    public function deleteEntryById($id) {
        $entry = $this->find($id);
        if ($entry) {
            return $entry->current()->delete();
        }

        return 0;
    }

    public function deleteEntriesByIds(array $ids) {
        $cols = $this->info('cols');
        return $this->delete(array($cols[0] . ' IN (?)' => $ids));
    }

    /**
     *
     * @param <type> $id
     * @param array $data
     * @return <type>
     */
    public function updateEntry($id, array $data) {
        $entry = $this->find($id)->current();
        if ($entry) {
            $data = $this->_mapArrayToCols($data);
            foreach ($data as $col => $value) {
                $entry->$col = $value;
            }

            return $entry->save();
        }

        return null;
    }

    /**
     *
     * @param type $ids
     * @param type $data 
     */
    public function updateEntries(array $ids, $data) {
        $data = $this->_mapArrayToCols($data);
        $cols = $this->info('cols');
        return $this->update($data, array($cols[0] . ' IN (?)' => $ids));
    }

    /**
     *
     * @param Zend_Db_Select $select
     * @param <type> $page
     * @param <type> $itemsOnPage
     * @return <type>
     */
    protected function _setPaging(Zend_Db_Select $select, $page, $itemsOnPage) {
        $paging = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        $paging->setCurrentPageNumber($page);
        $paging->setItemCountPerPage($itemsOnPage);
        return $paging;
    }

    /**
     *
     * @param array $data
     */
    protected function _mapArrayToCols(array $data) {
        $dbCols = $this->info('cols');

        $mappedData = array();
        foreach ($dbCols as $col) {
            if (isset($data[$col])) {
                $mappedData[$col] = $data[$col];
            }
        }

        return $mappedData;
    }

}