<?php

class Controller {
    private PDO $pdo;
    private \Twig\Loader\FilesystemLoader $loader;
    private \Twig\Environment $twig;
    private ?array $get = null;
    private ?array $post = null;

    public function __construct(\Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $db = Bd::getInstance();
        $this->pdo = $db->getConnexion();

        $this->loader = $loader;    
        $this->twig = $twig;

        if(isset($_GET) && !empty($_GET)){
            $this->get = $_GET;
        }
        if(isset($_POST) && !empty($_POST)){
            $this->post = $_POST;
        }
    }


    public function call(string $methode): mixed{
        if(!method_exists($this, $methode)){
            throw new Exception("La méthode $methode n'existe pas dans le controller ". __CLASS__);
        }
        return $this->$methode();
    }
}