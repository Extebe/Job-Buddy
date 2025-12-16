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

    public function findEtudiant($annonceId){
        $sql = "SELECT POSTULER.idEtudiant FROM Annonce  JOIN POSTULER ON POSTULER.idAnnonce=ANNONCE.id WHERE ANNONCE.id= :$annonceId";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonceId));
        $etudiantId = $pdoStatement->fetchColumn();
        return $etudiantId;
    }

    public function findParticulier($annonceId){
        $sql = "SELECT particulier FROM Annonce WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonceId));
        $particulierId = $pdoStatement->fetchColumn();
        return $particulierId;
    }



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

    public function hydrateAll($tableau): ?array{
        $categories = [];
        foreach($tableau as $tableauAssoc){
            $categorie = $this->hydrate($tableauAssoc);
            $categories[] = $categorie;
        }
        return $categories;
    }

    public function addRelations(Annonce $annonce): Annonce{
        // Creation de la liste de postulations
        $sql = "SELECT idEtudiant, datePostulat FROM Annonce WHERE idAnnonce= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonce->getId()));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $assocPostulations = $pdoStatement->fetch();

        $etudiantDao = new EtudiantDao($this->pdo);
        $postulations = [];
        foreach($assocPostulations as $assocPostulation){
            $etudiant = $etudiantDao->find($assocPostulation['idEtudiant']);
            $postulations[$etudiant] = $assocPostulation['datePostulat'];
        }
        $annonce->setPostulations($postulations);

        return $annonce;
    }
}