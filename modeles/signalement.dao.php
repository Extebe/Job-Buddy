<?php

require_once "include.php";

/**
 * @brief DAO de base pour la gestion des signalements.
 */
class SignalementDao
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Signalement.
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
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
        ;
    }


    /**
     * @brief Récupère tous les signalements sous forme de tableau associatif.
     * @return array Tableau associatif.
     */
    public function findAllAssoc()
    {
        $sql = "SELECT * FROM Signalement";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }

    // /**
    //  * @brief Trouve un signalement par l'ID de l'annonce signalée (retourne tableau assoc).
    //  * @param int|null $id ID de l'annonce.
    //  * @return SignalementAnnonce|null Le signalement.
    //  */
    // public function findAssocByID(?int $id): ?SignalementAnnonce
    // {
    //     $sql = "SELECT * FROM signalementAnnonce SA
    //         INNER JOIN signalement S ON SA.idSignalement=S.id
    //         INNER JOIN annonce A ON SA.idAnnonceSignale=A.id
    //         WHERE SA.idAnnonceSignale = :id";
    //     $pdoStatement = $this->getPdo()->prepare($sql);
    //     $pdoStatement->execute(array("id" => $id));
    //     $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

    //     $signalement = $pdoStatement->fetch();
    //     return $signalement;
    // }

    /**
     * @brief Hydrate un objet Signalement.
     * @param array $tabAssoc Données du signalement.
     * @return Signalement|null L'objet hydraté.
     */
    public function hydrate($tabAssoc): ?Signalement
    {
        $managerUtilisateur=new UtilisateurDao($this->pdo);
        $managerAnnonce = new AnnonceDao($this->pdo);
        
        $utilisateurSignaleur = $managerUtilisateur->findByID($tabAssoc['idSignaleur']);
        $utilisateurSignale = $managerUtilisateur->findByID($tabAssoc['idUtilisateurSignale']);
        $annonceSignale = $managerAnnonce->find($tabAssoc['idAnnonceSignale']);

        $signalement = new Signalement($tabAssoc['id'],
                                       $tabAssoc['dateSignalement'],
                                       $tabAssoc['motif'],
                                       $tabAssoc['description'], 
                                       $utilisateurSignaleur, 
                                       $utilisateurSignale,
                                       $annonceSignale
                                    );
        return $signalement;
    }

    /**
     * @brief Hydrate une liste de signalements d'annonces.
     * @param array $tab Tableau de données.
     * @return array Tableau d'objets SignalementAnnonce.
     */
    public function hydrateAll($tab): ?array
    {
        $listeSignalement = [];
        foreach ($tab as $tabAssoc) {
            $signalement = $this->hydrate($tabAssoc);
            $listeSignalement[] = $signalement;
        }
        return $listeSignalement;
    }
}
?>