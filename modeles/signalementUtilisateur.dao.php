<?php

require_once "include.php";
/**
 * @brief DAO pour les signalements d'utilisateurs.
 */
class SignalementUtilisateurDao extends SignalementDao
{
    /**
     * @brief Récupère tous les signalements d'utilisateurs.
     * @return array Tableau d'objets SignalementUtilisateur.
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
     * @param int|null $id ID de l'utilisateur.
     * @return SignalementUtilisateur|boolean Le signalement ou false.
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
     * @brief Récupère tous les signalements d'utilisateurs sous forme de tableau associatif.
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
     * @brief Trouve un signalement par l'ID de l'utilisateur signalé (associatif).
     * @param int|null $id ID de l'utilisateur.
     * @return SignalementUtilisateur|boolean Le signalement ou false.
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
     * @param mixed $tableauAssoc Données du signalement.
     * @return SignalementUtilisateur|null L'objet hydraté.
     */
    public function hydrate($tableauAssoc): ?SignalementUtilisateur
    {
        $signalementUtilisateur = new SignalementUtilisateur($tableauAssoc['signaleur'], $tableauAssoc['dateSignalement'], $tableauAssoc['motif'], $tableauAssoc['description'], $tableauAssoc['utilisateurSignale']);
        return $signalementUtilisateur;
    }

    /**
     * @brief Hydrate plusieurs signalements d'utilisateurs.
     * @param mixed $tab Tableau de données.
     * @return array|null Tableau d'objets SignalementUtilisateur.
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