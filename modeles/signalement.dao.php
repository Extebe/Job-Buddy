<?php

    require_once "include.php";
    class SignalementDao {
        private ?PDO $pdo;

        public function __construct(?PDO $pdo = null){
            $this->pdo = $pdo;
        }

        /**
         * Get the value of pdo
         */ 
        public function getPdo():?PDO
        {
                return $this->pdo;
        }

        /**
         * Set the value of pdo
         *
         */ 
        public function setPdo($pdo):void
        {
                $this->pdo = $pdo;
;
        }
    }
?>