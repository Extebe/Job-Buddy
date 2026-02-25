<head>
    <title>Job Buddy | Aide aux devoirs, missions et petits boulots</title>

<meta name="description" content="Trouvez de l'aide rapidement pour vos devoirs, missions ou petits boulots sur Job Buddy. La plateforme de confiance pour les étudiants et particuliers.">

<link rel="icon" type="image/png" href="https://job-buddy.fr/logo_jobbuddy.png">

<meta property="og:title" content="Job Buddy | Aide aux devoirs et missions">
<meta property="og:description" content="La plateforme idéale pour trouver de l'aide ou proposer ses services.">
<meta property="og:image" content="https://job-buddy.fr/logo_jobbuddy.png">
</head>

<?php

require_once 'include.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

try  {
    if (isset($_GET['controleur'])){
        $controllerName=$_GET['controleur'];
    }else{
        $controllerName='';
    }

    if (isset($_GET['methode'])){
        $methode=$_GET['methode'];
    }else{
        $methode='';
    }

    //Gestion de la page d'accueil par défaut
    if ($controllerName == '' && $methode ==''){
        $controllerName='annonce';
        $methode='afficher';
    }

    if ($controllerName == '' ){
        throw new Exception('Le controleur n\'est pas défini');
    }

    if ($methode == '' ){
        throw new Exception('La méthode n\'est pas définie');
    }

    $controller = ControllerFactory::getController($controllerName, $loader, $twig);
  
    $controller->call($methode);
}catch (Exception $e) {
   die('Erreur : ' . $e->getMessage());
}
