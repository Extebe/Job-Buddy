<?php
require_once "include.php";

/**
 * @brief DAO pour la gestion des inscriptions à la newsletter.
 */
class NewLetterDao
{
    /** @var PDO|null $pdo Objet de connexion à la base de données. */
    private ?PDO $pdo;

    /**
     * @brief Constructeur du DAO NewLetter.
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
     * @brief Insère un email dans la table de la newsletter.
     * @param NewLetter|null $newsLetter L'objet NewLetter contenant l'email.
     */
    public function insererEmail(?NewLetter $newsLetter): void
    {
        $sql = "INSERT INTO InscritNewsLetter (email)
                VALUE (:email)";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute([':email' => $newsLetter->getEmail()]);
    }

    /**
     * @brief Vérifie si un email est déjà inscrit à la newsletter.
     * @param string $email L'email à vérifier.
     * @return bool Vrai si l'email existe, faux sinon.
     */
    public function emailExisteNewsletter($email)
    {
        // Connexion à la base de données
        $baseDeDonnees = Bd::getInstance();

        // Préparation de la requête pour vérifier si l'email existe
        $requete = $baseDeDonnees->getConnexion()->prepare(
            'SELECT COUNT(*) FROM InscritNewsLetter WHERE email = :email'
        );

        // Exécution de la requête avec l'email récupéré au niveau du formulaire
        $requete->execute(['email' => $email]);

        // Retourne vrai si un utilisateur avec cet email existe, faux sinon
        return $requete->fetchColumn() > 0;
    }
}