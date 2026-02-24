<?php
require_once "include.php";

/**
 * @brief Classe utilitaire pour les validations.
 */
class Valide
{
    /**
     * @brief Vérifie si une adresse email existe déjà dans la base.
     * @param string $email L'email à vérifier.
     * @return bool True si existe, False sinon.
     */
    public static function emailExiste($email)
    {
        // Connexion à la base de données
        $baseDeDonnees = Bd::getInstance();

        // Préparation de la requête pour vérifier si l'email existe
        $requete = $baseDeDonnees->getConnexion()->prepare(
            'SELECT COUNT(*) FROM Utilisateur WHERE email = :email'
        );

        // Exécution de la requête avec l'email récupéré au niveau du formulaire
        $requete->execute(['email' => $email]);

        // Retourne vrai si un utilisateur avec cet email existe, faux sinon
        return $requete->fetchColumn() > 0;
    }


    /**
     * @brief Vérifie la robustesse d'un mot de passe.
     * @details Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.
     * @param string $password Le mot de passe.
     * @return bool True si robuste, False sinon.
     */
    public static function estRobuste($password)
    {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        // La fonction preg_match retourne 1 si une correspondance est trouvée.
        return preg_match($regex, $password) === 1;
    }

        public static function cvecExiste($cvec)
    {
        // Connexion à la base de données
        $baseDeDonnees = Bd::getInstance();

        // Préparation de la requête pour vérifier si l'email existe
        $requete = $baseDeDonnees->getConnexion()->prepare(
            'SELECT COUNT(*) FROM Utilisateur WHERE cvec = :cvec'
        );

        // Exécution de la requête avec l'email récupéré au niveau du formulaire
        $requete->execute(['cvec' => $cvec]);
        // Retourne vrai si un utilisateur avec cet email existe, faux sinon
        return $requete->fetchColumn() > 0;
    }

        public static function ineExiste($ine)
    {
        // Connexion à la base de données
        $baseDeDonnees = Bd::getInstance();

        // Préparation de la requête pour vérifier si l'email existe
        $requete = $baseDeDonnees->getConnexion()->prepare(
            'SELECT COUNT(*) FROM Utilisateur WHERE codeINE = :ine'
        );

        // Exécution de la requête avec l'email récupéré au niveau du formulaire
        $requete->execute(['ine' => $ine]);
        // Retourne vrai si un utilisateur avec cet email existe, faux sinon
        return $requete->fetchColumn() > 0;
    }
}

