<?php
namespace Model\Entities;

/**
 * Users
 *
 * @Table(name="kps_users")
 * @Entity(repositoryClass="Model\Repositories\Users")
 */
class User
{
    /**
     * @var integer
     *
     * @Column(name="user_id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * @var string
     *
     * @Column(name="user_group", type="string", length=10, nullable=false)
     */
    private $userGroup;

    /**
     * @var string
     *
     * @Column(name="user_first_name", type="string", length=255, nullable=true)
     */
    private $userFirstName;

    /**
     * @var string
     *
     * @Column(name="user_last_name", type="string", length=255, nullable=true)
     */
    private $userLastName;

    /**
     * @var string
     *
     * @Column(name="user_email", type="string", length=255, nullable=false)
     */
    private $userEmail;

    /**
     * @var string
     *
     * @Column(name="user_password", type="string", length=255, nullable=false)
     */
    private $userPassword;

    /**
     * @var \DateTime
     *
     * @Column(name="user_registration_date", type="datetime", nullable=false)
     */
    private $userRegistrationDate;

    /**
     * @var boolean
     *
     * @Column(name="user_status", type="boolean", nullable=true)
     */
    private $userStatus;



    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userGroup
     *
     * @param string $userGroup
     * @return Users
     */
    public function setUserGroup($userGroup)
    {
        $this->userGroup = $userGroup;
    
        return $this;
    }

    /**
     * Get userGroup
     *
     * @return string 
     */
    public function getUserGroup()
    {
        return $this->userGroup;
    }

    /**
     * Set userFirstName
     *
     * @param string $userFirstName
     * @return Users
     */
    public function setUserFirstName($userFirstName)
    {
        $this->userFirstName = $userFirstName;
    
        return $this;
    }

    /**
     * Get userFirstName
     *
     * @return string 
     */
    public function getUserFirstName()
    {
        return $this->userFirstName;
    }

    /**
     * Set userLastName
     *
     * @param string $userLastName
     * @return Users
     */
    public function setUserLastName($userLastName)
    {
        $this->userLastName = $userLastName;
    
        return $this;
    }

    /**
     * Get userLastName
     *
     * @return string 
     */
    public function getUserLastName()
    {
        return $this->userLastName;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     * @return Users
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    
        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string 
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set userPassword
     *
     * @param string $userPassword
     * @return Users
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;
    
        return $this;
    }

    /**
     * Get userPassword
     *
     * @return string 
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * Set userRegistrationDate
     *
     * @param \DateTime $userRegistrationDate
     * @return Users
     */
    public function setUserRegistrationDate($userRegistrationDate)
    {
        $this->userRegistrationDate = $userRegistrationDate;
    
        return $this;
    }

    /**
     * Get userRegistrationDate
     *
     * @return \DateTime 
     */
    public function getUserRegistrationDate()
    {
        return $this->userRegistrationDate;
    }

    /**
     * Set userStatus
     *
     * @param boolean $userStatus
     * @return Users
     */
    public function setUserStatus($userStatus)
    {
        $this->userStatus = $userStatus;
    
        return $this;
    }

    /**
     * Get userStatus
     *
     * @return boolean 
     */
    public function getUserStatus()
    {
        return $this->userStatus;
    }
}