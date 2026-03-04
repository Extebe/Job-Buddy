-- Suppression des tables :
DROP TABLE IF EXISTS Signalement;
DROP TABLE IF EXISTS Note;
DROP TABLE IF EXISTS Postuler;
DROP TABLE IF EXISTS Signalement;
DROP TABLE IF EXISTS Annonce;
DROP TABLE IF EXISTS Utilisateur;
DROP TABLE IF EXISTS InscritNewsLetter;


-- Création des tables :
CREATE TABLE Utilisateur(
                            id int AUTO_INCREMENT,
                            role VARCHAR(14) NOT NULL CHECK (role IN ('particulier', 'etudiant', 'administrateur')),
                            codeINE VARCHAR(20),
                            nom VARCHAR(50) NOT NULL,
                            prenom VARCHAR(50) NOT NULL,
                            tel CHAR(10) UNIQUE,
                            dateNaiss DATE NOT NULL,
                            email VARCHAR(50) NOT NULL UNIQUE,
                            mdp VARCHAR(250) NOT NULL,
                            dateSuppression DATE,
                            ville VARCHAR(50),
                            adresse VARCHAR(50),
                            codePostal VARCHAR(5),
                            tentativesEchouees INT DEFAULT 0 NOT NULL,
                            dateDernierEchecConnexion DATETIME DEFAULT NULL,
                            statutCompte ENUM('actif', 'desactive') DEFAULT 'actif',
                            statutModeration ENUM('normal', 'banni', 'suspendu', 'suspect') DEFAULT 'normal',
                            cvec VARCHAR(12),
                            photoProfil VARCHAR(255),
                            PRIMARY KEY(id)
);


CREATE TABLE Annonce(
                        id int AUTO_INCREMENT,
                        idParticulier int NOT NULL,


                        titre VARCHAR(100),
                        description VARCHAR(1000),
                        typeService VARCHAR(25) CHECK (typeService IN ('baby-sitting', 'jardinage', 'bricolage','ménage', 'transport', 'aide informatique', 'aide aux devoirs', 'autre')),
                        lieu VARCHAR(100),
                        remuneration DECIMAL(6,2) CHECK (remuneration >= 0),


                        dateDebutRealisation DATETIME,
                        dateFinRealisation DATETIME CHECK (dateFinRealisation > dateDebutRealisation),


                        etat VARCHAR(20) NOT NULL CHECK (etat IN ('disponible','accepte','confirme','termine')),


                        datePublication DATETIME,
                        dateSuppression DATETIME,
                        motifSuppression VARCHAR(50),


                        PRIMARY KEY(id),
                        FOREIGN KEY(idParticulier) REFERENCES Utilisateur(id),
                        FULLTEXT(titre, description, typeService, lieu)
);


CREATE TABLE Signalement(
                            id int AUTO_INCREMENT,
                            dateSignalement DATETIME NOT NULL,
                            motif VARCHAR(20),
                            description VARCHAR(500),
                            idSignaleur int NOT NULL,
                            idUtilisateurSignale int,
                            idAnnonceSignale int,
                            PRIMARY KEY(id),
                            FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id),
                            FOREIGN KEY(idUtilisateurSignale) REFERENCES Utilisateur(id),
                            FOREIGN KEY(idAnnonceSignale) REFERENCES Annonce(id)
);


CREATE TABLE Postuler(
                         idAnnonce int,
                         idEtudiant int,
                         datePostulat DATETIME NOT NULL,
                         estAccepte SMALLINT NOT NULL,
                         PRIMARY KEY(idAnnonce, idEtudiant),
                         FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
                         FOREIGN KEY(idEtudiant) REFERENCES Utilisateur(id)
);


