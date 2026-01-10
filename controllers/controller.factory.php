<?php

require_once 'controller.annonce.php';

/**
 * @brief Classe Factory pour instancier les contrôleurs.
 */
class ControllerFactory
{
    /**
     * @brief Crée et retourne une instance du contrôleur demandé.
     * @param string $controller Nom du contrôleur (suffixe après 'Controller').
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur Twig.
     * @param \Twig\Environment $twig Environnement Twig.
     * @return Controller Instance du contrôleur.
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