<?php

abstract class Kps_Form_DoctrineEntity extends Zend_Form
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em = null;
    
    /**
     *
     * @var array
     */
    private $_mapping = array();
    
       
    /**
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @return \Kps_Form_DoctrineEntity
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em){
        $this->_em = $em;
        return $this;
    }
    
    /**
     * 
     * @param Zend_Form_Element $element
     * @param string $entityField
     */
    public function mapElementToEntity(Zend_Form_Element $element, $entityField){
        if(!$this->_em){
            throw new Kps_Form_Exception('Entity manager is not defined');
        }
        
        $this->_mapping[$element->getName()] = $entityField;
        return $this;
    }
    
    /**
     * 
     * @return \Kps_Form_DoctrineEntity
     * @throws Kps_Form_Exception
     */
    public function save($entity){
        if(!count($this->_mapping)){
            throw new Kps_Form_Exception('Empty mapping');
        }
        
        $values = $this->getValues();
        foreach($this->_mapping as $name=>$map){
            $entity->{'set'.$map}($values[$name]);
        }        

        if($entity->getId()){
            $this->_em->merge($entity);
        }else{
            $this->_em->persist($entity);
        }

        $this->_em->flush();
    }
    
    /**
     * 
     * @param array $defaults
     */
    public function setDefaults(array $defaults, $entity = null) {
        if(is_object($entity)){
            foreach($this->_mapping as $name=>$map){
                $defaults[$name] = $entity->{'get'.$map}();
            }
        }
        parent::setDefaults($defaults);
    }
}

