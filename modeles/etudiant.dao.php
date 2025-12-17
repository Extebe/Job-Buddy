<?php

require_once "include.php";

class EtudiantDAO extends UtilisateurDAO{
    public function findAll(){
        $sql = "SELECT * FROM Utilisateur WHERE role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $etudiants = $pdoStatement->fetchAll();
        return $etudiants;
    }

    public function find(?int $id): ?Etudiant
    {
        $sql = "SELECT * FROM Utilisateur WHERE code = :id AND role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $etudiant = $pdoStatement->fetch();
        return $etudiant;
    }

    public function findAllAssoc(){
        $sql = "SELECT * FROM Utilisateur WHERE role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $etudiants = $pdoStatement->fetchAll();
        return $etudiants;
    }

    public function findAssoc(?int $id): ?array
    {
        $sql = "SELECT * FROM Utilisateur WHERE code = :id AND role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $etudiant = $pdoStatement->fetch();
        return $etudiant;
    }

    public function hydrate($tableauAssoc): ?Etudiant
    {
        $etudiant = new Etudiant(
            $tableauAssoc['id'],
            $tableauAssoc['codeINE'],
            $tableauAssoc['nom'],
            $tableauAssoc['prenom'],
            $tableauAssoc['tel'],
            $tableauAssoc['dateNaiss'],
            $tableauAssoc['role'],
            $tableauAssoc['email'],
            $tableauAssoc['mdp'],
            $tableauAssoc['adresse'],
            $tableauAssoc['ville'],
            $tableauAssoc['codePostal'],
            $tableauAssoc['dateSuppression']
        );
        return $etudiant;
    }

    public function hydrateAll($tableau): ?array
    {
        $etudiants = [];
        foreach($tableau as $tableauAssoc){
            $etudiant = $this->hydrate($tableauAssoc);
            $etudiants[] = $etudiant;
        }
        return $etudiants;
    }


        public function findByAnnonce($annonceId): ?Etudiant{
        $sql = "SELECT UTILISATEUR.* FROM Annonce JOIN POSTULER ON POSTULER.idAnnonce=ANNONCE.id JOIN UTILISATEUR ON POSTULER.idEtudiant=UTILISATEUR.id WHERE ANNONCE.id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonceId));
        $row = $pdoStatement->fetch();
        if ($row === false) {
        return null;
        }

    return $this->hydrate($row);
    }
}