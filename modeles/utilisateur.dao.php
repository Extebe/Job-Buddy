<?php

require_once "include.php";

class UtilisateurDao{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null){
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
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }

    public function insererUtilisateur($user, $passwordHache): void {
        $requete = "INSERT INTO Utilisateur (role, codeINE, nom, prenom, tel, dateNaiss, email, mdp, ville, adresse, codePostal) 
        values (:role, :codeINE, :nom, :prenom, :tel, :dateNaiss, :email, :mdp, :ville, :adresse, :codePostal);";
        $pdoStatement = $this->pdo->prepare($requete);
        $pdoStatement->execute([
            ':role' => $user->getRole(),
            ':codeINE' => $user->getCodeINE(),
            ':nom' => $user->getNom(),
            ':prenom' => $user->getPrenom(),
            ':tel' => $user->getTelephone(),
            ':dateNaiss' => $user->getDateNaiss(),
            ':email' => $user->getEmail(),
            ':mdp' => $user->getMdp(),
            ':ville' => $user->getVille(),
            ':adresse' => $user->getAdresse(),
            ':codePostal' => $user->getCodePostal()
        ]);
    }

}
?>