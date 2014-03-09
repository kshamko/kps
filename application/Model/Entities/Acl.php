<?php
namespace Model\Entities;

/**
* @Entity(repositoryClass="Model\Repositories\Acl")
* @Table(name="kps_acl")
*/
class Acl{
    
    /** 
     * @Id @Column(type="integer") 
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $acl_id;
    
    /** @Column(length=100) */
    private $acl_resource; // type defaults to string    
    
    /** @Column(length=30) */
    private $acl_group; // type defaults to string    

    /**
     * Set acl_id
     *
     * @param integer $aclId
     * @return Acl
     */
    public function setAclId($aclId)
    {
        $this->acl_id = $aclId;
    
        return $this;
    }

    /**
     * Get acl_id
     *
     * @return integer 
     */
    public function getAclId()
    {
        return $this->acl_id;
    }

    /**
     * Set acl_resource
     *
     * @param string $aclResource
     * @return Acl
     */
    public function setAclResource($aclResource)
    {
        $this->acl_resource = $aclResource;
    
        return $this;
    }

    /**
     * Get acl_resource
     *
     * @return string 
     */
    public function getAclResource()
    {
        return $this->acl_resource;
    }

    /**
     * Set acl_group
     *
     * @param string $aclGroup
     * @return Acl
     */
    public function setAclGroup($aclGroup)
    {
        $this->acl_group = $aclGroup;
    
        return $this;
    }

    /**
     * Get acl_group
     *
     * @return string 
     */
    public function getAclGroup()
    {
        return $this->acl_group;
    }
}