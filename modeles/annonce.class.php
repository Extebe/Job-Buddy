<?php

require_once "include.php"; 

class Annonce{
    private ?string $id;
    private ?Particulier $idParticulier;
    private array $postulations = [];
    private array $etuditantsSelectionnes = [];

    private ?string $titre;
    private ?string $description;
    private ?string $typeService;
    private ?string $lieu;
    private ?float $remuneration;

    private ?string $dateDebutRealisation;
    private ?string $dateFinRealisation;

    private ?string $etat;

    private ?string $datePublication;
    private ?string $dateSuppression;
    private ?string $motifSuppression;


    public function __construct(
        ?string $id = null,
        ?int $idParticulier = null,

        ?string $titre = null,
        ?string $description = null,
        ?string $typeService = null,
        ?string $lieu = null,
        ?float $remuneration = null,

        ?string $dateDebutRealisation = null,
        ?string $dateFinRealisation = null,

        ?string $etat = null,

        ?string $datePublication = null,
        ?string $dateSuppression = null,
        ?string $motifSuppression = null,

    ) {
        $this->id = $id;
        $particulierDAO = new ParticulierDAO(Bd::getInstance()->getConnexion());
        $this->idParticulier = $particulierDAO->find($idParticulier);

        $this->titre = $titre;
        $this->description = $description;
        $this->typeService = $typeService;
        $this->lieu = $lieu;
        $this->remuneration = $remuneration;

        $this->dateDebutRealisation = $dateDebutRealisation;
        $this->dateFinRealisation = $dateFinRealisation;

        $this->etat = $etat;

        $this->datePublication = $datePublication;
        $this->dateSuppression = $dateSuppression;
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
    public function setId(?string $id = null): void
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
     * Get the value of postulations
     */
    public function getPostulations(): array
    {
        return $this->postulations;
    }

    /**
     * Set the value of postulations
     */
    public function setPostulations(?array $postulations): void
    {
        $this->postulations = $postulations;
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
    public function getCreateur(): ?Particulier
    {
        return $this->idParticulier;
    }

    /**
     * @param string|null $createur
     */
    public function setCreateur(?Particulier $createur): void
    {
        $this->idParticulier = $createur;
    }

    /**
     * @return float|null
     */
    public function getRemuneration(): ?float
    {
        return $this->remuneration;
    }

    /**
     * @param float|null $remuneration
     */
    public function setRemuneration(?float $remuneration): void
    {
        $this->remuneration = $remuneration;
    }

    /**
     * @return string|null
     */
    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    /**
     * @param string|null $lieu
     */
    public function setLieu(?string $lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return array|null
     */
    public function getEtuditantsSelectionnes(): ?array
    {
        return $this->etuditantsSelectionnes;
    }  

    /**
     * @param array|null $etuditantsSelectionnes
     */
    public function setEtuditantsSelectionnes(?array $etuditantsSelectionnes): void
    {
        $this->etuditantsSelectionnes = $etuditantsSelectionnes;
    }

   /* public function delierParticulier(){
        if($this->getCreateur() != null){
            $particulier = $this->getCreateur();
            $this->setcreateur(null);
            $particulier->delierAnnoncePublie($this);
        }
    }

    public function lierParticulier($p){
        $this->delierParticulier();
        $this->setcreateur($p);
        $p->lierAnnoncePublie($this);
    }
*/
    public function __toString(): string
    {return $this->getId() . $this->getCreateur();}

    public static function getAnnonce(): ?Annonce{
        if(isset($_SESSION['id'])){
            $bd = Bd::getInstance();
            $pdo = $bd->getConnexion();
            $annonceDao=new AnnonceDao($pdo);
            $annonce = $annonceDao->findAllAssoc();
            $annonceHydrate=$annonceDao->hydrate($annonce[1]);
            return $annonceHydrate;
        }
        return null;
    }
    
}

?>