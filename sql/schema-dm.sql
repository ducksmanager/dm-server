CREATE TABLE `abonnements`
(
    `ID`             int(11)    NOT NULL AUTO_INCREMENT,
    `ID_Utilisateur` int(11)    NOT NULL,
    `Pays`           varchar(3) NOT NULL,
    `Magazine`       varchar(6) NOT NULL,
    `Date_debut`     date       NOT NULL,
    `Date_fin`       date       NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `abonnements_unique` (`Pays`, `Magazine`, `ID_Utilisateur`, `Date_debut`, `Date_fin`),
    KEY `abonnements_users_ID_fk` (`ID_Utilisateur`),
    CONSTRAINT `abonnements_users_ID_fk` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `abonnements_sorties`
(
    `Pays`            varchar(3) NOT NULL,
    `Magazine`        varchar(6) NOT NULL,
    `Numero`          varchar(8) NOT NULL,
    `Date_sortie`     date       NOT NULL,
    `Numeros_ajoutes` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`Pays`, `Magazine`, `Numero`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `achats`
(
    `ID_Acquisition` int(11)      NOT NULL AUTO_INCREMENT,
    `ID_User`        int(11)      NOT NULL,
    `Date`           date         NOT NULL,
    `Description`    varchar(100) NOT NULL,
    PRIMARY KEY (`ID_Acquisition`),
    UNIQUE KEY `user_date_description_unique` (`ID_User`, `Date`, `Description`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `auteurs_pseudos`
(
    `ID`              int(11)                          NOT NULL AUTO_INCREMENT,
    `NomAuteurAbrege` varchar(79) CHARACTER SET latin1 NOT NULL,
    `ID_user`         int(11)                          NOT NULL,
    `Notation`        int(4)                           NOT NULL DEFAULT -1,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `auteurs_pseudos_uindex` (`ID_user`, `NomAuteurAbrege`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
  ROW_FORMAT = DYNAMIC;

CREATE TABLE `bibliotheque_contributeurs`
(
    `ID`    int(11) NOT NULL AUTO_INCREMENT,
    `Nom`   varchar(30) COLLATE latin1_german2_ci DEFAULT NULL,
    `Texte` text COLLATE latin1_german2_ci        DEFAULT NULL,
    PRIMARY KEY (`ID`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `bibliotheque_ordre_magazines`
(
    `ID`              int(11)                               NOT NULL AUTO_INCREMENT,
    `ID_Utilisateur`  int(11)                               NOT NULL,
    `publicationcode` varchar(12) COLLATE latin1_german2_ci NOT NULL,
    `Ordre`           int(3)                                NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `bibliotheque_ordre_magazines_uindex` (`ID_Utilisateur`, `publicationcode`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `bouquineries`
(
    `ID`              int(11)                          NOT NULL AUTO_INCREMENT,
    `Nom`             varchar(25) CHARACTER SET latin1 NOT NULL,
    `Adresse`         text CHARACTER SET latin1                 DEFAULT NULL,
    `AdresseComplete` text                             NOT NULL,
    `CodePostal`      int(11)                                   DEFAULT NULL,
    `Ville`           varchar(20) CHARACTER SET latin1          DEFAULT NULL,
    `Pays`            varchar(20) CHARACTER SET latin1          DEFAULT 'France',
    `Commentaire`     text CHARACTER SET latin1        NOT NULL,
    `ID_Utilisateur`  int(11)                                   DEFAULT NULL,
    `CoordX`          double                           NOT NULL,
    `CoordY`          double                           NOT NULL,
    `DateAjout`       timestamp                        NOT NULL DEFAULT current_timestamp(),
    `Actif`           tinyint(1)                       NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `demo`
(
    `ID`              int(11)  NOT NULL DEFAULT 1,
    `DateDernierInit` datetime NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `magazines`
(
    `PaysAbrege`     varchar(4) CHARACTER SET latin1 NOT NULL,
    `NomAbrege`      varchar(7) CHARACTER SET latin1 NOT NULL,
    `NomComplet`     varchar(70) COLLATE utf8_bin    NOT NULL,
    `RedirigeDepuis` varchar(7) COLLATE utf8_bin     NOT NULL,
    `NeParaitPlus`   tinyint(1) DEFAULT NULL,
    PRIMARY KEY (`PaysAbrege`, `NomAbrege`, `RedirigeDepuis`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin;

CREATE TABLE `numeros`
(
    `ID`             int(11)                                                             NOT NULL AUTO_INCREMENT,
    `Pays`           varchar(3) COLLATE latin1_german2_ci                                NOT NULL,
    `Magazine`       varchar(6) COLLATE latin1_german2_ci                                NOT NULL,
    `Numero`         varchar(8) CHARACTER SET utf8 COLLATE utf8_bin                      NOT NULL,
    `Numero_nospace` varchar(8) GENERATED ALWAYS AS (replace(`Numero`, ' ', '')) VIRTUAL,
    `Etat`           enum ('mauvais','moyen','bon','indefini') COLLATE latin1_german2_ci NOT NULL DEFAULT 'indefini',
    `ID_Acquisition` int(11)                                                             NOT NULL DEFAULT -1,
    `AV`             tinyint(1)                                                          NOT NULL,
    `Abonnement`     tinyint(4)                                                          NOT NULL DEFAULT 0,
    `ID_Utilisateur` int(11)                                                             NOT NULL,
    `DateAjout`      timestamp                                                           NOT NULL DEFAULT current_timestamp(),
    `issuecode`      varchar(23) GENERATED ALWAYS AS (concat(`Pays`, '/', `Magazine`, ' ', `Numero`)) VIRTUAL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `Numero_Utilisateur` (`Pays`, `Magazine`, `Numero`, `ID_Utilisateur`),
    KEY `Utilisateur` (`ID_Utilisateur`),
    KEY `Pays_Magazine_Numero` (`Pays`, `Magazine`, `Numero`),
    KEY `Pays_Magazine_Numero_DateAjout` (`DateAjout`, `Pays`, `Magazine`, `Numero`),
    KEY `Numero_nospace_Utilisateur` (`Pays`, `Magazine`, `Numero_nospace`, `ID_Utilisateur`),
    KEY `numeros_issuecode_index` (`issuecode`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `numeros_popularite`
(
    `Pays`       varchar(3) NOT NULL,
    `Magazine`   varchar(6) NOT NULL,
    `Numero`     varchar(8) NOT NULL,
    `Popularite` int(11)    NOT NULL,
    `ID`         int(11)    NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `numeros_popularite_unique` (`Pays`, `Magazine`, `Numero`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE `tranches_doublons`
(
    `ID`               int(11)                              NOT NULL AUTO_INCREMENT,
    `Pays`             varchar(3) COLLATE latin1_german2_ci NOT NULL,
    `Magazine`         varchar(6) COLLATE latin1_german2_ci NOT NULL,
    `Numero`           varchar(8) COLLATE latin1_german2_ci NOT NULL,
    `NumeroReference`  varchar(8) COLLATE latin1_german2_ci NOT NULL,
    `TrancheReference` int(11) DEFAULT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `tranches_doublons_Pays_Magazine_Numero_uindex` (`Pays`, `Magazine`, `Numero`),
    KEY `tranches_doublons_tranches_pretes_ID_fk` (`TrancheReference`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `tranches_pretes`
(
    `ID`              int(11)                               NOT NULL AUTO_INCREMENT,
    `publicationcode` varchar(12) COLLATE latin1_german2_ci NOT NULL,
    `issuenumber`     varchar(10) COLLATE latin1_german2_ci NOT NULL,
    `dateajout`       timestamp                             NOT NULL DEFAULT current_timestamp(),
    `points`          int(11)                                        DEFAULT NULL,
    `slug`            varchar(30) GENERATED ALWAYS AS (concat('edges-', replace(`publicationcode`, '/', '-'), '-',
                                                              `issuenumber`)) VIRTUAL,
    `issuecode`       varchar(23) GENERATED ALWAYS AS (concat(`publicationcode`, ' ', `issuenumber`)) VIRTUAL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `tranchespretes_unique` (`publicationcode`, `issuenumber`),
    UNIQUE KEY `tranches_pretes_issuecode_uindex` (`issuecode`),
    KEY `tranches_pretes_dateajout_index` (`dateajout`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `tranches_pretes_contributeurs`
(
    `publicationcode` varchar(15)                     NOT NULL,
    `issuenumber`     varchar(30)                     NOT NULL,
    `contributeur`    int(11)                         NOT NULL,
    `contribution`    enum ('photographe','createur') NOT NULL DEFAULT 'createur',
    PRIMARY KEY (`publicationcode`, `issuenumber`, `contributeur`, `contribution`),
    KEY `tranches_pretes_contributeurs_publicationcode_issuenumber_index` (`publicationcode`, `issuenumber`),
    KEY `tranches_pretes_contributeurs_contributeur_index` (`contributeur`)
) ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE `tranches_pretes_contributions`
(
    `ID`           int(11)                         NOT NULL AUTO_INCREMENT,
    `ID_tranche`   int(11)                         NOT NULL,
    `ID_user`      int(11)                         NOT NULL,
    `dateajout`    timestamp                       NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `contribution` enum ('photographe','createur') NOT NULL,
    `points_new`   int(11)                         NOT NULL,
    `points_total` int(11)                         NOT NULL,
    PRIMARY KEY (`ID`),
    KEY `tranches_pretes_contributions_ID_user_contribution_index` (`ID_user`, `contribution`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `tranches_pretes_sprites`
(
    `ID`          int(11)     NOT NULL AUTO_INCREMENT,
    `ID_Tranche`  int(11)     NOT NULL,
    `Sprite_name` varchar(25) NOT NULL,
    `Sprite_size` int(11) DEFAULT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `tranches_pretes_sprites_unique` (`ID_Tranche`, `Sprite_name`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1;

CREATE TABLE `tranches_pretes_sprites_size`
(
    `sprite_name` varchar(25) DEFAULT NULL,
    `size`        int(11)     DEFAULT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `tranches_pretes_sprites_urls`
(
    `ID`          int(11)     NOT NULL AUTO_INCREMENT,
    `Sprite_name` varchar(25) NOT NULL,
    `Version`     varchar(12) NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `tranches_pretes_sprites_urls_unique` (`Sprite_name`, `Version`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `users`
(
    `ID`                         int(11)                                         NOT NULL AUTO_INCREMENT,
    `username`                   varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    `password`                   varchar(40) CHARACTER SET latin1                NOT NULL,
    `AccepterPartage`            tinyint(1)                                      NOT NULL DEFAULT 1,
    `DateInscription`            date                                            NOT NULL,
    `EMail`                      varchar(50) CHARACTER SET latin1                NOT NULL,
    `RecommandationsListeMags`   tinyint(1)                                      NOT NULL DEFAULT 1,
    `BetaUser`                   tinyint(1)                                      NOT NULL DEFAULT 0,
    `AfficherVideo`              tinyint(1)                                      NOT NULL DEFAULT 1,
    `Bibliotheque_Texture1`      varchar(20) CHARACTER SET latin1                NOT NULL DEFAULT 'bois',
    `Bibliotheque_Sous_Texture1` varchar(50) CHARACTER SET latin1                NOT NULL DEFAULT 'HONDURAS MAHOGANY',
    `Bibliotheque_Texture2`      varchar(20) CHARACTER SET latin1                NOT NULL DEFAULT 'bois',
    `Bibliotheque_Sous_Texture2` varchar(50) CHARACTER SET latin1                NOT NULL DEFAULT 'KNOTTY PINE',
    `DernierAcces`               datetime                                                 DEFAULT NULL,
    `PrecedentAcces`             datetime                                                 DEFAULT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `username` (`username`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `users_contributions`
(
    `ID`           int(11)                                                       NOT NULL AUTO_INCREMENT,
    `ID_user`      int(11)                                                       NOT NULL,
    `date`         datetime                                                      NOT NULL DEFAULT current_timestamp(),
    `contribution` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `points_new`   int(11)                                                       NOT NULL,
    `points_total` int(11)                                                       NOT NULL,
    `emails_sent`  tinyint(1)                                                    NOT NULL,
    `ID_tranche`   int(11)                                                                DEFAULT NULL,
    `ID_bookstore` int(11)                                                                DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `IDX_7FDC16F375567043` (`ID_tranche`),
    KEY `IDX_7FDC16F3A5778B6C` (`ID_bookstore`),
    KEY `users_contributions__user_contribution` (`ID_user`, `contribution`),
    CONSTRAINT `FK_7FDC16F375567043` FOREIGN KEY (`ID_tranche`) REFERENCES `tranches_pretes` (`ID`),
    CONSTRAINT `FK_7FDC16F3A5778B6C` FOREIGN KEY (`ID_bookstore`) REFERENCES `bouquineries` (`ID`),
    CONSTRAINT `users_contributions___fk_user` FOREIGN KEY (`ID_user`) REFERENCES `users` (`ID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `users_options`
(
    `ID`            int(11)                                  NOT NULL AUTO_INCREMENT,
    `ID_User`       int(11)                                  NOT NULL,
    `Option_nom`    enum ('suggestion_notification_country') NOT NULL,
    `Option_valeur` varchar(50)                              NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `users_options__unique` (`ID_User`, `Option_nom`, `Option_valeur`),
    KEY `users_options__user_option` (`ID_User`, `Option_nom`),
    CONSTRAINT `users_options_users_ID_fk` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `users_password_tokens`
(
    `ID`      int(11)                             NOT NULL AUTO_INCREMENT,
    `ID_User` int(11)                             NOT NULL,
    `Token`   varchar(16) COLLATE utf8_unicode_ci NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `users_password_tokens_unique` (`ID_User`, `Token`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE `users_permissions`
(
    `ID`        int(11)                                                        NOT NULL AUTO_INCREMENT,
    `username`  varchar(25) COLLATE latin1_german2_ci                          NOT NULL,
    `role`      varchar(20) COLLATE latin1_german2_ci                          NOT NULL,
    `privilege` enum ('Admin','Edition','Affichage') COLLATE latin1_german2_ci NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `permission_username_role` (`username`, `role`)
) ENGINE = MyISAM
  DEFAULT CHARSET = latin1
  COLLATE = latin1_german2_ci;

CREATE TABLE `users_points`
(
    `ID`               int(11)                                      NOT NULL AUTO_INCREMENT,
    `ID_Utilisateur`   int(11)                                      NOT NULL,
    `TypeContribution` enum ('photographe','createur','duckhunter') NOT NULL,
    `NbPoints`         int(11) DEFAULT 0,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `users_suggestions_notifications`
(
    `ID`        int(11)     NOT NULL AUTO_INCREMENT,
    `ID_User`   int(10)     NOT NULL,
    `issuecode` varchar(12) NOT NULL,
    `text`      text     DEFAULT NULL,
    `date`      datetime DEFAULT current_timestamp(),
    PRIMARY KEY (`ID`),
    UNIQUE KEY `users_notifications__index_user_issue` (`ID_User`, `issuecode`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

