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
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetch();
        $annonce = $this->hydrate($annonce);
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

        public function findAllById($utilisateur){
        $sql = "SELECT * FROM Annonce LEFT JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE Annonce.idParticulier = :idParticulier OR Postuler.idEtudiant = :idParticulier";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idParticulier"=>$utilisateur));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement->fetchAll();
        $annonce = $this->hydrateAll($annonce);
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
        $annonce = new Annonce(
            $tableauAssoc['id'] ?? null, 
            $tableauAssoc['idParticulier'] ?? null,
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

    public function insererAnnonce(){
        // Creation d'une annonce'
        $sql = "INSERT INTO Annonce (dateDebutRealisation, dateFinRealisation, etat, typeService, titre, description, datePublication, dataSuppression, motifSuppression, idParticulier) 
        VALUES (:dateDebutRealisation, :dateFinRealisation, :etat, :typeService, :titre, :description, :datePublication, :dataSuppression, :idParticulier )";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
    }
}