<?php

require_once "include.php";

/**
 * @brief Classe représentant une annonce.
 */
class Annonce
{
    /** @var string|null $id Identifiant de l'annonce. */
    private ?string $id;
    /** @var Particulier|null $idParticulier Identifiant du particulier créateur de l'annonce. */
    private ?Particulier $idParticulier;
    /** @var array $postulations Liste des postulations pour cette annonce. */
    private array $postulations = [];
    /** @var array $etuditantsSelectionnes Liste des étudiants sélectionnés. */
    private array $etuditantsSelectionnes = [];

    /** @var string|null $titre Titre de l'annonce. */
    private ?string $titre;
    /** @var string|null $description Description de l'annonce. */
    private ?string $description;
    /** @var string|null $typeService Type de service proposé. */
    private ?string $typeService;
    /** @var string|null $lieu Lieu de la mission. */
    private ?string $lieu;
    /** @var float|null $remuneration Rémunération proposée. */
    private ?float $remuneration;

    /** @var string|null $dateDebutRealisation Date de début de réalisation. */
    private ?string $dateDebutRealisation;
    /** @var string|null $dateFinRealisation Date de fin de réalisation. */
    private ?string $dateFinRealisation;

    /** @var string|null $etat État de l'annonce (ex: ouverte, fermée). */
    private ?string $etat;

    /** @var string|null $datePublication Date de publication de l'annonce. */
    private ?string $datePublication;
    /** @var string|null $dateSuppression Date de suppression de l'annonce. */
    private ?string $dateSuppression;
    /** @var string|null $motifSuppression Motif de suppression. */
    private ?string $motifSuppression;


    /**
     * @brief Constructeur de la classe Annonce.
     * @param string|null $id Identifiant.
     * @param int|null $idParticulier ID du particulier.
     * @param string|null $titre Titre.
     * @param string|null $description Description.
     * @param string|null $typeService Type de service.
     * @param string|null $lieu Lieu.
     * @param float|null $remuneration Rémunération.
     * @param string|null $dateDebutRealisation Date début.
     * @param string|null $dateFinRealisation Date fin.
     * @param string|null $etat État.
     * @param string|null $datePublication Date publication.
     * @param string|null $dateSuppression Date suppression.
     * @param string|null $motifSuppression Motif suppression.
     */
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
    {
        return $this->getId() . $this->getCreateur();
    }

    public static function getAnnonce(): ?Annonce
    {
        if (isset($_SESSION['id'])) {
            $bd = Bd::getInstance();
            $pdo = $bd->getConnexion();
            $annonceDao = new AnnonceDao($pdo);
            $annonce = $annonceDao->findAllAssoc();
            $annonceHydrate = $annonceDao->hydrate($annonce[1]);
            return $annonceHydrate;
        }
        return null;
    }

}

?>