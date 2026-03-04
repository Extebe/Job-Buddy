<?php

require_once "vendor/autoload.php";

require_once "config/twig.php";

require_once "modeles/note.class.php";
require_once "modeles/note.dao.php";

require_once "modeles/utilisateur.class.php";
require_once "modeles/utilisateur.dao.php";

require_once "modeles/etudiant.class.php";
require_once "modeles/etudiant.dao.php";

require_once 'modeles/particulier.class.php';
require_once 'modeles/particulier.dao.php';

require_once "modeles/annonce.class.php";
require_once "modeles/annonce.dao.php";

// Ajout du modèle qui gère la connexion à la base de données mySQL
require_once "modeles/bd.class.php";

require_once "modeles/signalement.class.php";
require_once "modeles/signalement.dao.php";

require_once "modeles/newsLetter.class.php";
require_once "modeles/newsLetter.dao.php";

require_once "modeles/valide.class.php";

require_once "modeles/notification.class.php";
require_once "modeles/notification.dao.php";

// Controleurs
require_once "controllers/controller.class.php";
require_once "controllers/controller.factory.php";
require_once "controllers/controller.utilisateur.php";
require_once "controllers/controller.note.php";
require_once "controllers/controller.particulier.php";
require_once "controllers/controller.newsLetter.php";
require_once "controllers/controller.signalement.php";
require_once "controllers/controller.notification.php";

// Constantes
require_once "config/constantes.class.php";

