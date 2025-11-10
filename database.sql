CREATE TABLE Utilisateur(
   id VARCHAR(15),
   role VARCHAR(11) NOT NULL,
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
   id INT,
   dateDebutRealisation DATE,
   dateFinRealisation DATE,
   etat VARCHAR(20) NOT NULL,
   typeService VARCHAR(25),
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
   id INT,
   dateSignalement DATETIME NOT NULL,
   motif VARCHAR(20),
   description VARCHAR(500),
   idSignaleur VARCHAR(15) NOT NULL,
   idAnnonceSignale INT,
   idUtilisateurSignale VARCHAR(15),
   PRIMARY KEY(id),
   FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id),
   FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
   FOREIGN KEY(idSignale) REFERENCES Utilisateur(id)
);

CREATE TABLE Postuler(
   idAnnonce INT,
   idEtudiant VARCHAR(15),
   datePostulat DATETIME NOT NULL,
   estAccepte LOGICAL NOT NULL,
   PRIMARY KEY(id, id),
   FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
   FOREIGN KEY(idEtudiant) REFERENCES Utilisateur(id)
);

CREATE TABLE Note(
   id INT,
   idAnnonce INT NOT NULL;
   idUtilisateurNoteur VARCHAR(15) NOT NULL,
   idUtilisateurNote VARCHAR(15) NOT NULL,
   note SMALLINT NOT NULL,
   commentaire VARCHAR(100),
   PRIMARY KEY(id),
   FOREIGN KEY(idAnnonce) REFERENCES Annonce(id),
   FOREIGN KEY(idUtilisateurNoteur) REFERENCES Utilisateur(id),
   FOREIGN KEY(idUtilisateurNote) REFERENCES Utilisateur(id)
);