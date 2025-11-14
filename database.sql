

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
   idAnnonceSignale VARCHAR(15),
   idUtilisateurSignale VARCHAR(15),
   PRIMARY KEY(id),
   FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id),
   FOREIGN KEY(idAnnonceSignale) REFERENCES Annonce(id),
   FOREIGN KEY(idUtilisateurSignale) REFERENCES Utilisateur(id)
);

CREATE TABLE SignalementUtilisateur(
   idSignaleur VARCHAR(15),
   idUtilisateurSignale VARCHAR(15),
   FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id),
   FOREIGN KEY(idUtilisateurSignale) REFERENCES Utilisateur(id),
   PRIMARY KEY(idSignaleur,idUtilisateurSignale)
);

CREATE TABLE SignalementAnonce(
   idSignaleur VARCHAR(15),
   idAnonnceSignale VARCHAR(15),
   FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id),
   FOREIGN KEY(idAnnonceSignale) REFERENCES Annonce(id),
   PRIMARY KEY(idSignaleur,idAnnonceSignale)
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