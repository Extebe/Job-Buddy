<?php

require_once "include.php";

/**
 * @brief Classe représentant une note attribuée.
 */
class Note
{
    /** @var int|null $id Identifiant de la note. */
    private ?int $id;
    /** @var int|null $valeur Valeur de la note (de 0 à 5). */
    private ?int $valeur; //Note entre 0 et 5
    /** @var string|null $commentaire Commentaire associé à la note. */
    private ?string $commentaire;
    /** @var Utilisateur|null $auteur Auteur de la note. */
    private ?Utilisateur $auteur;
    /** @var Utilisateur|null $receveur Bénéficiaire de la note. */
    private ?Utilisateur $receveur;
    /** @var Annonce|null $annonce Annonce concernée. */
    private ?Annonce $annonce;

    /**
     * @brief Constructeur de la classe Note.
     * @param int|null $id Identifiant.
     * @param int|null $valeur Valeur.
     * @param string|null $commentaire Commentaire.
     * @param Utilisateur|null $auteur Auteur.
     * @param Utilisateur|null $receveur Receveur.
     * @param Annonce|null $annonce Annonce.
     */
    public function __construct(?int $id = null, ?int $valeur = null, ?string $commentaire = null, ?Utilisateur $auteur = null, ?Utilisateur $receveur = null, ?Annonce $annonce = null)
    {
        $this->id = $id;
        $this->valeur = $valeur;
        $this->commentaire = $commentaire;
        $this->auteur = $auteur;
        $this->receveur = $receveur;
        $this->annonce = $annonce;
    }

    // Getters et Setters
    /**
     * @brief Récupère l'ID.
     * @return int|null L'identifiant.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @brief Récupère la valeur.
     * @return int|null La valeur.
     */
    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    /**
     * @brief Récupère le commentaire.
     * @return string|null Le commentaire.
     */
    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    /**
     * @brief Récupère l'auteur.
     * @return Utilisateur|null L'auteur.
     */
    public function getAuteur(): ?Utilisateur
    {
        return $this->auteur;
    }

    /**
     * @brief Récupère le receveur.
     * @return Utilisateur|null Le receveur.
     */
    public function getReceveur(): ?Utilisateur
    {
        return $this->receveur;
    }

    /**
     * @brief Récupère l'annonce.
     * @return Annonce|null L'annonce.
     */
    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    /**
     * @brief Définit l'ID.
     * @param string|null $id L'identifiant.
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @brief Définit la valeur.
     * @param int|null $valeur La valeur.
     */
    public function setValeur(?int $valeur): void
    {
        $this->valeur = $valeur;
    }

    /**
     * @brief Définit le commentaire.
     * @param string|null $commentaire Le commentaire.
     */
    public function setCommentaire(?string $commentaire): void
    {
        $this->commentaire = $commentaire;
    }

    /**
     * @brief Définit l'auteur.
     * @param Utilisateur|null $auteur L'auteur.
     */
    public function setAuteur(?Utilisateur $auteur): void
    {
        $this->auteur = $auteur;
    }

    /**
     * @brief Définit le receveur.
     * @param Utilisateur|null $receveur Le receveur.
     */
    public function setReceveur(?Utilisateur $receveur): void
    {
        $this->receveur = $receveur;
    }

    /**
     * @brief Définit l'annonce.
     * @param Annonce|null $annonce L'annonce.
     */
    public function setAnnonce(?Annonce $annonce): void
    {
        $this->annonce = $annonce;
    }

    public function __toString(): string
    {
        return "Note [id=" . $this->id . ", valeur=" . $this->valeur . ", commentaire=" . $this->commentaire .
            ", auteur=" . ($this->auteur ? $this->auteur->getNom() : "null") .
            ", receveur=" . ($this->receveur ? $this->receveur->getNom() : "null") .
            ", annonce=" . ($this->annonce ? $this->annonce->getTitre() : "null") . "]";
    }


}

