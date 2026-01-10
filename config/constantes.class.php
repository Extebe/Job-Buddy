<?php


use Symfony\Component\Yaml\Yaml;


class Constantes
{
   public static $constantes;
   private static ?Constantes $instance = null;


   private function __construct()
   {
       self::$constantes = Yaml::parseFile("./config/constantes.yaml");
   }


   public static function getConstantes(){
       if(self::$instance == null){
           self::$instance = new Constantes();
       }
       return self::$constantes;
   }
}
