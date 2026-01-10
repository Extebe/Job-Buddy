<?php

/**
 * @brief Contrôleur pour les fonctionnalités liés aux particuliers.
 */
class ControllerParticulier extends Controller
{
    /**
     * @brief Constructeur du contrôleur particulier.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }


}