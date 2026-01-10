<?php

require_once 'controller.annonce.php';

/**
 * @brief Factory pour l'instanciation des contrôleurs.
 */
class ControllerFactory
{
    /**
     * @brief Crée et retourne une instance de contrôleur.
     * @param string $controller Le nom du contrôleur (sans le préfixe "Controller").
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de fichiers Twig.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @return object L'instance du contrôleur.
     * @throws Exception Si le contrôleur n'existe pas.
     */
    public static function getController($controller, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $controllerName = "Controller" . ucfirst($controller);
        if (!class_exists($controllerName)) {
            throw new Exception("Le controleur $controllerName n'existe pas.");
        }
        return new $controllerName($twig, $loader);
    }
}