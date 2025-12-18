<?php

require_once "include.php";

class UtilisateurDAO{
    protected ?PDO $pdo;

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



    public function hydrate($tableau){
        $utilisateur = new Utilisateur();
        $utilisateur->setId($tableau['id'] ?? null);
        $utilisateur->setRole($tableau['role'] ?? null);
        $utilisateur->setNom($tableau['nom'] ?? null);
        $utilisateur->setPrenom($tableau['prenom'] ?? null);
        $utilisateur->setTel($tableau['tel'] ?? null);
        $utilisateur->setDateNaiss($tableau['dateNaiss'] ?? null);
        $utilisateur->setEmail($tableau['email'] ?? null);
        $utilisateur->setMdp($tableau['mdp'] ?? null);
        $utilisateur->setVille($tableau['ville'] ?? null);
        $utilisateur->setAdresse($tableau['adresse'] ?? null);
        $utilisateur->setCodePostal($tableau['codePostal'] ?? null);

        return $utilisateur;
    }

    function findById(string $id): ?Utilisateur{
        $sql = "SELECT * FROM Utilisateur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['id' => $id]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        return $this->hydrate($tableau) ?: null;
    }
}
?>