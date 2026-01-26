<?php

/**
 * @brief Contrôleur gérant les signalements.
 */
class ControllerSignalement extends Controller
{
    /**
     * @brief Constructeur du contrôleur Signalement.
     * @param \Twig\Environment $twig Environnement Twig.
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur Twig.
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche la liste des signalement.
     */
    public function signalerAnnonce()
    {
        $template = $this->getTwig();

        //recupération des annonces
        $managerSignalement = new SignalementDao($this->getPdo());
        $tableau = $managerSignalement->findAllAssoc();
        $signalements = $managerSignalement->hydrateAll($tableau);

        echo $template->render('detailAnnonce.html.twig', [
            'signalements' => $signalements
        ]);
    }
}