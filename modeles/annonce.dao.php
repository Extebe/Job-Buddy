<?php
require_once "include.php";


class AnnonceDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
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

    public function find(?string $id): ?Annonce
    {
        $sql = "SELECT * FROM Annonce WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Annonce');
        $annonce = $pdoStatement->fetch();
        return $annonce;
    }

    public function findAll(){
        $sql = "SELECT * FROM Annonce";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Annonce');
        $annonce = $pdoStatement->fetchAll();
        return $annonce;
    }

    public function findAllAssoc(){
        $sql="SELECT * FROM Annonce";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetchAll();
        return $annonce;
    }

    public function hydrate($tableauAssoc): ?Annonce
    {
        $annonce = new Annonce();
        $annonce->setId($tableauAssoc['id']);
        $annonce->setTitre($tableauAssoc['titre']);
        $annonce->setDescription($tableauAssoc['description']);
        $annonce->setEtat($tableauAssoc['etat']);
        $annonce->setTypeService($tableauAssoc['typeService']);
        $annonce->setDatePublication($tableauAssoc['datePublication']);
        $annonce->setDateDebutRealisation($tableauAssoc['dateDebutRealisation']);
        $annonce->setDateFinRealisation($tableauAssoc['dateFinRealisation']);
        $annonce->setMotifSuppression($tableauAssoc['motifSuppression']);
        $annonce->setDateSuppression($tableauAssoc['dateSuppression']);
        return $annonce;
    }

    public function hydrateAll($tableau): ?array{
        $categories = [];
        foreach($tableau as $tableauAssoc){
            $categorie = $this->hydrate($tableauAssoc);
            $categories[] = $categorie;
        }
        return $categories;
    }
}