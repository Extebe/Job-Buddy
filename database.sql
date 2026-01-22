-- Suppression des tables :
DROP TABLE IF EXISTS SignalementAnonce;
DROP TABLE IF EXISTS SignalementUtilisateur;
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
  codeINE VARCHAR(20) UNIQUE,
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
  dateDernierEchecConnexion DATETIME DEFAULT NULL, -- Date et heure du dernier échec de connexion
  statutCompte ENUM('actif', 'desactive', 'suspect') DEFAULT 'actif',
  cvec VARCHAR(12),
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


  etat VARCHAR(20) NOT NULL CHECK (etat IN ('disponible','accepte','termine')),


  datePublication DATETIME,
  dateSuppression DATETIME,
  motifSuppression VARCHAR(50),


  PRIMARY KEY(id),
  FOREIGN KEY(idParticulier) REFERENCES Utilisateur(id)
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
  estAccepte BOOLEAN NOT NULL,
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
INSERT INTO Utilisateur VALUES(1, 'particulier', '123456789012', 'Dupont', 'Jean', '0123456789', '1990-05-15', 'jean.dupont@example.com', 'mdp123', NULL, 'Paris', '10 rue de Paris', '75001', 0, NULL, 'actif',NULL);
INSERT INTO Utilisateur VALUES(2, 'etudiant', '123456789013', 'Martin', 'Sophie', '0123456790', '2000-10-25', 'sophie.martin@example.com', 'mdp456', NULL, 'Lyon', '20 rue de Lyon', '69001', 0, NULL, 'actif',NULL);


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

