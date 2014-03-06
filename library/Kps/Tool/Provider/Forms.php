<?php
/**
 * Provider for Zend_Tool to generate Zend_Form classes
 *
 * @author Kanstantsin Shamko <konstantin.shamko@gmail.com> *
 */
class Kps_Tool_Provider_Forms implements Zend_Tool_Framework_Provider_Interface {

    /**
     * Used for input prompting
     *
     * @var Zend_Tool_Framework_Registry
     */
    private $_registry;

    /**
     * Initial setup
     */
    public function __construct() {
        $this->_registry = new Zend_Tool_Framework_Registry();
        $this->_registry->setClient(new Zend_Tool_Framework_Client_Console());
    }

    /**
     * Creates form class. This method is called from console
     *
     * @param string $formName - name of the form
     * @param string $method - form method
     * @param string $action - form action
     */
    public function generate($formName, $method, $action) {
        $className = 'forms_'.$formName;

        $body = 'parent::__construct($options);';
        $body .= "\n\n";
        $body.='$this->setName(\''.$formName.'\');';
        $body.='$this->setAction(\''.$action.'\');';
        $body.='$this->setMethod(\''.$method.'\');';
        $body .= "\n\n";

        $i = 0;

        $fields = array();

        while(!$this->_promtForFinish()) {
            $fields[$i]['name'] = $this->_promtForFieldName();
            $fields[$i]['label'] = $this->_promtForFieldLabel();
            $fields[$i]['type'] = $this->_promtForFieldType();
            $fields[$i]['required'] = $this->_promtForRequired();
            $fields[$i]['validators'] = $this->_promtForValidation();
            $fields[$i]['filters'] = $this->_promtForFilters();
            $i++;
        }

        foreach ($fields as $key=>$field) {
            $f = '$field'.$key;
            $body .= $f.' = new '.$field['type'].'(\''.$field['name'].'\');';
            $body .= $f.'->setRequired('.$field['required'].');';
            $body .= $f.'->setLabel(\''.$field['label'].'\');';

            foreach($field['validators'] as $v) {
                $body .= $f.'->addValidator(new '.$v.'());';
            }

            foreach($field['filters'] as $filter) {
                $body .= $f.'->addFilter(new '.$filter.'());';
            }

            $body .= '$this->addElement('.$f.');';
            $body .= "\n\n";
        }

        $codeGenFile = new Zend_CodeGenerator_Php_File(array(
                        'classes' => array(
                                new Zend_CodeGenerator_Php_Class(array(
                                        'name' => $className,
                                        'extendedClass' => 'Zend_Form',
                                        'methods' => array(
                                                new Zend_CodeGenerator_Php_Method(array(
                                                        'name' => '__construct',
                                                        'body' => $body,
                                                        'parameters'=>array(array('defaultValue'=>array(), 'name'=>'options')),
                                                ))
                                        )
                                ))
                        )
        ));

        $filePath = dirname(__FILE__).'/../../../../application/forms/'.$formName.'.php';

        file_put_contents($filePath, $codeGenFile->__toString());
        echo $codeGenFile->__toString();
    }

    /**
     * Promt for finish
     *
     * @return bool
     */
    private function _promtForFinish() {

        $res = $this->_promt('Are you done?', array('Yes', 'No'));

        if($res == 0) {
            return true;
        }else {
            return false;
        }
    }

    /**
     *
     * Asks if the field is required
     *
     * @return bool
     */
    private function _promtForRequired() {

        $res = $this->_promt('Field required?', array('Yes', 'No'));

        if($res == 0) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * Asks for name of the field
     *
     * @return string
     */
    private function _promtForFieldName() {
        return $this->_promt('Type field name');
    }

    /**
     * Asks for label of the field
     *
     * @return string
     */
    private function _promtForFieldLabel() {
        return $this->_promt('Type field label');
    }

    /**
     * Asks for type of the field
     *
     * @return string
     */
    private function _promtForFieldType() {
        $oDir = dir( dirname(__FILE__).'/../../../Zend/Form/Element');
        $fieldTypes = array();

        while (false !== ($entry = $oDir->read())) {
            if($entry!=='..' && $entry!=='.') {
                $fieldTypes[] = str_replace('.php', '', $entry);
            }
        }

        $result = (int)$this->_promt('Choose field type', $fieldTypes);
        return 'Zend_Form_Element_'.$fieldTypes[$result];
    }

    /**
     * Asks for validation rules of the field
     *
     * @return array
     */
    private function _promtForValidation() {
        $oDir = dir( dirname(__FILE__).'/../../../Zend/Validate');
        $validTypes = array('Validation is not required');
        while (false !== ($entry = $oDir->read())) {
            if($entry!=='..' && $entry!=='.' && !is_dir($entry)) {
                $validTypes[] = str_replace('.php', '', $entry);
            }
        }

        $result = $this->_promt('Choose validation (comma separated)', $validTypes);
        $result = explode(',', $result);
        $valid = array();

        foreach($result as $r) {
            if(isset($validTypes[$r]) && $r) {
                $valid[] = 'Zend_Validate_'.trim($validTypes[$r]);
            }
        }

        return $valid;
    }

    /**
     * Asks for filters for the field
     *
     * @return array
     */
    private function _promtForFilters() {
        $oDir = dir( dirname(__FILE__).'/../../../Zend/Filter');
        $filterTypes = array('Filter is not required');
        while (false !== ($entry = $oDir->read())) {
            if($entry!=='..' && $entry!=='.' && !is_dir($entry)) {
                $filterTypes[] = str_replace('.php', '', $entry);
            }
        }

        $result = $this->_promt('Choose filters (comma separated)', $filterTypes);
        $result = explode(',', $result);
        $filter = array();

        foreach($result as $r) {
            if(isset($filterTypes[$r]) && $r) {
                $filter[] = 'Zend_Filter_'.trim($filterTypes[$r]);
            }
        }

        return $filter;
    }

    /**
     * Promts for something
     *
     * @param string $text
     * @param array $options
     * @return string
     */
    private function _promt($text, $options = array()) {
        if(count($options)) {
            $text .= "\n";
            foreach($options as $key=>$val) {
                $text .= $key.'. '.$val."\n";
            }
        }
        $oRequest = $this->_registry->getClient()->promptInteractiveInput($text);
        return $oRequest->getContent();
    }
}
