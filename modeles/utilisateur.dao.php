<?php

require_once "include.php";

/**
 * @brief DAO de base pour la gestion des utilisateurs.
 */
class UtilisateurDAO
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    protected ?PDO $pdo;

    /**
     * @brief Constructeur du DAO Utilisateur.
     * @param PDO|null $pdo Instance de PDO.
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'objet PDO.
     * @return PDO|null L'objet PDO.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit l'objet PDO.
     * @param PDO|null $pdo L'objet PDO.
     */
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }



    /**
     * @brief Hydrate un objet Utilisateur.
     * @param array $tableau Données de l'utilisateur.
     * @return Utilisateur L'objet utilisateur hydraté.
     */
    public function hydrate($tableau)
    {
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
        $utilisateur->setPhotoProfil($tableau['photoProfil'] ?? null);

        return $utilisateur;
    }

    /**
     * @brief Trouve un utilisateur par son ID.
     * @param string|null $id ID de l'utilisateur.
     * @return Utilisateur|null L'utilisateur trouvé.
     */
    function findById(?string $id): ?Utilisateur
    {
        $sql = "SELECT * FROM Utilisateur WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['id' => $id]);
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $tableau = $pdoStatement->fetch();
        return $this->hydrate($tableau);
    }

    /**
     * @brief Met à jour les informations d'un utilisateur.
     * @param Utilisateur $user L'utilisateur à mettre à jour.
     */
    public function update(Utilisateur $user): void
    {
        $sql = "UPDATE Utilisateur SET 
            nom = :nom,
            prenom = :prenom,
            tel = :tel,
            dateNaiss = :dateNaiss,
            email = :email,
            mdp = :mdp,
            ville = :ville,
            adresse = :adresse,
            codePostal = :codePostal,
            photoProfil = :photoProfil"
            ;

        // Ajout du champ codeINE si l'utilisateur est un étudiant
        if ($user instanceof Etudiant) {
            $sql .= ", codeINE = :codeINE";
        }

        $sql .= " WHERE id = :id";

        $pdoStatement = $this->pdo->prepare($sql);

        $params = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'tel' => $user->getTel(),
            'dateNaiss' => $user->getDateNaiss(),
            'email' => $user->getEmail(),
            'mdp' => $user->getMdp(),
            'ville' => $user->getVille(),
            'adresse' => $user->getAdresse(),
            'codePostal' => $user->getCodePostal(),
            'photoProfil' => $user->getPhotoProfil(),
            'id' => $user->getId()
            
        ];

        if ($user instanceof Etudiant) {
            $params['codeINE'] = $user->getCodeINE();
        }

        $pdoStatement->execute($params);
    }

    /**
     * @brief Supprime (soft delete) un utilisateur.
     * @param string|null $id ID de l'utilisateur.
     */
    public function delete(?string $id): void
    {
        $sql = "UPDATE Utilisateur SET dateSuppression = NOW() WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['id' => $id]);
    }

    public function updatePhotoProfil(?string $id, ?string $photoPath): void
    {
        $sql = "UPDATE Utilisateur SET photoProfil = :photoProfil WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([
            'photoProfil' => $photoPath,
            'id' => $id
        ]);
    }
}
?>