<?php
require_once "include.php";


/**
 * @brief DAO pour la gestion des annonces.
 */
class AnnonceDao
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Annonce.
     * @param PDO|null $pdo Instance de PDO.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'objet PDO.
     * @return PDO|null L'objet PDO.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit l'objet PDO.
     * @param PDO|null $pdo L'objet PDO.
     */
    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Trouve une annonce par son ID.
     * @param string|null $id ID de l'annonce.
     * @return Annonce|null L'annonce trouvée ou null.
     */
    public function find(?string $id): ?Annonce
    {
        $sql = "SELECT * FROM Annonce WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetch();
        $annonce = $this->hydrate($annonce);
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
     * @brief Récupère toutes les annonces liées à un utilisateur (créées ou postulées).
     * @param int $utilisateur ID de l'utilisateur.
     * @return array Tableau d'annonces.
     */
    public function findAllById($utilisateur)
    {
        $sql = "SELECT DISTINCT Annonce.* FROM Annonce LEFT JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE Annonce.idParticulier = :idParticulier OR Postuler.idEtudiant = :idParticulier";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idParticulier" => $utilisateur));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetchAll();
        $annonce = $this->hydrateAll($annonce);
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
     * @brief Récupère toutes les annonces disponibles sous forme de tableau associatif.
     * @return array Tableau associatif des annonces disponibles.
     */
    public function findAllAssocDispo()
    {
        $sql = "SELECT id, idParticulier,titre, description, typeService, lieu, remuneration, dateDebutRealisation, dateFinRealisation, etat, datePublication, dateSuppression, motifSuppression,COUNT(Postuler.idEtudiant) as nbEtudiant
        FROM Annonce LEFT JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE etat = 'disponible' GROUP BY id ORDER BY typeService,nbEtudiant DESC ;";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetchAll();
        return $annonce;
    }

    /**
     * @brief Hydrate un objet Annonce à partir d'un tableau associatif.
     * @param array $tableauAssoc Données de l'annonce.
     * @return Annonce|null L'objet Annonce hydraté.
     */
    public function hydrate($tableauAssoc): ?Annonce
    {
        $manageurParticulier = new ParticulierDao($this->pdo);
        $particulier = $manageurParticulier->find($tableauAssoc['idParticulier'] ?? null);
        $annonce = new Annonce(
            $tableauAssoc['id'] ?? null,
            $particulier,
            $tableauAssoc['titre'] ?? null,
            $tableauAssoc['description'] ?? null,
            $tableauAssoc['typeService'] ?? null,
            $tableauAssoc['lieu'] ?? null,
            $tableauAssoc['remuneration'] ?? null,
            $tableauAssoc['dateDebutRealisation'] ?? null,
            $tableauAssoc['dateFinRealisation'] ?? null,
            $tableauAssoc['etat'] ?? null,
            $tableauAssoc['datePublication'] ?? null,
            $tableauAssoc['dateSuppression'] ?? null,
            $tableauAssoc['motifSuppression'] ?? null
        );

        return $annonce;
    }

    /**
     * @brief Hydrate un tableau d'annonces.
     * @param array $tableau Tableau de données d'annonces.
     * @return array Tableau d'objets Annonce.
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
     * @brief Ajoute les relations (étudiants qui ont postulé) à une annonce.
     * @param Annonce $annonce L'annonce.
     * @return Annonce L'annonce avec les postulations.
     */
    public function addRelations(Annonce $annonce): Annonce
    {
        // Creation de la liste de postulations
        $sql = "SELECT Postuler.idEtudiant, Postuler.datePostulat FROM Annonce JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE Postuler.idAnnonce= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $annonce->getId()));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $assocPostulations = $pdoStatement->fetchAll();
        $etudiantDao = new EtudiantDao($this->pdo);
        $postulations = [];
        foreach ($assocPostulations as $assocPostulation) {
            $etudiant = $etudiantDao->find($assocPostulation['idEtudiant']);
            $postulations[] = $etudiant;
        }
        $annonce->setPostulations($postulations);

        return $annonce;
    }

    /**
     * @brief Ajoute les étudiants sélectionnés à une annonce.
     * @param Annonce $annonce L'annonce.
     * @return Annonce L'annonce avec les étudiants sélectionnés.
     */
    public function addSelectedStudents(Annonce $annonce): Annonce
    {
        // Creation de la liste des etudiants selectionnes
        $sql = "SELECT Postuler.idEtudiant, Postuler.datePostulat FROM Annonce JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE Postuler.idAnnonce= :id AND Postuler.estAccepte='1'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $annonce->getId()));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $assocEtudiants = $pdoStatement->fetchAll();
        $etudiantDao = new EtudiantDao($this->pdo);
        $etudiantsSelectionnes = [];
        foreach ($assocEtudiants as $assocEtudiant) {
            $etudiant = $etudiantDao->find($assocEtudiant['idEtudiant']);
            $etudiantsSelectionnes[] = $etudiant;
        }
        $annonce->setEtuditantsSelectionnes($etudiantsSelectionnes);

        return $annonce;
    }

    /**
     * @brief Insère une nouvelle annonce dans la base de données.
     * @param Annonce $annonce L'annonce à insérer.
     */
    public function insererAnnonce(Annonce $annonce)
    {
        $sql = "INSERT INTO Annonce (
    idParticulier, titre, description, typeService, lieu, remuneration,
    dateDebutRealisation, dateFinRealisation, etat, datePublication,
    dateSuppression, motifSuppression
) VALUES (
    :idParticulier, :titre, :description, :typeService, :lieu, :remuneration,
    :dateDebutRealisation, :dateFinRealisation, :etat, :datePublication,
    :dateSuppression, :motifSuppression
)";

        $pdoStatement = $this->pdo->prepare($sql);


        // Assure-toi d’avoir défini toutes ces variables avant execute
        $pdoStatement->execute([
            'idParticulier' => $annonce->getCreateur()->getId(),
            'titre' => $annonce->getTitre(),
            'description' => $annonce->getDescription(),
            'typeService' => lcfirst($annonce->getTypeService()),
            'lieu' => $annonce->getLieu(),
            'remuneration' => $annonce->getRemuneration(),
            'dateDebutRealisation' => $annonce->getDateDebutRealisation(),
            'dateFinRealisation' => $annonce->getDateFinRealisation(),
            'etat' => $annonce->getEtat(),
            'datePublication' => $annonce->getDatePublication(),
            'dateSuppression' => $annonce->getDateSuppression(),
            'motifSuppression' => $annonce->getMotifSuppression()
        ]);

    }

    /**
     * @brief Enregistre la candidature d'un étudiant à une annonce.
     * @param int $idAnnonce ID de l'annonce.
     * @param int $idEtudiant ID de l'étudiant.
     */
    public function postuler($idAnnonce, $idEtudiant)
    {
        // Postuler a une annonce
        $sql = "INSERT INTO Postuler (idAnnonce, idEtudiant, datePostulat, estAccepte) 
        VALUES (:idAnnonce, :idEtudiant, :datePostulat, '0')";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce" => $idAnnonce,
            "idEtudiant" => $idEtudiant,
            "datePostulat" => date("Y-m-d H:i:s")
        ));
    }

    /**
     * @brief Supprime une annonce (et ses dépendances).
     * @param int $idAnnonce ID de l'annonce.
     * @param int $idParticulier ID du propriétaire.
     * @throws Exception Si l'utilisateur n'est pas autorisé.
     */
    public function supprimer($idAnnonce, $idParticulier)
    {
        // Supprimer une annonce
        $sql1 = "SELECT idParticulier FROM Annonce WHERE id= :idAnnonce";
        $pdoStatement1 = $this->pdo->prepare($sql1);
        $pdoStatement1->execute(array("idAnnonce" => $idAnnonce));
        $pdoStatement1->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement1->fetch();
        if ($annonce['idParticulier'] != $idParticulier) {
            throw new Exception("Vous n'êtes pas autorisé à supprimer cette annonce.");
            exit();
        }
        $sql2 = "DELETE FROM Postuler WHERE idAnnonce = :idAnnonce";
        $pdoStatement2 = $this->pdo->prepare($sql2);
        $pdoStatement2->execute(array(
            "idAnnonce" => $idAnnonce
        ));
        $sql3 = "DELETE FROM Note WHERE idAnnonce = :idAnnonce";
        $pdoStatement3 = $this->pdo->prepare($sql3);
        $pdoStatement3->execute(array(
            "idAnnonce" => $idAnnonce
        ));
        $sql4 = "DELETE FROM SignalementAnonce WHERE idAnnonceSignale = :idAnnonce";
        $pdoStatement4 = $this->pdo->prepare($sql4);
        $pdoStatement4->execute(array(
            "idAnnonce" => $idAnnonce
        ));
        $sql = "DELETE FROM Annonce WHERE id = :idAnnonce";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce" => $idAnnonce
        ));
    }

    /**
     * @brief Met à jour les informations d'une annonce.
     * @param Annonce $annonce L'objet annonce modifié.
     * @todo Implémenter cette méthode (actuellement manquante).
     */
    public function modifier(Annonce $annonce)
    {
        $sql = "UPDATE Annonce SET titre = :titre, description = :description, typeService = :typeService, lieu = :lieu, remuneration = :remuneration, dateDebutRealisation = :dateDebutRealisation, dateFinRealisation = :dateFinRealisation WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([
            'id' => $annonce->getId(),
            'titre' => $annonce->getTitre(),
            'description' => $annonce->getDescription(),
            'typeService' => $annonce->getTypeService(),
            'lieu' => $annonce->getLieu(),
            'remuneration' => $annonce->getRemuneration(),
            'dateDebutRealisation' => $annonce->getDateDebutRealisation(),
            'dateFinRealisation' => $annonce->getDateFinRealisation()
        ]);
    }


    /**
     * @brief Refuse la candidature d'un étudiant.
     * @param int $idAnnonce ID de l'annonce.
     * @param int $idEtudiant ID de l'étudiant.
     */
    public function refuserEtudiant($idAnnonce, $idEtudiant)
    {
        // Refuser un étudiant
        $sql = "DELETE FROM Postuler WHERE idAnnonce = :idAnnonce AND idEtudiant = :idEtudiant";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce" => $idAnnonce,
            "idEtudiant" => $idEtudiant
        ));
    }

    /**
     * @brief Accepte la candidature d'un étudiant.
     * @param int $idAnnonce ID de l'annonce.
     * @param int $idEtudiant ID de l'étudiant.
     */
    public function accepterEtudiant($idAnnonce, $idEtudiant)
    {
        // Accepter un étudiant
        $sql1 = "UPDATE Annonce SET etat = 'accepte' WHERE id = :idAnnonce";
        $pdoStatement1 = $this->pdo->prepare($sql1);
        $pdoStatement1->execute(array(
            "idAnnonce" => $idAnnonce
        ));
        $sql = "UPDATE Postuler SET estAccepte = '1' WHERE idAnnonce = :idAnnonce AND idEtudiant = :idEtudiant";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce" => $idAnnonce,
            "idEtudiant" => $idEtudiant
        ));
    }
    /**
     * @brief Trouve l'annonces par l'id de l'utilisateur et l'id de l'annonce.
     * @param int $utilisateur ID de l'utilisateur.
     * @return array Tableau d'annonces.
     */
    public function findById($utilisateur,$idAnnonce)
    {
        $sql = "SELECT * FROM Annonce WHERE idParticulier = :idParticulier AND id=:id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idParticulier" => $utilisateur,"id"=>$idAnnonce));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetch();
        $annonce = $this->hydrate($annonce);
        return $annonce;
    }

    /**
     * @brief Trouve les annonces par utilisateur et par état.
     * @param int $utilisateur ID de l'utilisateur.
     * @param string $etat Etat de l'annonce.
     * @return array Tableau d'annonces.
     */
    public function findAllByIdAndEtat($utilisateur, $etat)
    {
        $sql = "SELECT DISTINCT Annonce.* FROM Annonce LEFT JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE (Annonce.idParticulier = :idParticulier OR Postuler.idEtudiant = :idParticulier) AND Annonce.etat = :etat";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idParticulier" => $utilisateur, "etat" => $etat));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetchAll();
        $annonce = $this->hydrateAll($annonce);
        return $annonce;
    }

    /**
     * @brief Récupère les statistiques des annonces groupées par type de service.
     * 
     * Cette requête utilise :
     * - Une sous-requête pour compter le nombre de postulants par type de service
     * - GROUP BY pour regrouper les résultats par type de service
     * - Des fonctions d'agrégation (COUNT, AVG, SUM)
     * 
     * @return array Tableau associatif contenant pour chaque type de service :
     *               - typeService : le type de service
     *               - nbAnnonces : nombre d'annonces
     *               - remunerationMoyenne : rémunération moyenne
     *               - remunerationTotale : somme des rémunérations
     *               - totalPostulants : nombre total de postulants (via sous-requête)
     */
    public function getStatistiquesParType(): array
    {
        $sql = "
            SELECT 
                a.typeService,
                COUNT(*) AS nbAnnonces,
                ROUND(AVG(a.remuneration), 2) AS remunerationMoyenne,
                ROUND(SUM(a.remuneration), 2) AS remunerationTotale,
                (
                    SELECT COUNT(*) 
                    FROM Postuler p 
                    INNER JOIN Annonce a2 ON p.idAnnonce = a2.id 
                    WHERE a2.typeService = a.typeService
                ) AS totalPostulants
            FROM Annonce a
            WHERE a.dateSuppression IS NULL
            GROUP BY a.typeService
            ORDER BY nbAnnonces DESC
        ";

        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

        return $pdoStatement->fetchAll();
    }

    public function rechercherAnnonces(array $filtres, string $recherche): array
    {
        $sql = "SELECT * FROM Annonce WHERE ";
        foreach ($filtres as $attribut => $valeur) {
            $sql .= " $attribut = $valeur AND";
        }
        $sql .= " MATCH(titre, description, typeService, lieu) AGAINST (:recherche IN NATURAL LANGUAGE MODE);";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['recherche' => $recherche]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $resultats = $pdoStatement->fetchAll();
        return $this->hydrateAll($resultats);
    }

    /**
     * @brief Recherche et filtre des annonces selon les critères fournis.
     * @param string $recherche Texte de recherche FULLTEXT.
     * @param string $typeService Type de service.
     * @param string $lieu Lieu de l'annonce.
     * @param string $remunerationMin Rémunération minimale.
     * @param string $remunerationMax Rémunération maximale.
     * @param string $dateDebut Date de début souhaitée.
     * @param string $heureDebut Heure de début souhaitée.
     * @return array Tableau d'annonces filtrées.
     */
    public function search(string $recherche, string $typeService, string $lieu, string $remunerationMin, string $remunerationMax, string $dateDebut, string $heureDebut): array
    {
        $sql = "SELECT * FROM Annonce WHERE etat = 'disponible' AND dateSuppression IS NULL";
        $params = [];

        // Recherche FULLTEXT sur titre, description, typeService, lieu
        if (!empty($recherche)) {
            $sql .= " AND MATCH(titre, description, typeService, lieu) AGAINST(:recherche IN NATURAL LANGUAGE MODE)";
            $params['recherche'] = $recherche;
        }

        // Filtre type de service
        if (!empty($typeService)) {
            $sql .= " AND typeService = :typeService";
            $params['typeService'] = $typeService;
        }

        // Filtre lieu
        if (!empty($lieu)) {
            $sql .= " AND lieu LIKE :lieu";
            $params['lieu'] = '%' . $lieu . '%';
        }

        // Filtre rémunération minimale
        if (!empty($remunerationMin)) {
            $sql .= " AND remuneration >= :remunerationMin";
            $params['remunerationMin'] = (float)$remunerationMin;
        }

        // Filtre rémunération maximale
        if (!empty($remunerationMax)) {
            $sql .= " AND remuneration <= :remunerationMax";
            $params['remunerationMax'] = (float)$remunerationMax;
        }

        // Filtre date de début
        if (!empty($dateDebut)) {
            $sql .= " AND DATE(dateDebutRealisation) >= :dateDebut";
            $params['dateDebut'] = $dateDebut;
        }

        // Filtre heure de début
        if (!empty($heureDebut)) {
            $sql .= " AND TIME(dateDebutRealisation) >= :heureDebut";
            $params['heureDebut'] = $heureDebut;
        }

        $sql .= " ORDER BY datePublication DESC";

        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute($params);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $resultats = $pdoStatement->fetchAll();
        
        return $this->hydrateAll($resultats);
    }
}