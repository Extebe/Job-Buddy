<?php

/**
 * @brief Classe parente des contrôleurs.
 */
class Controller
{
    /** @var PDO $pdo Connexion à la base de données. */
    private PDO $pdo;
    /** @var \Twig\Loader\FilesystemLoader $loader Chargeur de templates Twig. */
    private \Twig\Loader\FilesystemLoader $loader;
    /** @var \Twig\Environment $twig Environnement Twig. */
    private \Twig\Environment $twig;
    /** @var array|null $get Données GET. */
    private ?array $get = null;
    /** @var array|null $post Données POST. */
    private ?array $post = null;

    /**
     * @brief Constructeur de la classe Controller.
     * @param \Twig\Environment $twig Environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        $db = Bd::getInstance();
        $this->pdo = $db->getConnexion();
        $this->loader = $loader;
        $this->twig = $twig;
        if (isset($_GET) && !empty($_GET)) {
            $this->get = $_GET;
        }
        if (isset($_POST) && !empty($_POST)) {
            $this->post = $_POST;
        }
    }

    /**
     * @brief Appelle une méthode du contrôleur si elle existe.
     * @param string $methode Nom de la méthode.
     * @return mixed Résultat de la méthode.
     * @throws Exception Si la méthode n'existe pas.
     */
    public function call(string $methode): mixed
    {
        if (!method_exists($this, $methode)) {
            throw new Exception("La méthode $methode n'existe pas dans le controller " . __CLASS__);
        }
        return $this->$methode();
    }

    /**
     * @brief Récupère la connexion PDO.
     * @return PDO Connexion.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit la connexion PDO.
     * @param PDO $pdo Connexion.
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère le chargeur Twig.
     * @return \Twig\Loader\FilesystemLoader Chargeur.
     */
    public function getLoader(): \Twig\Loader\FilesystemLoader
    {
        return $this->loader;
    }

    /**
     * @brief Définit le chargeur Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur.
     */
    public function setLoader(\Twig\Loader\FilesystemLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @brief Récupère l'environnement Twig.
     * @return \Twig\Environment Environnement.
     */
    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    /**
     * @brief Définit l'environnement Twig.
     * @param \Twig\Environment $twig Environnement.
     */
    public function setTwig(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @brief Récupère les données GET.
     * @return array|null Données GET.
     */
    public function getGet(): ?array
    {
        return $this->get;
    }

    /**
     * @brief Définit les données GET.
     * @param array|null $get Données GET.
     */
    public function setGet(?array $get)
    {
        $this->get = $get;
    }

    /**
     * @brief Récupère les données POST.
     * @return array|null Données POST.
     */
    public function getPost(): ?array
    {
        return $this->post;
    }

    /**
     * @brief Définit les données POST.
     * @param array|null $post Données POST.
     */
    public function setPost(?array $post)
    {
        $this->post = $post;
    }
}