<?php

require_once "include.php";

class ParticulierDAO extends UtilisateurDAO{
    public function findAll(){
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE role = 'Particulier '";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Particulier');
        $particuliers = $pdoStatement->fetchAll();
        return $particuliers;
    }

    public function find(?int $id): ?Particulier
    {
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE id = :id AND role = 'Particulier '";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $particulier = $pdoStatement->fetch();
        $particulier = $this->hydrate($particulier);
        return $particulier;
    }

    public function findAllAssoc(){
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE role = 'Particulier '";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $particuliers = $pdoStatement->fetchAll();
        return $particuliers;
    }

    public function findAssoc(?int $id): ?array
    {
        $sql = "SELECT id, role, nom, prenom, tel, dateNaiss, email, mdp, dateSuppression, ville, adresse, codePostal FROM Utilisateur WHERE id = :id AND role = 'Particulier '";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $particulier = $pdoStatement->fetch();
        return $particulier;
    }

    public function findByAnnonce($annonceId): ?Particulier{
        $sql = "SELECT UTILISATEUR.* FROM Annonce JOIN UTILISATEUR ON UTILISATEUR.id=Annonce.idParticulier WHERE Annonce.id = :id AND UTILISATEUR.role = 'Particulier '";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$annonceId));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $pdoStatement->fetch();        
          if ($row === false) {
        return null;
    }

    return $this->hydrate($row);
}


    public function hydrate($tableauAssoc): ?Particulier
    {
        $particulier = new Particulier();
        $particulier->setId($tableauAssoc['id'] ?? null);
        $particulier->setNom($tableauAssoc['nom'] ?? null);
        $particulier->setPrenom($tableauAssoc['prenom'] ?? null);
        $particulier->setTel($tableauAssoc['tel'] ?? null);
        $particulier->setDateNaiss($tableauAssoc['dateNaiss'] ?? null);
        $particulier->setRole($tableauAssoc['role'] ?? null);
        $particulier->setEmail($tableauAssoc['email'] ?? null);
        $particulier->setMdp($tableauAssoc['mdp'] ?? null);
        $particulier->setAdresse($tableauAssoc['adresse'] ?? null);
        $particulier->setVille($tableauAssoc['ville'] ?? null);
        $particulier->setCodePostal($tableauAssoc['codePostal'] ?? null);

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

    public function insererUtilisateur($user, $passwordHache): void {
    $requete = "INSERT INTO Utilisateur (role, codeINE, nom, prenom, tel, dateNaiss, email, mdp, ville, adresse, codePostal,tentativesEchouees,dateDernierEchecConnexion,statutCompte) 
    values (:role, :codeINE, :nom, :prenom, :tel, :dateNaiss, :email, :mdp, :ville, :adresse, :codePostal,:tentativesEchouees,:dateDernierEchecConnexion,:statutCompte);";
    $pdoStatement = $this->pdo->prepare($requete);
    $pdoStatement->execute([
        ':role' => 'Particulier ',
        ':codeINE' => null,
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