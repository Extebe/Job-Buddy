<?php

require_once "include.php";

class Utilisateur{
    private ?string $id;
    private ?string $nom;
    private ?string $prenom;
    private ?string $telephone;
    private ?string $dateNaiss;
    private ?string $email;
    private ?string $mdp;
    private ?string $adresse;
    private ?string $ville;
    private ?string $codePostal;

    public function __construct(?string $id = null, ?string $nom = null, ?string $prenom = null, ?string $telephone = null, ?string $dateNaiss = null, ?string $email = null, ?string $mdp = null, ?string $adresse = null, ?string $ville = null, ?string $codePostal = null){
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->telephone = $telephone;
        $this->dateNaiss = $dateNaiss;
        $this->email = $email;
        $this->mdp = $mdp;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->codePostal = $codePostal;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the value of nom
     */ 
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     */ 
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * Get the value of prenom
     */ 
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     */ 
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * Get the value of telephone
     */ 
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     */ 
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Get the value of dateNaiss
     */ 
    public function getDateNaiss()
    {
        return $this->dateNaiss;
    }

    /**
     * Set the value of dateNaiss
     */ 
    public function setDateNaiss($dateNaiss)
    {
        $this->dateNaiss = $dateNaiss;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */ 
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get the value of mdp
     */ 
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * Set the value of mdp
     */ 
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    /**
     * Get the value of adresse
     */ 
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set the value of adresse
     */ 
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * Get the value of ville
     */ 
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set the value of ville
     */ 
    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    /**
     * Get the value of codePostal
     */ 
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set the value of codePostal
     */ 
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
    }
}
?>