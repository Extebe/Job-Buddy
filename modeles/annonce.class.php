<?php

require_once "include.php";

/**
 * @brief Classe représentant une annonce de job.
 */
class Annonce
{

    /** @var int|null $id Identifiant unique de l'annonce. */
    private ?int $id;
    /** @var Particulier|null $particulier Le particulier créateur de l'annonce. */
    private ?Particulier $particulier;

    /** @var array $postulations Liste des étudiants ayant postulé. */
    private array $postulations = [];
    /** @var array $etudiantsSelectionnes Liste des étudiants sélectionnés. */
    private array $etudiantsSelectionnes = [];

    /** @var string|null $titre Titre de l'annonce. */
    private ?string $titre;
    /** @var string|null $description Description détaillée. */
    private ?string $description;
    /** @var string|null $typeService Type de service (ex: Jardinage, Cours). */
    private ?string $typeService;
    /** @var string|null $lieu Lieu de la mission. */
    private ?string $lieu;
    /** @var float|null $remuneration Rémunération proposée. */
    private ?float $remuneration;

    /** @var string|null $dateDebutRealisation Date de début. */
    private ?string $dateDebutRealisation;
    /** @var string|null $dateFinRealisation Date de fin. */
    private ?string $dateFinRealisation;

    /** @var string|null $etat État de l'annonce (ex: PUBLIEE, POURVUE). */
    private ?string $etat;

    /** @var string|null $datePublication Date de publication. */
    private ?string $datePublication;
    /** @var string|null $dateSuppression Date de suppression. */
    private ?string $dateSuppression;
    /** @var string|null $motifSuppression Motif de suppression. */
    private ?string $motifSuppression;

    /**
     * @brief Constructeur de la classe Annonce.
     * @param int|null $id Identifiant.
     * @param Particulier|null $particulier Créateur.
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
        ?int $id = null,
        ?Particulier $particulier = null,

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
        $this->particulier = $particulier;

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
     * @brief Récupère l'identifiant.
     * @return string|null L'identifiant (casté en string ou int selon implémentation, ici typehinté string).
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @brief Définit l'identifiant.
     * @param string|null $id L'identifiant.
     */
    public function setId(?string $id = null): void
    {
        $this->id = $id;
    }

    /**
     * @brief Récupère le titre.
     * @return string|null Le titre.
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * @brief Définit le titre.
     * @param string|null $titre Le titre.
     */
    public function setTitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * @brief Récupère la description.
     * @return string|null La description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @brief Définit la description.
     * @param string|null $description La description.
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @brief Récupère l'état.
     * @return string|null L'état.
     */
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    /**
     * @brief Définit l'état.
     * @param string|null $etat L'état.
     */
    public function setEtat(?string $etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @brief Récupère le type de service.
     * @return string|null Le type de service.
     */
    public function getTypeService(): ?string
    {
        return $this->typeService;
    }

    /**
     * @brief Définit le type de service.
     * @param string|null $typeService Le type de service.
     */
    public function setTypeService(?string $typeService): void
    {
        $this->typeService = $typeService;
    }

    /**
     * @brief Récupère la date de publication.
     * @return string|null La date de publication.
     */
    public function getDatePublication(): ?string
    {
        return $this->datePublication;
    }

    /**
     * @brief Définit la date de publication.
     * @param string|null $datePublication La date de publication.
     */
    public function setDatePublication(?string $datePublication): void
    {
        $this->datePublication = $datePublication;
    }

    /**
     * @brief Récupère la date de début de réalisation.
     * @return string|null La date de début.
     */
    public function getDateDebutRealisation(): ?string
    {
        return $this->dateDebutRealisation;
    }

    /**
     * @brief Définit la date de début de réalisation.
     * @param string|null $dateDebutRealisation La date de début.
     */
    public function setDateDebutRealisation(?string $dateDebutRealisation): void
    {
        $this->dateDebutRealisation = $dateDebutRealisation;
    }

    /**
     * @brief Récupère la date de fin de réalisation.
     * @return string|null La date de fin.
     */
    public function getDateFinRealisation(): ?string
    {
        return $this->dateFinRealisation;
    }

    /**
     * @brief Définit la date de fin de réalisation.
     * @param string|null $dateFinRealisation La date de fin.
     */
    public function setDateFinRealisation(?string $dateFinRealisation): void
    {
        $this->dateFinRealisation = $dateFinRealisation;
    }

    /**
     * @brief Récupère la liste des postulations.
     * @return array La liste des postulations.
     */
    public function getPostulations(): array
    {
        return $this->postulations;
    }

    /**
     * @brief Définit la liste des postulations.
     * @param array|null $postulations La liste des postulations.
     */
    public function setPostulations(?array $postulations): void
    {
        $this->postulations = $postulations;
    }

    /**
     * @brief Récupère le motif de suppression.
     * @return string|null Le motif.
     */
    public function getMotifSuppression(): ?string
    {
        return $this->motifSuppression;
    }

    /**
     * @brief Définit le motif de suppression.
     * @param string|null $motifSuppression Le motif.
     */
    public function setMotifSuppression(?string $motifSuppression): void
    {
        $this->motifSuppression = $motifSuppression;
    }

    /**
     * @brief Récupère la date de suppression.
     * @return string|null La date de suppression.
     */
    public function getDateSuppression(): ?string
    {
        return $this->dateSuppression;
    }

    /**
     * @brief Définit la date de suppression.
     * @param string|null $dateSuppression La date de suppression.
     */
    public function setDateSuppression(?string $dateSuppression): void
    {
        $this->dateSuppression = $dateSuppression;
    }

    /**
     * @brief Récupère le créateur de l'annonce.
     * @return Particulier|null Le créateur.
     */
    public function getCreateur(): ?Particulier
    {
        return $this->particulier;
    }

    /**
     * @brief Définit le créateur de l'annonce.
     * @param Particulier|null $createur Le créateur.
     */
    public function setCreateur(?Particulier $createur): void
    {
        $this->particulier = $createur;
    }

    /**
     * @brief Récupère la rémunération.
     * @return float|null La rémunération.
     */
    public function getRemuneration(): ?float
    {
        return $this->remuneration;
    }

    /**
     * @brief Définit la rémunération.
     * @param float|null $remuneration La rémunération.
     */
    public function setRemuneration(?float $remuneration): void
    {
        $this->remuneration = $remuneration;
    }

    /**
     * @brief Récupère le lieu.
     * @return string|null Le lieu.
     */
    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    /**
     * @brief Définit le lieu.
     * @param string|null $lieu Le lieu.
     */
    public function setLieu(?string $lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @brief Récupère les étudiants sélectionnés.
     * @return array|null Les étudiants sélectionnés.
     */
    public function getEtuditantsSelectionnes(): ?array
    {
        return $this->etudiantsSelectionnes;
    }

    /**
     * @brief Définit les étudiants sélectionnés.
     * @param array|null $etuditantsSelectionnes Les étudiants sélectionnés.
     */
    public function setEtuditantsSelectionnes(?array $etuditantsSelectionnes): void
    {
        $this->etudiantsSelectionnes = $etuditantsSelectionnes;
    }

    public function __toString(): string
    {
        return $this->getId() . $this->getCreateur();
    }

    /**
     * @brief Récupère une annonce spécifique si possible (méthode de test probablement).
     * @return Annonce|null L'annonce trouvée ou null.
     */
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

    public function aPostule(int $etudiantId): bool
    {
        $managerAnnonce = new AnnonceDAO(Bd::getInstance()->getConnexion());
        $managerAnnonce->addRelations($this);
        return in_array($etudiantId, $this->postulations);
    }

}