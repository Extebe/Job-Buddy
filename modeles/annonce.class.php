<?php

require_once "include.php"; 

class Annonce{
    private ?string $id;
    private ?string $titre;
    private ?string $description;
    private ?int $etat; // int qui correspond à un état
    private ?int $typeService; // int qui correspond à un service
    private ?string $datePublication;
    private ?string $dateDebut;
    private ?string $dateFin;
    private ?array $assocEtudiantDate; // Demander au prof pour le type
    private ?Particulier $auteur;
    private ?string $motifSuppression; 

    public function __construct(?string $id=null, ?string $titre=null, ?string $description=null, ?int $etat=null, ?int $typeService=null, ?string $datePublication=null, ?string $dateDebut=null, ?string $dateFin=null, ?array $assocEtudiantDate=null, ?Particulier $auteur=null, ?string $motifSuppression=null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->etat = $etat;
        $this->typeService = $typeService;
        $this->datePublication = $datePublication;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->assocEtudiantDate = $assocEtudiantDate;
        $this->auteur = $auteur;
        $this->motifSuppression = $motifSuppression;
    }

    /**
     * Get the value of id
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the value of titre
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Set the value of titre
     */
    public function setTitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get the value of etat
     */
    public function getEtat(): ?int
    {
        return $this->etat;
    }

    /**
     * Set the value of etat
     */
    public function setEtat(?int $etat): void
    {
        $this->etat = $etat;
    }

    /**
     * Get the value of typeService
     */
    public function getTypeService(): ?int
    {
        return $this->typeService;
    }

    /**
     * Set the value of typeService
     */
    public function setTypeService(?int $typeService): void
    {
        $this->typeService = $typeService;
    }

    /**
     * Get the value of datePublication
     */
    public function getDatePublication(): ?string
    {
        return $this->datePublication;
    }

    /**
     * Set the value of datePublication
     */
    public function setDatePublication(?string $datePublication): void
    {
        $this->datePublication = $datePublication;
    }

    /**
     * Get the value of dateDebut
     */
    public function getDateDebut(): ?string
    {
        return $this->dateDebut;
    }

    /**
     * Set the value of dateDebut
     */
    public function setDateDebut(?string $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * Get the value of dateFin
     */
    public function getDateFin(): ?string
    {
        return $this->dateFin;
    }

    /**
     * Set the value of dateFin
     */
    public function setDateFin(?string $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    /**
     * Get the value of assocEtudiantDate
     */
    public function getAssocEtudiantDate(): ?array
    {
        return $this->assocEtudiantDate;
    }

    /**
     * Set the value of assocEtudiantDate
     */
    public function setAssocEtudiantDate(?array $assocEtudiantDate): void
    {
        $this->assocEtudiantDate = $assocEtudiantDate;
    }

    /**
     * Get the value of auteur
     */
    public function getAuteur(): ?Particulier
    {
        return $this->auteur;
    }

    /**
     * Set the value of auteur
     */
    public function setAuteur(?Particulier $auteur): void
    {
        $this->auteur = $auteur;
    }

    /**
     * Get the value of motifSuppression
     */
    public function getMotifSuppression(): ?string
    {
        return $this->motifSuppression;
    }

    /**
     * Set the value of motifSuppression
     */
    public function setMotifSuppression(?string $motifSuppression): void
    {
        $this->motifSuppression = $motifSuppression;
    }
}

?>