<?php
require_once "include.php";

Class NewLetter{
    private ?int $id;
    private ?string $email;

    public function __construct(?int $id=null, ?string $email=null){
        $this->id= $id;
        $this->email= $email;
    }

    /**
     * Get the value of id
     * @return  self
     */ 
    public function getId():int
    {
        return $this->id;
    }

    /**
     * Set the value of id 
     */ 
    public function setId($id):void
    {
        $this->id = $id;
    }

    /**
     * Get the value of email
     * @return  self
     */ 
    public function getEmail():string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     */ 
    public function setEmail($email):void
    {
        $this->email = $email;
    }
}