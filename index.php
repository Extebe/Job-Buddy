<?php

require_once 'include.php';

$pdo = Bd::getInstance()->getConnexion();

$managerAnnonce = new AnnonceDao($pdo);

$annonce = $managerAnnonce->findAll();
var_dump($annonce);