<?php

require_once 'controller.annonce.php';

class ControllerFactory {
    public static function getController($controller, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig, $constantes){
        $controllerName="Controller".ucfirst($controller);
        if (!class_exists($controllerName)){
            throw new Exception("Le controleur $controllerName n'existe pas.");
        }
        return new $controllerName($twig, $loader, $constantes);
    }
}