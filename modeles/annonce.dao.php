<?php
require_once "include.php";


class AnnonceDao
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the value of pdo
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * Set the value of pdo
     */
    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Trouve une annonce par son identifiant.
     * @param string|null $id Identifiant de l'annonce.
     * @return Annonce|boolean L'annonce correspondante ou false si non trouvée.
     */
    public function find(?string $id): mixed
    {
        $sql = "SELECT * FROM Annonce WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Annonce');
        $annonce = $pdoStatement->fetch();
        return $annonce;
    }

    /**
     * @brief Récupère toutes les annonces.
     * @return array Tableau d'annonces.
     */
    public function findAll()
    {
        $sql = "SELECT * FROM Annonce";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Annonce');
        $annonce = $pdoStatement->fetchAll();
        return $annonce;
    }

    /**
     * @brief Récupère toutes les annonces associées à un utilisateur (créateur ou postulant).
     * @param mixed $utilisateur Identifiant de l'utilisateur.
     * @return array Tableau d'annonces.
     */
    public function findAllById($utilisateur)
    {
        $sql = "SELECT * FROM Annonce LEFT JOIN POSTULER ON Annonce.id=POSTULER.idAnnonce WHERE Annonce.idParticulier = :idParticulier OR POSTULER.idEtudiant = :idParticulier";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idParticulier" => $utilisateur));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Annonce');
        $annonce = $pdoStatement->fetchAll();
        return $annonce;
    }

    /**
     * @brief Récupère toutes les annonces sous forme de tableau associatif.
     * @return array Tableau associatif des annonces.
     */
    public function findAllAssoc()
    {
        $sql = "SELECT * FROM Annonce";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetchAll();
        return $annonce;
    }

    /**
     * @brief Hydrate un objet Annonce à partir d'un tableau associatif.
     * @param array $tableauAssoc Tableau de données.
     * @return Annonce|null L'objet Annonce hydraté.
     */
    public function hydrate($tableauAssoc): ?Annonce
    {
        $annonce = new Annonce(
            $tableauAssoc['id'],
            $tableauAssoc['idParticulier'],
            $tableauAssoc['titre'],
            $tableauAssoc['description'],
            $tableauAssoc['typeService'],
            $tableauAssoc['lieu'],
            $tableauAssoc['remuneration'],
            $tableauAssoc['dateDebutRealisation'],
            $tableauAssoc['dateFinRealisation'],
            $tableauAssoc['etat'],
            $tableauAssoc['datePublication'],
            $tableauAssoc['dateSuppression'],
            $tableauAssoc['motifSuppression']
        );

        return $annonce;
    }

    /**
     * @brief Hydrate un tableau d'annonces.
     * @param array $tableau Tableau de tableaux associatifs.
     * @return array|null Tableau d'objets Annonce.
     */
    public function hydrateAll($tableau): ?array
    {
        $categories = [];
        foreach ($tableau as $tableauAssoc) {
            $categorie = $this->hydrate($tableauAssoc);
            $categories[] = $categorie;
        }
        return $categories;
    }

    /**
     * @brief Ajoute les relations (postulations) à une annonce.
     * @param Annonce $annonce L'annonce à compléter.
     * @return Annonce L'annonce avec ses relations.
     */
    public function addRelations(Annonce $annonce): Annonce
    {
        // Creation de la liste de postulations
        $sql = "SELECT idEtudiant, datePostulat FROM Annonce WHERE idAnnonce= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $annonce->getId()));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $assocPostulations = $pdoStatement->fetch();

        $etudiantDao = new EtudiantDao($this->pdo);
        $postulations = [];
        foreach ($assocPostulations as $assocPostulation) {
            $etudiant = $etudiantDao->find($assocPostulation['idEtudiant']);
            $postulations[] = ['etudiant' => $etudiant, 'date' => $assocPostulation['datePostulat']];
        }
        $annonce->setPostulations($postulations);

        return $annonce;
    }

    /**
     * @brief Insère une nouvelle annonce en base de données.
     * @param Annonce $annonce L'annonce à insérer.
     */
    public function insererAnnonce(Annonce $annonce): void
    {
        // Creation d'une annonce'
        $sql = "INSERT INTO Annonce (dateDebutRealisation, dateFinRealisation, etat, typeService, titre, description, datePublication, dataSuppression, motifSuppression, idParticulier) 
        VALUES (:dateDebutRealisation, :dateFinRealisation, :etat, :typeService, :titre, :description, :datePublication, :dataSuppression, :idParticulier )";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([]);
    }

    // TODO: Implement missing methods referenced in ControllerAnnonce:
    // - findAllByIdAndEtat($id, $etat)
    // - addSelectedStudents($annonce)
    // - postuler($idAnnonce, $idEtudiant)
    // - supprimer($idAnnonce, $idUser)
    // - accepterEtudiant($idAnnonce, $idEtudiant)
    // - refuserEtudiant($idAnnonce, $idEtudiant)
    // - modifier($annonce)
}