CREATE TABLE Note(
                     id int AUTO_INCREMENT,
                     idAnnonce int NOT NULL,
                     idUtilisateurNoteur int NOT NULL,
                     idUtilisateurNote int NOT NULL,
                     note SMALLINT NOT NULL CHECK (note >= 0 AND note <= 5),
                     commentaire VARCHAR(100),
                     PRIMARY KEY(id),
                     FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
                     FOREIGN KEY(idUtilisateurNoteur) REFERENCES Utilisateur(id),
                     FOREIGN KEY(idUtilisateurNote) REFERENCES Utilisateur(id)
);


CREATE TABLE InscritNewsLetter(
                                  id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
                                  email varchar(50)
);

-- INSERTIONS
INSERT INTO Utilisateur VALUES(1, 'particulier', NULL, 'JobN', 'JobP', '0123456789', '1990-05-15', 'job@gmail.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Paris', '10 rue de Paris', '75001', 0, NULL, 'actif', 'normal', NULL,NULL);
INSERT INTO Utilisateur VALUES(2, 'etudiant', '123456789013', 'Martin', 'Sophie', '0123456790', '2000-10-25', 'sophie@gmail.com', '$2y$10$OrQApLZOzpj9lLVG1JEdreVjSzzMlwunN8G.7pIZe2lLxoZ1Zzh3i', NULL, 'Lyon', '20 rue de Lyon', '69001', 0, NULL, 'actif', 'normal', NULL,NULL);


