<?php
require_once "include.php";
/**
 * @brief Classe utilitaire pour la validation de données.
 */
class Valide
{
    /**
     * @brief Vérifie si un email existe déjà dans la base de données.
     * @param string $email L'email à vérifier.
     * @return bool True si l'email existe, sinon False.
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
     * @param string $password Le mot de passe à vérifier.
     * @return bool True si le mot de passe est robuste, sinon False.
     */
    public static function estRobuste($password)
    {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        // La fonction preg_match retourne 1 si une correspondance est trouvée.
        return preg_match($regex, $password) === 1;
    }
}