<?php
namespace Application\Model;

class Customer 
{
    private $id;
    private $name;
    private $appUserId;
    private $status;
    
    public function __construct($name, $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }
    
    public function getId() 
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getAppUserId()
    {
        return $this->appUserId;
    }
    
    public function setAppUserId($appUserId)
    {
        $this->appUserId = $appUserId;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
    }
}