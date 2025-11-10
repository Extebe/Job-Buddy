<?php

require_once "include.php";

class Note {
    private ?string $id;
    private ?int $valeur; //Note entre 0 et 5
    private ?string $commentaire;
    private ?Utilisateur $auteur;
    private ?Utilisateur $receveur;
    private ?Annonce $annonce;

    // Constructeur
    public function __construct(string|null $id = null, int|null $valeur = null, string|null $commentaire = null, Utilisateur|null $auteur = null, Utilisateur|null $receveur = null, Annonce|null $annonce = null) {
        $this->id = $id;
        $this->valeur = $valeur;
        $this->commentaire = $commentaire;
        $this->auteur = $auteur;
        $this->receveur = $receveur;
        $this->annonce = $annonce;
    }

    // Getters et Setters
    public function getId(): ?string {
        return $this->id;
    }

    public function getValeur(): ?int {
        return $this->valeur;
    }

    public function getCommentaire(): ?string {
        return $this->commentaire;
    }

    public function getAuteur(): ?Utilisateur {
        return $this->auteur;
    }

    public function getReceveur(): ?Utilisateur {
        return $this->receveur;
    }

    public function getAnnonce(): ?Annonce {
        return $this->annonce;
    }

    public function setId(?string $id): void {
        $this->id = $id;
    }

    public function setValeur(?int $valeur): void {
        $this->valeur = $valeur;
    }

    public function setCommentaire(?string $commentaire): void {
        $this->commentaire = $commentaire;
    }

    public function setAuteur(?Utilisateur $auteur): void {
        $this->auteur = $auteur;
    }

    public function setReceveur(?Utilisateur $receveur): void {
        $this->receveur = $receveur;
    }

    public function setAnnonce(?Annonce $annonce): void {
        $this->annonce = $annonce;
    }


    
    
}

