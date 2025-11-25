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
   code INT,
   dateDebutRealisation DATE,
   dateFinRealisation DATE,
   etat VARCHAR(20) NOT NULL,
   typeService VARCHAR(25),
   titre VARCHAR(100),
   description VARCHAR(1000),
   datePublication DATE,
   code_1 VARCHAR(15) NOT NULL,
   PRIMARY KEY(code),
   FOREIGN KEY(code_1) REFERENCES Utilisateur(code)
);

CREATE TABLE Signalement(
   code INT,
   dateSignalement DATE,
   motif VARCHAR(20),
   description VARCHAR(500),
   code_1 VARCHAR(15) NOT NULL,
   code_2 INT,
   code_3 VARCHAR(15),
   PRIMARY KEY(code),
   FOREIGN KEY(code_1) REFERENCES Utilisateur(code),
   FOREIGN KEY(code_2) REFERENCES Annonce(code),
   FOREIGN KEY(code_3) REFERENCES Utilisateur(code)
);

CREATE TABLE Postuler(
   code INT,
   code_1 VARCHAR(15),
   datePostulat DATE NOT NULL,
   estAccepte LOGICAL NOT NULL,
   PRIMARY KEY(code, code_1),
   FOREIGN KEY(code) REFERENCES Annonce(code),
   FOREIGN KEY(code_1) REFERENCES Utilisateur(code)
);

CREATE TABLE PublierNote(
   code INT,
   code_1 VARCHAR(15),
   code_2 VARCHAR(15),
   note FLOAT(1,1) NOT NULL,
   commentaire VARCHAR(100),
   PRIMARY KEY(code, code_1, code_2),
   FOREIGN KEY(code) REFERENCES Annonce(code),
   FOREIGN KEY(code_1) REFERENCES Utilisateur(code),
   FOREIGN KEY(code_2) REFERENCES Utilisateur(code)
);

CREATE TABLE supprimer(
   code INT,
   dateSuppression DATE NOT NULL,
   motifSuppression VARCHAR(50),
   code_1 VARCHAR(15) NOT NULL,
   PRIMARY KEY(code),
   FOREIGN KEY(code) REFERENCES Annonce(code),
   FOREIGN KEY(code_1) REFERENCES Utilisateur(code)
);