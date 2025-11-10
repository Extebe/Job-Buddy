<?php

require_once "include.php"; 

class Annonce{
    private ?string $id;
    private ?string $titre;
    private ?string $description;
    private ?int $etat; // int qui correspond à un état
    private ?string $datePublication;
    private ?string $dateDebut;
    private ?string $dateFin;
}

?>