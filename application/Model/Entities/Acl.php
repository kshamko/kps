<?php
use Doctrine\ORM\Mapping as ORM;

/**
* @Entity
* @Table(name="kps_acl")
*/
class Model_Entities_Acl{
    
    /** @Id @Column(type="integer") */
    private $acl_id;
    
    /** @Column(length=100) */
    private $acl_resource; // type defaults to string    
    
    /** @Column(length=30) */
    private $acl_group; // type defaults to string    
}
