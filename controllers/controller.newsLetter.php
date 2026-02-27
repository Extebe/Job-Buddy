<?php

/**
 * @brief Contrôleur gérant la newsletter.
 */
class ControllerNewsLetter extends Controller
{
    /**
     * @brief Constructeur du contrôleur NewsLetter.
     * @param \Twig\Environment $twig
     * @param \Twig\Loader\FilesystemLoader $loader
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche la page d'inscription à la newsletter.
     */
    public function afficher()
    {
        $template = $this->getTwig();
        echo $template->render('inscriptionNewsLetter.html.twig', ['user' => Utilisateur::getUser()]);
    }

    /**
     * @brief Affiche la politique de confidentialité.
     */
}