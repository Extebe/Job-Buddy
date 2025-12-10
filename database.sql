

CREATE TABLE Utilisateur(
   id int,
   role VARCHAR(14) NOT NULL CHECK (role IN ('PARTICULIER', 'ETUDIANT', 'ADMINISTRATEUR')),
   codeINE VARCHAR(20),
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   tel CHAR(10),
   dateNaiss DATE NOT NULL,
   email VARCHAR(50) NOT NULL,
   mdp VARCHAR(250) NOT NULL,
   dateSuppression DATE,
   ville VARCHAR(50),
   adresse VARCHAR(50),
   codePostale VARCHAR(5),
   PRIMARY KEY(id)
);

CREATE TABLE Annonce(
                        id int,
                        dateDebutRealisation DATETIME,
                        dateFinRealisation DATETIME CHECK (dateFinRealisation > dateDebutRealisation),
                        etat VARCHAR(20) NOT NULL CHECK (etat IN ('DISPONIBLE','ACCEPTE','TERMINE')),
                        typeService VARCHAR(25) CHECK (typeService IN ('baby-sitting', 'jardinage', 'bricolage','ménage', 'transport', 'aide informatique', 'aide aux devoirs', 'autre')),
                        titre VARCHAR(100),
                        description VARCHAR(1000),
                        datePublication DATETIME,
                        dateSuppression DATETIME,
                        motifSuppression VARCHAR(50),
                        idParticulier int NOT NULL,
                        PRIMARY KEY(id),
                        FOREIGN KEY(idParticulier) REFERENCES Utilisateur(id)
);

CREATE TABLE Signalement(
                            id int,
                            dateSignalement DATETIME NOT NULL,
                            motif VARCHAR(20),
                            description VARCHAR(500),
                            idSignaleur int NOT NULL,
                            PRIMARY KEY(id),
                            FOREIGN KEY(idSignaleur) REFERENCES Utilisateur(id)
);

CREATE TABLE SignalementUtilisateur(
                                       idSignalement int,
                                       idUtilisateurSignale int,
                                       FOREIGN KEY(idSignalement) REFERENCES Signalement(id),
                                       FOREIGN KEY(idUtilisateurSignale) REFERENCES Utilisateur(id),
                                       PRIMARY KEY(idSignalement,idUtilisateurSignale)
);

CREATE TABLE SignalementAnonce(
                                  idSignalement int,
                                  idAnnonceSignale int,
                                  FOREIGN KEY(idSignalement) REFERENCES Signalement(id),
                                  FOREIGN KEY(idAnnonceSignale) REFERENCES Annonce(id),
                                  PRIMARY KEY(idSignalement,idAnnonceSignale)
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
                     id int,
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
