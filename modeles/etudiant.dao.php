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

    public function find(?int $id): ?Etudiant
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
        $etudiant->setId($tableauAssoc["id"]);
        $etudiant->setNom($tableauAssoc["nom"]);
        $etudiant->setPrenom($tableauAssoc["prenom"]);
        $etudiant->setTel($tableauAssoc["tel"]);
        $etudiant->setDateNaiss($tableauAssoc["dateNaiss"]);
        $etudiant->setRole($tableauAssoc["role"]);
        $etudiant->setCodeEtudiant($tableauAssoc["codeINE"]);
        $etudiant->setEmail($tableauAssoc["email"]);
        $etudiant->setMdp($tableauAssoc["mdp"]);
        $etudiant->setAdresse($tableauAssoc["adresse"]);
        $etudiant->setVille($tableauAssoc["ville"]);
        $etudiant->setCodePostal($tableauAssoc["codePostal"]);
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

    public function insererUtilisateur($user, $passwordHache): void {
    $requete = "INSERT INTO Utilisateur (role, codeINE, nom, prenom, tel, dateNaiss, email, mdp, ville, adresse, codePostal,tentativesEchouees,dateDernierEchecConnexion,statutCompte) 
    values (:role, :codeINE, :nom, :prenom, :tel, :dateNaiss, :email, :mdp, :ville, :adresse, :codePostal,:tentativesEchouees,:dateDernierEchecConnexion,:statutCompte);";
    $pdoStatement = $this->pdo->prepare($requete);
    $pdoStatement->execute([
        ':role' => 'Etudiant',
        ':codeINE' => $user->getCodeEtudiant(),
        ':nom' => $user->getNom(),
        ':prenom' => $user->getPrenom(),
        ':tel' => $user->getTel(),
        ':dateNaiss' => $user->getDateNaiss(),
        ':email' => $user->getEmail(),
        ':mdp' => $passwordHache,
        ':ville' => $user->getVille(),
        ':adresse' => $user->getAdresse(),
        ':codePostal' => $user->getCodePostal(),
        ':tentativesEchouees' => 0,
        ':dateDernierEchecConnexion' => null,
        ':statutCompte' => 'actif'
        ]);
    }
}