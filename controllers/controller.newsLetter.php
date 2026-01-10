<?php
/**
 * @brief Contrôleur gérant la newsletter et la politique de confidentialité.
 */
class ControllerNewsLetter extends Controller
{
    /**
     * @brief Constructeur du contrôleur newsletter.
     * @param \Twig\Environment $twig L'environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur Twig.
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
    public function afficherPolitiqueConfidentialite()
    {
        $template = $this->getTwig();
        echo $template->render('politiqueConfidentialite.html.twig', ['user' => Utilisateur::getUser()]);
    }
}