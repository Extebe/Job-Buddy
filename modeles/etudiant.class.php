<?php

require_once "include.php";

class Etudiant extends Utilisateur{
    private ?string $codeINE;
    private array $annoncesPostule = [];

    public function __construct(
            ?int $id=null, 
            ?string $codeINE=null,
            ?string $nom=null, 
            ?string $prenom=null, 
            ?string $tel=null, 
            ?string $dateNaiss=null,
            ?string $email=null, 
            ?string $mdp=null, 
            ?string $adresse=null, 
            ?string $ville=null, 
            ?string $codePostal=null, 
            ?string $dateSuppression=null
        )
    {
        parent::__construct(
            $id, 
            $nom, 
            $prenom, 
            $tel, 
            $dateNaiss,
            $email, 
            $mdp, 
            $adresse, 
            $ville, 
            $codePostal, 
            $dateSuppression);
        $this->setCodeINE($codeINE);
    }

    /**
     * Get the value of codeEtudiant
     */ 
    public function getCodeINE()
    {
        return $this->codeINE;
    }

    /**
     * Set the value of codeEtudiant
     */ 
    public function setCodeINE($codeINE)
    {
        $this->codeINE = $codeINE;
    }
}