INSERT INTO Annonce VALUES (1, 1, "Garde d'enfant", "Garde d'enfant pour une journée complète.", 'baby-sitting', 'Paris', 100.00, '2025-11-01 10:00:00', '2025-11-10 18:00:00', 'disponible', '2025-11-01 10:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (2, 1, 'Réparation de plomberie', "Réparation d'une fuite d'eau.", 'bricolage', 'Lyon', 80.00, '2025-11-02 09:00:00', '2025-11-05 17:00:00', 'accepte', '2025-11-02 09:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (3, 1, 'Aide aux devoirs collège', 'Recherche étudiant pour aide aux devoirs niveau collège, maths et français.', 'aide aux devoirs', 'Paris', 15.00, '2025-11-15 17:00:00', '2025-11-15 19:00:00', 'disponible', '2025-11-05 09:30:00', NULL, NULL);
INSERT INTO Annonce VALUES (4, 1, 'Nettoyage appartement', 'Besoin d’aide pour le ménage d’un appartement de 50m².', 'ménage', 'Paris', 60.00, '2025-11-20 09:00:00', '2025-11-20 12:00:00', 'disponible', '2025-11-06 14:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (5, 1, 'Jardinage week-end', 'Tonte de pelouse et taille de haies dans un petit jardin.', 'jardinage', 'Versailles', 90.00, '2025-11-22 08:30:00', '2025-11-22 13:00:00', 'accepte', '2025-11-07 10:15:00', NULL, NULL);
INSERT INTO Annonce VALUES (6, 1, 'Déménagement léger', 'Aide pour transporter quelques meubles et cartons.', 'transport', 'Paris', 120.00, '2025-11-25 14:00:00', '2025-11-25 18:00:00', 'disponible', '2025-11-08 16:45:00', NULL, NULL);
INSERT INTO Annonce VALUES (7, 1, 'Assistance informatique', 'Installation d’un nouvel ordinateur et configuration basique.', 'aide informatique', 'Paris', 50.00, '2025-11-28 10:00:00', '2025-11-28 12:00:00', 'termine', '2025-11-09 11:20:00', NULL, NULL);
INSERT INTO Annonce VALUES (8, 1, 'Garde d’enfant soirée', 'Garde d’un enfant de 6 ans en soirée.', 'baby-sitting', 'Paris', 70.00, '2025-12-01 18:00:00', '2025-12-01 22:00:00', 'disponible', '2025-11-10 09:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (9, 1, 'Montage meuble IKEA', 'Montage d’une armoire et d’un lit.', 'bricolage', 'Paris', 85.00, '2025-12-03 09:00:00', '2025-12-03 13:00:00', 'disponible', '2025-11-11 14:30:00', NULL, NULL);
INSERT INTO Annonce VALUES (10, 1, 'Ménage après déménagement', 'Nettoyage complet après départ des locataires.', 'ménage', 'Paris', 100.00, '2025-12-05 08:00:00', '2025-12-05 12:00:00', 'accepte', '2025-11-12 10:15:00', NULL, NULL);
INSERT INTO Annonce VALUES (11, 1, 'Aide informatique senior', 'Assistance pour utilisation basique d’un smartphone.', 'aide informatique', 'Paris', 40.00, '2025-12-06 15:00:00', '2025-12-06 17:00:00', 'disponible', '2025-11-13 11:45:00', NULL, NULL);
INSERT INTO Annonce VALUES (12, 1, 'Transport courses', 'Aide pour transporter des courses volumineuses.', 'transport', 'Paris', 30.00, '2025-12-08 16:00:00', '2025-12-08 17:30:00', 'termine', '2025-11-14 09:20:00', NULL, NULL);
INSERT INTO Annonce VALUES (13, 1, 'Garde d’animaux', 'Visites quotidiennes pour nourrir un chat.', 'autre', 'Paris', 45.00, '2025-12-10 09:00:00', '2025-12-15 09:30:00', 'disponible', '2025-11-15 08:50:00', NULL, NULL);
INSERT INTO Annonce VALUES (14, 1, 'Cours initiation informatique', 'Initiation à l’ordinateur et à Internet.', 'aide informatique', 'Paris', 60.00, '2025-12-18 14:00:00', '2025-12-18 16:00:00', 'accepte', '2025-11-16 13:10:00', NULL, NULL);
INSERT INTO Annonce VALUES (15, 1, 'Petit bricolage maison', 'Réparation de poignées et remplacement d’ampoules.', 'bricolage', 'Paris', 55.00, '2025-12-20 10:00:00', '2025-12-20 12:00:00', 'disponible', '2025-11-17 15:00:00', NULL, NULL);


INSERT INTO Signalement VALUES(1, '2025-11-03 14:30:00', 'Contenu inapproprié', "Annonce de garde d'enfant avec des détails douteux.", 2, NULL, 1);
INSERT INTO Signalement VALUES(2, '2025-11-04 11:00:00', 'Faux service', 'Annonce de bricolage pour un travail qui semble être une fraude.', 1, NULL, 2);
INSERT INTO Signalement VALUES (3, '2025-11-06 16:45:00', 'Comportement', 'L’étudiant ne s’est pas présenté à la mission acceptée.', 1, 2, NULL);
INSERT INTO Signalement VALUES (4, '2025-11-07 09:20:00', 'Paiement', 'Le particulier n’a pas payé la mission après réalisation.', 2, 1, NULL);


INSERT INTO Postuler VALUES(1, 2, '2025-11-03 14:00:00', TRUE);
INSERT INTO Postuler VALUES(2, 2, '2025-11-04 10:00:00', FALSE);


INSERT INTO Note VALUES(1, 1, 1, 2, 4, 'Très bonne expérience, mais un peu trop cher.');
INSERT INTO Note VALUES(2, 2, 2, 1, 2, "Le travail n'a pas été bien fait.");

-- Nouvelle insertion --

-- Nouveaux Particuliers
INSERT INTO Utilisateur VALUES(3, 'particulier', NULL, 'Dupont', 'Jean', '0612345671', '1985-03-12', 'jean.dupont@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Paris', '15 rue Lafayette', '75009', 0, NULL, 'actif', 'normal', NULL, NULL);
INSERT INTO Utilisateur VALUES(4, 'particulier', NULL, 'Leroy', 'Marie', '0612345672', '1978-07-22', 'marie.leroy@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Lyon', '8 place Bellecour', '69002', 0, NULL, 'actif', 'normal', NULL, NULL);
INSERT INTO Utilisateur VALUES(5, 'particulier', NULL, 'Bernard', 'Luc', '0612345673', '1992-11-05', 'luc.bernard@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Marseille', '22 rue de la République', '13001', 0, NULL, 'actif', 'normal', NULL, NULL);
INSERT INTO Utilisateur VALUES(6, 'particulier', NULL, 'Dubois', 'Claire', '0612345674', '1990-01-30', 'claire.dubois@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Bordeaux', '10 Cours de l''Intendance', '33000', 0, NULL, 'actif', 'normal', NULL, NULL);

-- Nouveaux Étudiants
INSERT INTO Utilisateur VALUES(7, 'etudiant', '20230001', 'Richard', 'Emma', '0712345674', '2002-04-18', 'emma.richard@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Paris', '5 bd Raspail', '75007', 0, NULL, 'actif', 'normal', NULL, NULL);
INSERT INTO Utilisateur VALUES(8, 'etudiant', '20230002', 'Simon', 'Lucas', '0712345675', '2001-09-30', 'lucas.simon@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Lyon', '12 av Jean Jaurès', '69007', 0, NULL, 'actif', 'normal', NULL, NULL);
INSERT INTO Utilisateur VALUES(9, 'etudiant', '20230003', 'Michel', 'Chloe', '0712345676', '2003-01-15', 'chloe.michel@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Marseille', '45 cours Pierre Puget', '13006', 0, NULL, 'actif', 'normal', NULL, NULL);
INSERT INTO Utilisateur VALUES(10, 'etudiant', '20230004', 'Lefevre', 'Hugo', '0712345677', '2000-11-20', 'hugo.lefevre@email.com', '$2y$10$P6Y.VZQx4p0HpM0znpUgSe.AMZHFYc6p0x.vLRKpW1aXy2o8DS6qO', NULL, 'Bordeaux', '2 rue Sainte-Catherine', '33000', 0, NULL, 'actif', 'normal', NULL, NULL);

-- Mix de statuts (disponible, accepte, termine) et de villes
INSERT INTO Annonce VALUES (16, 3, "Tonte de pelouse estivale", "Cherche étudiant pour tondre un jardin de 300m².", 'jardinage', 'Paris', 40.00, '2026-04-10 10:00:00', '2026-04-10 12:00:00', 'disponible', '2026-03-01 09:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (17, 4, "Aide pour déménagement", "Besoin de bras pour descendre des cartons du 3ème étage sans ascenseur.", 'transport', 'Lyon', 75.00, '2026-03-15 08:00:00', '2026-03-15 12:00:00', 'accepte', '2026-02-28 14:30:00', NULL, NULL);
INSERT INTO Annonce VALUES (18, 5, "Cours de maths Terminale", "Soutien scolaire en mathématiques pour préparation au bac.", 'aide aux devoirs', 'Marseille', 25.00, '2026-03-20 17:00:00', '2026-03-20 19:00:00', 'disponible', '2026-03-02 11:15:00', NULL, NULL);
INSERT INTO Annonce VALUES (19, 3, "Nettoyage de printemps (Vitres)", "Nettoyage complet des vitres d'un grand appartement.", 'ménage', 'Paris', 50.00, '2026-03-12 14:00:00', '2026-03-12 17:00:00', 'termine', '2026-02-20 10:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (20, 4, "Sortie d'école maternelle", "Aller chercher deux enfants à l'école maternelle les mardis.", 'baby-sitting', 'Lyon', 30.00, '2026-03-10 16:30:00', '2026-03-10 18:30:00', 'disponible', '2026-03-03 08:45:00', NULL, NULL);
INSERT INTO Annonce VALUES (21, 6, "Peinture petite chambre", "Besoin d'aide pour peindre les murs d'une chambre de 12m².", 'bricolage', 'Bordeaux', 90.00, '2026-03-25 09:00:00', '2026-03-25 18:00:00', 'disponible', '2026-03-04 10:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (22, 5, "Configuration Smart TV", "Aide pour installer et paramétrer une nouvelle télévision connectée.", 'aide informatique', 'Marseille', 35.00, '2026-03-08 18:00:00', '2026-03-08 19:30:00', 'termine', '2026-03-01 14:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (23, 6, "Promenade chien", "Cherche personne pour promener mon golden retriever 1h par jour ce weekend.", 'autre', 'Bordeaux', 40.00, '2026-03-14 10:00:00', '2026-03-15 11:00:00', 'accepte', '2026-03-05 09:30:00', NULL, NULL);
INSERT INTO Annonce VALUES (24, 3, "Aide montage PC", "Je cherche quelqu'un s'y connaissant en composants pour monter ma tour.", 'aide informatique', 'Paris', 60.00, '2026-03-18 14:00:00', '2026-03-18 16:00:00', 'disponible', '2026-03-06 11:00:00', NULL, NULL);
INSERT INTO Annonce VALUES (25, 4, "Désherbage allée", "Arrachage de mauvaises herbes dans une allée pavée.", 'jardinage', 'Lyon', 45.00, '2026-03-22 14:00:00', '2026-03-22 17:00:00', 'disponible', '2026-03-06 15:45:00', NULL, NULL);
INSERT INTO Annonce VALUES (26, 5, "Nettoyage voiture", "Lavage intérieur et extérieur complet d'une berline.", 'ménage', 'Marseille', 50.00, '2026-03-16 10:00:00', '2026-03-16 12:30:00', 'disponible', '2026-03-07 08:20:00', NULL, NULL);
INSERT INTO Annonce VALUES (27, 6, "Aller chercher meuble", "Besoin d'un véhicule avec conducteur pour récupérer un canapé sur Leboncoin.", 'transport', 'Bordeaux', 55.00, '2026-03-11 18:00:00', '2026-03-11 19:30:00', 'accepte', '2026-03-07 16:00:00', NULL, NULL);

-- Format : id_annonce, id_etudiant, date_postulation, est_accepte (TRUE/FALSE)
INSERT INTO Postuler VALUES(16, 7, '2026-03-02 10:00:00', FALSE);
INSERT INTO Postuler VALUES(17, 8, '2026-03-01 15:30:00', TRUE);
INSERT INTO Postuler VALUES(18, 9, '2026-03-03 09:20:00', FALSE);
INSERT INTO Postuler VALUES(19, 7, '2026-02-21 11:00:00', TRUE);
INSERT INTO Postuler VALUES(20, 8, '2026-03-04 14:00:00', FALSE);
INSERT INTO Postuler VALUES(22, 9, '2026-03-02 18:45:00', TRUE);
INSERT INTO Postuler VALUES(23, 10, '2026-03-06 08:15:00', TRUE);
INSERT INTO Postuler VALUES(27, 10, '2026-03-08 12:30:00', TRUE);

-- Notes liées aux annonces terminées (id 19 et 22)
-- Note de l'étudiant 7 vers le particulier 3 (Annonce 19)
INSERT INTO Note VALUES(3, 19, 7, 3, 5, "Propriétaire très sympa, tout le matériel de nettoyage était fourni.");
-- Note du particulier 3 vers l'étudiant 7 (Annonce 19)
INSERT INTO Note VALUES(4, 19, 3, 7, 5, "Travail impeccable, les vitres sont nickel ! Je recommande.");

-- Note du particulier 5 vers l'étudiant 9 (Annonce 22)
INSERT INTO Note VALUES(5, 22, 5, 9, 4, "Très compétente, la télé fonctionne bien, mais a eu un peu de retard.");

-- Notes pour l'annonce 7 (Assistance informatique, terminée, de Particulier 1)
INSERT INTO Note VALUES(6, 7, 1, 8, 5, 'Lucas a été très patient et a tout configuré rapidement. Parfait !');
INSERT INTO Note VALUES(7, 7, 8, 1, 4, 'Mission agréable, personne sympathique. Un peu difficile de se garer dans la rue par contre.');

-- Notes pour l'annonce 12 (Transport courses, terminée, de Particulier 1)
INSERT INTO Note VALUES(8, 12, 1, 10, 5, 'Hugo est très costaud, il a monté toutes les courses au 4ème étage avec le sourire.');
INSERT INTO Note VALUES(9, 12, 10, 1, 3, 'La mission s''est bien passée, mais le volume de courses était bien plus important que ce qui était décrit.');

-- Suite des notes pour l'annonce 22 (Smart TV, de Particulier 5 vers Etudiant 9)
-- L'étudiant 9 évalue à son tour le particulier 5
INSERT INTO Note VALUES(10, 22, 9, 5, 5, 'Luc a été super accueillant, il m''a même offert un café pendant que je configurais la télé !');

-- Notes pour l'annonce 17 (Aide pour déménagement, de Particulier 4)
INSERT INTO Note VALUES(11, 17, 4, 8, 5, 'Lucas est ponctuel et très efficace. Les cartons ont été descendus en un temps record.');
INSERT INTO Note VALUES(12, 17, 8, 4, 5, 'Super déménagement, Marie avait tout bien préparé et scotché à l''avance. Top !');

-- Notes pour l'annonce 27 (Aller chercher meuble, de Particulier 6)
INSERT INTO Note VALUES(13, 27, 6, 10, 4, 'Hugo a été très pro, le canapé est arrivé sans une égratignure. Juste un petit retard de 10 min à l''arrivée.');
INSERT INTO Note VALUES(14, 27, 10, 6, 5, 'Très bonne communication avec Claire pour l''organisation du transport. Je recommande.');

-- Nouvelles notes pour l'Etudiant 2 (Sophie) sur d'autres missions passées
INSERT INTO Note VALUES(15, 14, 1, 2, 4, 'Les explications pour l''informatique étaient claires, j''arrive enfin à envoyer des emails avec pièces jointes.');
INSERT INTO Note VALUES(16, 8, 1, 2, 5, 'Sophie a un excellent contact avec les enfants. Mon fils a adoré la soirée jeux de société.');

-- Nouvelles notes pour l'Etudiant 7 (Emma)
INSERT INTO Note VALUES(17, 10, 1, 7, 5, 'L''appartement était étincelant pour l''état des lieux. Merci Emma !');
INSERT INTO Note VALUES(18, 10, 7, 1, 4, 'Bonne mission, tout le matériel de nettoyage nécessaire était bien sur place.');

-- Nouvelles notes pour le Particulier 6 (Claire)
INSERT INTO Note VALUES(19, 23, 10, 6, 5, 'Le golden retriever de Claire est adorable, une promenade très agréable.');
INSERT INTO Note VALUES(20, 23, 6, 10, 5, 'Mon chien est rentré ravi et bien dépensé. Hugo a l''habitude des animaux, ça se voit.');

-- Quelques notes moyennes/basses pour le réalisme
INSERT INTO Note VALUES(21, 5, 1, 9, 2, 'Le travail n''a été fait qu''à moitié, la taille des haies n''était pas droite.');
INSERT INTO Note VALUES(22, 5, 9, 1, 1, 'Je n''avais pas les bons outils à disposition, impossible de faire un travail correct dans ces conditions.');

-- TRIGGERS
DELIMITER $$
DROP TRIGGER IF EXISTS verif_bcp_signalement$$
CREATE TRIGGER verif_bcp_signalement
    BEFORE INSERT
    ON Signalement
    FOR EACH ROW
BEGIN
    DECLARE nbSignalement INT;
    SELECT COUNT(*) INTO nbSignalement FROM Signalement WHERE idUtilisateurSignale = NEW.idUtilisateurSignale;
    IF nbSignalement >= 5 THEN
    UPDATE Utilisateur SET statutModeration = 'suspect' WHERE id = NEW.idUtilisateurSignale;
END IF;
END$$
