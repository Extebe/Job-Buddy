<?php

require_once "include.php";

class ParticulierDAO extends UtilisateurDAO{
    public function findAll(){
        $sql = "SELECT * FROM utilisateur WHERE role = 'particulier'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'utilisateur');
        $utilisateurs = $pdoStatement->fetchAll();
        return $utilisateurs;
    }

    public function find(?int $id): ?Particulier
    {
        $sql = "SELECT * FROM utilisateur WHERE code = :id AND role = 'particulier'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'utilisateur');
        $utilisateur = $pdoStatement->fetch();
        return $utilisateur;
    }

    public function findAllAssoc(){
        $sql = "SELECT * FROM utilisateur WHERE role = 'particulier'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $utilisateurs = $pdoStatement->fetchAll();
        return $utilisateurs;
    }

    public function findAssoc(?int $id): ?array
    {
        $sql = "SELECT * FROM utilisateur WHERE code = :id AND role = 'particulier'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $utilisateur = $pdoStatement->fetch();
        return $utilisateur;
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
        $particulier->setTelephone($tableauAssoc["telephone"]);
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