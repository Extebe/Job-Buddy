<?php

require_once "include.php";

class Etudiant extends Utilisateur{
    private ?string $codeEtudiant;
    private $listAnnoncePostule = [];

    public function __construct(?string $id = null, ?string $nom = null, ?string $codeEtudiant = null, ?string $prenom = null, ?string $telephone = null, ?string $dateNaiss = null, ?string $email = null, ?string $mdp = null, ?string $adresse = null, ?string $ville = null, ?string $codePostal = null){
        $this->id = $id;
        $this->codeEtudiant = $codeEtudiant;
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
}