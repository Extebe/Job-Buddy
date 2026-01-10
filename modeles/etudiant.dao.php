<?php

require_once "include.php";

/**
 * @brief DAO spécifique pour les étudiants, héritant de UtilisateurDAO.
 */
class EtudiantDAO extends UtilisateurDAO
{
    /**
     * @brief Récupère tous les étudiants.
     * @return array Tableau d'objets Etudiant.
     */
    public function findAll()
    {
        $sql = "SELECT * FROM Utilisateur WHERE role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Utilisateur');
        $etudiants = $pdoStatement->fetchAll();
        return $etudiants;
    }

    /**
     * @brief Trouve un étudiant par son ID.
     * @param int|null $id Identifiant de l'étudiant.
     * @return Etudiant|boolean L'étudiant correspondant ou false.
     */
    public function find(?int $id): ?Etudiant
    {
        $sql = "SELECT 
    id,
    codeINE,
    nom,
    prenom,
    tel,
    dateNaiss,
    role,
    email,
    mdp,
    adresse,
    ville,
    codePostal,
    dateSuppression,
    tentativesEchouees,
    dateDernierEchecConnexion,
    statutCompte
FROM Utilisateur
WHERE id = :id
  AND role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Etudiant');
        $etudiant = $pdoStatement->fetch();
        return $etudiant;
    }

    /**
     * @brief Récupère tous les étudiants sous forme de tableau associatif.
     * @return array Tableau d'étudiants (associatif).
     */
    public function findAllAssoc()
    {
        $sql = "SELECT * FROM Utilisateur WHERE role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $etudiants = $pdoStatement->fetchAll();
        return $etudiants;
    }

    /**
     * @brief Trouve un étudiant par son code (INE ?) et rôle.
     * @param int|null $id Code/ID.
     * @return array|boolean Tableau associatif de l'étudiant ou false.
     */
    public function findAssoc(?int $id): ?array
    {
        $sql = "SELECT * FROM Utilisateur WHERE code = :id AND role = 'ETUDIANT'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $etudiant = $pdoStatement->fetch();
        return $etudiant;
    }

    /**
     * @brief Hydrate un objet Etudiant.
     * @param array $tableauAssoc Données de l'étudiant.
     * @return Etudiant|null L'objet Etudiant hydraté.
     */
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

    /**
     * @brief Hydrate plusieurs étudiants.
     * @param array $tableau Tableau de données.
     * @return array|null Tableau d'objets Etudiant.
     */
    public function hydrateAll($tableau): ?array
    {
        $etudiants = [];
        foreach ($tableau as $tableauAssoc) {
            $etudiant = $this->hydrate($tableauAssoc);
            $etudiants[] = $etudiant;
        }
        return $etudiants;
    }


    /**
     * @brief Trouve l'étudiant retenu pour une annonce.
     * @param mixed $annonceId Identifiant de l'annonce.
     * @return Etudiant|null L'étudiant retenu ou null.
     */
    public function findByAnnonce($annonceId): ?Etudiant
    {
        $sql = "SELECT utilisateur.* FROM Annonce JOIN Postuler ON Postuler.idAnnonce=Annonce.id JOIN utilisateur ON Postuler.idEtudiant=utilisateur.id WHERE Annonce.id = :id AND utilisateur.role = 'ETUDIANT' AND Postuler.estAccepte='1'";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $annonceId));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $row = $pdoStatement->fetch();
        if ($row === false) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @brief Insère un nouvel étudiant en base de données après vérification CVEC.
     * @param Etudiant $user L'objet étudiant à insérer.
     * @param string $passwordHache Mot de passe haché.
     * @throws Exception Si le CVEC est invalide.
     */
    public function insererUtilisateur($user, $passwordHache): void
    {
        if (!$user->verifierCvecAvecINE($user->getCvec(), $user->getNom(), $user->getCodeINE())) {
            throw new Exception("CVEC invalide");
            // header('Location: index.php?controller=utilisateur&method=pageInscription');
            exit();
        }
        $requete = "INSERT INTO Utilisateur (role, codeINE, nom, prenom, tel, dateNaiss, email, mdp, ville, adresse, codePostal,tentativesEchouees,dateDernierEchecConnexion,statutCompte,cvec) 
    values (:role, :codeINE, :nom, :prenom, :tel, :dateNaiss, :email, :mdp, :ville, :adresse, :codePostal,:tentativesEchouees,:dateDernierEchecConnexion,:statutCompte,:cvec);";
        $pdoStatement = $this->pdo->prepare($requete);
        $pdoStatement->execute([
            ':role' => 'Etudiant',
            ':codeINE' => $user->getCodeINE(),
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
            ':statutCompte' => 'actif',
            ':cvec' => $user->getCvec()
        ]);
    }
}