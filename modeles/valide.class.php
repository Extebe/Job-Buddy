<?php
require_once "include.php";
class Valide
{
    public static function emailExiste($email) {
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


    public static function estRobuste($password) {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        // La fonction preg_match retourne 1 si une correspondance est trouvée.
        return preg_match($regex, $password) === 1;
    }
}