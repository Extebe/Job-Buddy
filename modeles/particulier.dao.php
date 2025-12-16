<?php

require_once "include.php";

class ParticulierDAO extends UtilisateurDAO{
    public function findAll(){
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE role = 'PARTICULIER'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Particulier');
        $particuliers = $pdoStatement->fetchAll();
        return $particuliers;
    }

    public function find(?int $id): ?Particulier
    {
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE id = :id AND role = 'PARTICULIER'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Particulier');
        $particulier = $pdoStatement->fetch();
        return $particulier;
    }

    public function findAllAssoc(){
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE role = 'PARTICULIER'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $particuliers = $pdoStatement->fetchAll();
        return $particuliers;
    }

    public function findAssoc(?int $id): ?array
    {
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE id = :id AND role = 'PARTICULIER'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $particulier = $pdoStatement->fetch();
        return $particulier;
    }

    public function findByAnnonce($annonceId): ?Particulier{
        $sql = "SELECT idParticulier FROM Annonce WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonceId));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $particulierId = $pdoStatement->fetch();
        $particulierId = $this->hydrate($particulierId);
        return $particulierId;
    }


    public function hydrate($tableauAssoc): ?Particulier
    {
        $particulier = new Particulier();
        $particulier->setId($tableauAssoc["id"]);
        $particulier->setNom($tableauAssoc["nom"]);
        $particulier->setPrenom($tableauAssoc["prenom"]);
        $particulier->setTel($tableauAssoc["tel"]);
        $particulier->setDateNaiss($tableauAssoc["dateNaiss"]);
        $particulier->setRole($tableauAssoc["role"]);
        $particulier->setEmail($tableauAssoc["email"]);
        $particulier->setMdp($tableauAssoc["mdp"]);
        $particulier->setAdresse($tableauAssoc["adresse"]);
        $particulier->setVille($tableauAssoc["ville"]);
        $particulier->setCodePostal($tableauAssoc["codePostal"]);
        return $particulier;
    }

        

    public function hydrateAll($tableau): ?array
    {
        $particuliers = [];
        foreach($tableau as $tableauAssoc){
            $particulier = $this->hydrate($tableauAssoc);
            $particuliers[] = $particulier;
        }
        return $particuliers;
    }
}