

CREATE TABLE Utilisateur(
   id VARCHAR(15),
   role VARCHAR(14) NOT NULL CHECK (role IN ('PARTICULIER', 'ETUDIANT', 'ADMINISTRATEUR')),
   codeINE VARCHAR(20),
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   tel CHAR(10),
   dateNaiss DATE NOT NULL,
   email VARCHAR(50) NOT NULL,
   mdp VARCHAR(50) NOT NULL,
   dateSuppression DATE,
   ville VARCHAR(50),
   adresse VARCHAR(50),
   codePostale VARCHAR(5),
   PRIMARY KEY(id)
);

CREATE TABLE Annonce(
   id VARCHAR(15),
   dateDebutRealisation DATETIME,
   dateFinRealisation DATETIME CHECK (dateFinRealisation > dateDebutRealisation),
   etat VARCHAR(20) NOT NULL CHECK (etat IN ('DISPONIBLE','ACCEPTE','TERMINE')),
   typeService VARCHAR(25) CHECK (typeService IN ('baby-sitting', 'jardinage', 'bricolage','ménage', 'transport', 'aide informatique', 'autre')),
   titre VARCHAR(100),
   description VARCHAR(1000),
   datePublication DATETIME,
   dateSupression DATETIME,
   motifSupression VARCHAR(50),
   idParticulier VARCHAR(15) NOT NULL,
   PRIMARY KEY(id),
   FOREIGN KEY(idParticulier) REFERENCES Utilisateur(id)
);

CREATE TABLE Signalement(
   id VARCHAR(15),
   dateSignalement DATETIME NOT NULL,
   motif VARCHAR(20),
   description VARCHAR(500),
   idSignaleur VARCHAR(15) NOT NULL,
   PRIMARY KEY(id),
   FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id)
);

CREATE TABLE SignalementUtilisateur(
   idSignalement VARCHAR(15),
   idUtilisateurSignale VARCHAR(15),
   FOREIGN KEY(idSignalement) REFERENCES Signalement(id),
   FOREIGN KEY(idUtilisateurSignale) REFERENCES Utilisateur(id),
   PRIMARY KEY(idSignalement,idUtilisateurSignale)
);

CREATE TABLE SignalementAnonce(
   idSignalement VARCHAR(15),
   idAnnonceSignale VARCHAR(15),
   FOREIGN KEY(idSignalement) REFERENCES Signalement(id),
   FOREIGN KEY(idAnnonceSignale) REFERENCES Annonce(id),
   PRIMARY KEY(idSignalement,idAnnonceSignale)
);

CREATE TABLE Postuler(
   idAnnonce VARCHAR(15),
   idEtudiant VARCHAR(15),
   datePostulat DATETIME NOT NULL,
   estAccepte BOOLEAN NOT NULL,
   PRIMARY KEY(idAnnonce, idEtudiant),
   FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
   FOREIGN KEY(idEtudiant) REFERENCES Utilisateur(id)
);

CREATE TABLE Note(
   id VARCHAR(15),
   idAnnonce VARCHAR(15) NOT NULL,
   idUtilisateurNoteur VARCHAR(15) NOT NULL,
   idUtilisateurNote VARCHAR(15) NOT NULL,
   note SMALLINT NOT NULL CHECK (note >= 0 AND note <= 5),
   commentaire VARCHAR(100),
   PRIMARY KEY(id),
   FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
   FOREIGN KEY(idUtilisateurNoteur) REFERENCES Utilisateur(id),
   FOREIGN KEY(idUtilisateurNote) REFERENCES Utilisateur(id)
);



INSERT INTO Utilisateur VALUES('U001', 'PARTICULIER', '123456789012', 'Dupont', 'Jean', '0123456789', '1990-05-15', 'jean.dupont@example.com', 'mdp123', NULL, 'Paris', '10 rue de Paris', '75001');
INSERT INTO Utilisateur VALUES('U002', 'ETUDIANT', '123456789013', 'Martin', 'Sophie', '0123456790', '2000-10-25', 'sophie.martin@example.com', 'mdp456', NULL, 'Lyon', '20 rue de Lyon', '69001');
INSERT INTO Utilisateur VALUES('U003', 'ADMINISTRATEUR', '123456789014', 'Leblanc', 'Pierre', '0123456791', '1985-03-10', 'pierre.leblanc@example.com', 'admin123', NULL, 'Marseille', '30 rue de Marseille', '13001');

INSERT INTO Annonce VALUES('A001', '2025-11-01 10:00:00', '2025-11-10 18:00:00', 'DISPONIBLE', 'baby-sitting', "Garde d'enfant", "Garde d'enfant pour une journée complète.", '2025-11-01 10:00:00', NULL, NULL, 'U001');
INSERT INTO Annonce VALUES('A002', '2025-11-02 09:00:00', '2025-11-05 17:00:00', 'ACCEPTE', 'bricolage', 'Réparation de plomberie', "Réparation d'une fuite d'eau.", '2025-11-02 09:00:00', NULL, NULL, 'U002');

INSERT INTO Signalement VALUES('S001', '2025-11-03 14:30:00', 'Contenu inapproprié', "Annonce de garde d'enfant avec des détails douteux.", 'U002');
INSERT INTO Signalement VALUES('S002', '2025-11-04 11:00:00', 'Faux service', 'Annonce de bricolage pour un travail qui semble être une fraude.', 'U003');

INSERT INTO SignalementUtilisateur VALUES('S001', 'U003');
INSERT INTO SignalementUtilisateur VALUES('S002', 'U001');

INSERT INTO SignalementAnonce VALUES('S001', 'A001');
INSERT INTO SignalementAnonce VALUES('S002', 'A002');

INSERT INTO Postuler VALUES('A001', 'U002', '2025-11-03 14:00:00', TRUE);
INSERT INTO Postuler VALUES('A002', 'U003', '2025-11-04 10:00:00', FALSE);

INSERT INTO Note VALUES('N001', 'A001', 'U001', 'U002', 4, 'Très bonne expérience, mais un peu trop cher.');
INSERT INTO Note VALUES('N002', 'A002', 'U003', 'U001', 2, "Le travail n'a pas été bien fait.");