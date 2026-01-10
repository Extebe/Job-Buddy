<?php
/**
 * @brief Classe de base pour tous les contrôleurs du site.
 */
class Controller
{
    /** @var PDO $pdo Instance de connexion à la base de données. */
    private PDO $pdo;
    /** @var \Twig\Loader\FilesystemLoader $loader Chargeur de fichiers Twig. */
    private \Twig\Loader\FilesystemLoader $loader;
    /** @var \Twig\Environment $twig Environnement Twig. */
    private \Twig\Environment $twig;
    /** @var array|null $get Tableau des paramètres GET. */
    private ?array $get = null;
    /** @var array|null $post Tableau des paramètres POST. */
    private ?array $post = null;

    /**
     * @brief Constructeur de la classe Controller.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur Twig.
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
     * @brief Appelle une méthode du contrôleur.
     * @param string $methode Le nom de la méthode à appeler.
     * @return mixed Le résultat de la méthode.
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
     * @brief Récupère l'instance PDO.
     * @return PDO L'instance PDO.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * @brief Définit l'instance PDO.
     * @param PDO $pdo L'instance PDO.
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère le loader Twig.
     * @return \Twig\Loader\FilesystemLoader Le loader.
     */
    public function getLoader(): \Twig\Loader\FilesystemLoader
    {
        return $this->loader;
    }

    /**
     * @brief Définit le loader Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le loader.
     */
    public function setLoader(\Twig\Loader\FilesystemLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @brief Récupère l'environnement Twig.
     * @return \Twig\Environment L'environnement Twig.
     */
    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    /**
     * @brief Définit l'environnement Twig.
     * @param \Twig\Environment $twig L'environnement.
     */
    public function setTwig(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @brief Récupère les paramètres GET.
     * @return array|null Le tableau $_GET.
     */
    public function getGet(): ?array
    {
        return $this->get;
    }

    /**
     * @brief Définit les paramètres GET.
     * @param array|null $get Le tableau $_GET.
     */
    public function setGet(?array $get)
    {
        $this->get = $get;
    }

    /**
     * @brief Récupère les paramètres POST.
     * @return array|null Le tableau $_POST.
     */
    public function getPost(): ?array
    {
        return $this->post;
    }

    /**
     * @brief Définit les paramètres POST.
     * @param array|null $post Le tableau $_POST.
     */
    public function setPost(?array $post)
    {
        $this->post = $post;
    }
}