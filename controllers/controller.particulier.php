<?php

/**
 * @brief Contrôleur spécifique aux particuliers (vide pour l'instant).
 */
class ControllerParticulier extends Controller
{
    /**
     * @brief Constructeur.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }


}