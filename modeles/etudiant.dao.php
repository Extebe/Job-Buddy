<?php

require_once "include.php";

class EtudiantDAO extends UtilisateurDAO{
    public function findAll(){
        $sql = "SELECT * FROM utilisateur WHERE role = 'etudiant'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'utilisateur');
        $utilisateurs = $pdoStatement->fetchAll();
        return $utilisateurs;
    }

    public function find(?int $id): ?Particulier
    {
        $sql = "SELECT * FROM utilisateur WHERE code = :id AND role = 'etudiant'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'utilisateur');
        $utilisateur = $pdoStatement->fetch();
        return $utilisateur;
    }

    public function findAllAssoc(){
        $sql = "SELECT * FROM utilisateur WHERE role = 'etudiant'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $utilisateurs = $pdoStatement->fetchAll();
        return $utilisateurs;
    }

    public function findAssoc(?int $id): ?array
    {
        $sql = "SELECT * FROM utilisateur WHERE code = :id AND role = 'etudiant'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $utilisateur = $pdoStatement->fetch();
        return $utilisateur;
    }

    public function hydrate($tableauAssoc): ?Etudiant
    {
        $etudiant = new Etudiant();
        $etudiant->setId($tableauAssoc["code"]);
        $etudiant->setNom($tableauAssoc["nom"]);
        $etudiant->setPrenom($tableauAssoc["prenom"]);
        $etudiant->setTelephone($tableauAssoc["telephone"]);
        $etudiant->setDateNaiss($tableauAssoc["dateNaiss"]);
        $etudiant->setEmail($tableauAssoc["email"]);
        $etudiant->setMdp($tableauAssoc["mdp"]);
        $etudiant->setAdresse($tableauAssoc["adresse"]);
        $etudiant->setVille($tableauAssoc["ville"]);
        $etudiant->setCodePostal($tableauAssoc["codePostal"]);
        $etudiant->setCodeEtudiant($tableauAssoc["codeEtudiant"]);
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
}