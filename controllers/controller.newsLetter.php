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
    public function afficherPolitiqueConfidentialite()
    {
        $template = $this->getTwig();
        echo $template->render('politiqueConfidentialite.html.twig', ['user' => Utilisateur::getUser()]);
    }

    /**
     * Inscrit l'utilisateur 
     *  à la newsletter
     * @return void
     */
    public function newsletter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $pdoNewsLetter = new NewLetterDao($this->getPdo());
            $managerAnnonce = new AnnonceDao($this->getPdo());
            $tableau = $managerAnnonce->findAllAssocDispo();
            $annonces = $managerAnnonce->hydrateAll($tableau);
            $icons = Constantes::getConstantes()['icons'];
            
            foreach ($annonces as $key => $annonce) {
                $tab[$key] = $managerAnnonce->addRelations($annonce);
            }

            if (!$pdoNewsLetter->emailExisteNewsletter($email)) {
                $newsLetter = new NewLetter(null, $email);
                $pdoNewsLetter->insererEmail($newsLetter);
                $template = $this->getTwig();
                echo $template->render('index.html.twig', ['user' => Utilisateur::getUser(),
                    'annonces' => $tab,
                    'message' => 'Merci pour votre inscription à la newsletter !'
                ]);
            }
            else {
                $template = $this->getTwig();
                echo $template->render('index.html.twig', [
                    'user' => Utilisateur::getUser(),
                    'message' => 'Cet email est déjà inscrit à la newsletter.',
                    'annonces' => $tab
                ]);
                
            }
            
        }
    }
}