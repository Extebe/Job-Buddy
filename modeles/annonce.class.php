<?php

require_once "include.php"; 

class Annonce{
    private ?string $id;
    private ?string $titre;
    private ?string $description;
    private ?string $etat;
    private ?string $typeService;
    private ?string $datePublication;
    private ?string $dateDebutRealisation;
    private ?string $dateFinRealisation;
    private ?array $assocEtudiantDate;
    private ?string $motifSuppression;
    private ?string $dateSuppression;
    private ?string $idParticulier;

    public function __construct(?string $id=null, ?string $titre=null, ?string $description=null, ?string $etat=null, ?string $typeService=null, ?string $datePublication=null, ?string $dateDebutRealisation=null, ?string $dateFinRealisation=null, ?array $assocEtudiantDate=null, ?Particulier $auteur=null, ?string $motifSuppression=null, ?string $dateSuppression=null, ?string $idParticulier=null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->etat = $etat;
        $this->typeService = $typeService;
        $this->datePublication = $datePublication;
        $this->dateDebutRealisation = $dateDebutRealisation;
        $this->dateFinRealisation = $dateFinRealisation;
        $this->assocEtudiantDate = $assocEtudiantDate;
        $this->motifSuppression = $motifSuppression;
        $this->dateSuppression = $dateSuppression;
        $this->idParticulier = $idParticulier;
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
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    /**
     * Set the value of etat
     */
    public function setEtat(?string $etat): void
    {
        $this->etat = $etat;
    }

    /**
     * Get the value of typeService
     */
    public function getTypeService(): ?string
    {
        return $this->typeService;
    }

    /**
     * Set the value of typeService
     */
    public function setTypeService(?string $typeService): void
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
     * Get the value of dateDebutRealisation
     */
    public function getDateDebutRealisation(): ?string
    {
        return $this->dateDebutRealisation;
    }

    /**
     * Set the value of dateDebutRealisation
     */
    public function setDateDebutRealisation(?string $dateDebutRealisation): void
    {
        $this->dateDebutRealisation = $dateDebutRealisation;
    }

    /**
     * Get the value of dateFinRealisation
     */
    public function getDateFinRealisation(): ?string
    {
        return $this->dateFinRealisation;
    }

    /**
     * Set the value of dateFinRealisation
     */
    public function setDateFinRealisation(?string $dateFinRealisation): void
    {
        $this->dateFinRealisation = $dateFinRealisation;
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

    /**
     * @return string|null
     */
    public function getDateSuppression(): ?string
    {
        return $this->dateSuppression;
    }

    /**
     * @param string|null $dateSuppression
     */
    public function setDateSuppression(?string $dateSuppression): void
    {
        $this->dateSuppression = $dateSuppression;
    }

    /**
     * @return string|null
     */
    public function getIdParticulier(): ?string
    {
        return $this->idParticulier;
    }

    public function delierParticulier(){
        if($this->getAuteur() != null){
            $p = $this->getAuteur();
            $this->setAuteur(null);
            $p->delierAnnoncePublie($this);
        }
    }

    public function lierParticulier($p){
        $this->delierParticulier();
        $this->setAuteur($p);
        $p->lierAnnoncePublie($this);
    }

    public function __toString(): string
    {return $this->getId() . $this->getAuteur();}
}

?>