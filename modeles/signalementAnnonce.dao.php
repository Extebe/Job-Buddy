<?php

require_once "include.php";
/**
 * @brief DAO pour les signalements d'annonces.
 */
class SignalementAnnonceDoa extends SignalementDao
{
    /**
     * @brief Récupère tous les signalements d'annonces.
     * @return array Tableau d'objets SignalementAnnonce.
     */
    public function findAll()
    {
        $sql = "SELECT * FROM signalementAnnonce SA
              INNER JOIN signalement S ON SA.idSignalement=S.id
              INNER JOIN annonce A ON SA.idAnnonceSignale=A.id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "signalementAnnonce");

        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }

    /**
     * @brief Trouve un signalement par l'ID de l'annonce signalée.
     * @param int|null $id ID de l'annonce.
     * @return SignalementAnnonce|boolean Le signalement ou false.
     */
    public function find(?int $id): ?SignalementAnnonce
    {
        $sql = "SELECT * FROM signalementAnnonce SA
        INNER JOIN signalement S ON SA.idSignalement=S.id
        INNER JOIN annonce A ON SA.idAnnonceSignale=A.id
        WHERE SA.idAnnonceSignale = :id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "signalementAnnonce");

        $signalement = $pdoStatement->fetch();
        return $signalement;
    }

    /**
     * @brief Récupère tous les signalements d'annonces sous forme de tableau associatif.
     * @return array Tableau associatif.
     */
    public function findAllAssoc()
    {
        $sql = "SELECT * FROM signalementAnnonce SA
        INNER JOIN signalement S ON SA.idSignalement=S.id
        INNER JOIN annonce A ON SA.idAnnonceSignale=A.id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }

    /**
     * @brief Trouve un signalement par l'ID de l'annonce signalée (associatif).
     * @param int|null $id ID de l'annonce.
     * @return SignalementAnnonce|boolean Le signalement ou false.
     */
    public function findAssoc(?int $id): ?SignalementAnnonce
    {
        $sql = "SELECT * FROM signalementAnnonce SA
        INNER JOIN signalement S ON SA.idSignalement=S.id
        INNER JOIN annonce A ON SA.idAnnonceSignale=A.id
        WHERE SA.idAnnonceSignale = :id";
        $pdoStatement = $this->getPdo()->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);

        $signalement = $pdoStatement->fetch();
        return $signalement;
    }

    /**
     * @brief Hydrate un objet SignalementAnnonce.
     * @param mixed $tabAssoc Données du signalement.
     * @return SignalementAnnonce|null L'objet hydraté.
     */
    public function hydrate($tabAssoc): ?SignalementAnnonce
    {
        $signalementAnnonce = new SignalementAnnonce($tabAssoc['signaleur'], $tabAssoc['dateSignalement'], $tabAssoc['motif'], $tabAssoc['description'], $tabAssoc['annondeSignale'], );
        return $signalementAnnonce;
    }

    /**
     * @brief Hydrate plusieurs signalements d'annonces.
     * @param mixed $tab Tableau de données.
     * @return array|null Tableau d'objets SignalementAnnonce.
     */
    public function hydrateAll($tab): ?array
    {
        $listeSignalementAnnonce = [];
        foreach ($tab as $tabAssoc) {
            $signalementAnnonce = $this->hydrate($tabAssoc);
            $listeSignalementAnnonce[] = $signalementAnnonce;
        }
        return $listeSignalementAnnonce;
    }
}
?>