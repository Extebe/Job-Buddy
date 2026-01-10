<?php

require_once "include.php";

/**
 * @brief DAO pour la gestion des signalements d'utilisateurs.
 */
class SignalementUtilisateurDao extends SignalementDao
{
    /**
     * @brief Récupère tous les signalements d'utilisateurs.
     * @return array Tableau de signalements.
     */
    public function findAll()
    {
        $sql = "SELECT * FROM signalementUtilisateur SU 
                  INNER JOIN signalement S ON SU.idSignalement=S.id
                  INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "signalementUtilisateur");

        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }

    /**
     * @brief Trouve un signalement par l'ID de l'utilisateur signalé.
     * @param int|null $id ID de l'utilisateur signalé.
     * @return SignalementUtilisateur|null Le signalement trouvé.
     */
    public function find(?int $id): SignalementUtilisateur
    {
        $sql = "SELECT * FROM signalementUtilisateur SU 
            INNER JOIN signalement S ON SU.idSignalement=S.id
            INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id
            WHERE  SU.idUtilisateurSignale =:id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "signalementUtilisateur");

        $signalement = $pdoStatement->fetch();
        return $signalement;
    }

    /**
     * @brief Récupère tous les signalements sous forme de tableau associatif.
     * @return array Tableau associatif.
     */
    public function findAllAssoc()
    {
        $sql = "SELECT * FROM signalementUtilisateur SU 
            INNER JOIN signalement S ON SU.idSignalement=S.id
            INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }

    /**
     * @brief Trouve un signalement par ID utilisateur (retourne tableau assoc).
     * @param int|null $id ID utilisateur signalé.
     * @return SignalementUtilisateur|null Le signalement.
     */
    public function findAssoc(?int $id): SignalementUtilisateur
    {
        $sql = "SELECT * FROM signalementUtilisateur SU 
            INNER JOIN signalement S ON SU.idSignalement=S.id
            INNER JOIN Utilisateur U ON SU.idUtilisateurSignale=U.id
            WHERE  SU.idUtilisateurSignale =:id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

        $signalement = $pdoStatement->fetch();
        return $signalement;
    }

    /**
     * @brief Hydrate un objet SignalementUtilisateur.
     * @param array $tableauAssoc Données du signalement.
     * @return SignalementUtilisateur|null L'objet hydraté.
     */
    public function hydrate($tableauAssoc): ?SignalementUtilisateur
    {
        $signalementUtilisateur = new SignalementUtilisateur($tableauAssoc['signaleur'], $tableauAssoc['dateSignalement'], $tableauAssoc['motif'], $tableauAssoc['description'], $tableauAssoc['utilisateurSignale']);
        return $signalementUtilisateur;
    }

    /**
     * @brief Hydrate une liste de signalements d'utilisateurs.
     * @param array $tab Tableau de données.
     * @return array Tableau d'objets SignalementUtilisateur.
     */
    public function hydrateAll($tab): ?array
    {
        $listeUtilisateurSignale = [];
        foreach ($tab as $tabAssoc) {
            $utilisateur = $this->hydrate($tabAssoc);
            $listeUtilisateurSignale[] = $utilisateur;
        }
        return $listeUtilisateurSignale;
    }

}

?>