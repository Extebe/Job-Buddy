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
        $sql = "SELECT DISTINCT Annonce.* FROM Annonce LEFT JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE Annonce.idParticulier = :idParticulier OR Postuler.idEtudiant = :idParticulier";
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
        $sql = "SELECT Postuler.idEtudiant, Postuler.datePostulat FROM Annonce JOIN Postuler ON Annonce.id=Postuler.idAnnonce WHERE Postuler.idAnnonce= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonce->getId()));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $assocPostulations = $pdoStatement->fetchAll();
        $etudiantDao = new EtudiantDao($this->pdo);
        $postulations = [];
        foreach($assocPostulations as $assocPostulation){
            $etudiant = $etudiantDao->find($assocPostulation['idEtudiant']);
           $postulations[] = $etudiant;
        }
        $annonce->setPostulations($postulations);

        return $annonce;
    }

    public function insererAnnonce(Annonce $annonce){
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
    'typeService' => $annonce->getTypeService(),
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

    public function postuler($idAnnonce, $idEtudiant){
        // Postuler a une annonce
        $sql = "INSERT INTO Postuler (idAnnonce, idEtudiant, datePostulat, estAccepte) 
        VALUES (:idAnnonce, :idEtudiant, :datePostulat, '0')";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce"=>$idAnnonce,
            "idEtudiant"=>$idEtudiant,
            "datePostulat"=>date("Y-m-d H:i:s")
        ));
    }

    public function supprimer($idAnnonce,$idParticulier){
        // Supprimer une annonce
        $sql1 ="SELECT idParticulier FROM Annonce WHERE id= :idAnnonce";
        $pdoStatement1 = $this->pdo->prepare($sql1);
        $pdoStatement1->execute(array("idAnnonce"=>$idAnnonce));
        $pdoStatement1->setFetchMode(PDO::FETCH_ASSOC);
        $annonce = $pdoStatement1->fetch();
        if($annonce['idParticulier'] != $idParticulier){
            throw new Exception("Vous n'êtes pas autorisé à supprimer cette annonce.");
            exit();
        }
        $sql2 ="DELETE FROM Postuler WHERE idAnnonce = :idAnnonce";
        $pdoStatement2 = $this->pdo->prepare($sql2);
        $pdoStatement2->execute(array(
            "idAnnonce"=>$idAnnonce
        ));
        $sql3 ="DELETE FROM Note WHERE idAnnonce = :idAnnonce";
        $pdoStatement3 = $this->pdo->prepare($sql3);
        $pdoStatement3->execute(array(
            "idAnnonce"=>$idAnnonce
        ));
        $sql4 ="DELETE FROM SignalementAnonce WHERE idAnnonceSignale = :idAnnonce";
        $pdoStatement4 = $this->pdo->prepare($sql4);
        $pdoStatement4->execute(array(
            "idAnnonce"=>$idAnnonce
        ));
        $sql = "DELETE FROM Annonce WHERE id = :idAnnonce";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce"=>$idAnnonce
        ));
    }
    
    public function refuserEtudiant($idAnnonce, $idEtudiant){
        // Refuser un étudiant
        $sql = "DELETE FROM Postuler WHERE idAnnonce = :idAnnonce AND idEtudiant = :idEtudiant";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array(
            "idAnnonce"=>$idAnnonce,
            "idEtudiant"=>$idEtudiant
        ));
    }

}