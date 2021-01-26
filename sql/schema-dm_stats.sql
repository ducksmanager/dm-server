CREATE TABLE `auteurs_histoires`
(
    `ID`         int(11)     NOT NULL AUTO_INCREMENT,
    `personcode` varchar(22) NOT NULL,
    `storycode`  varchar(19) NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `unique_index` (`personcode`, `storycode`),
    KEY `index_storycode` (`storycode`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 32768
  DEFAULT CHARSET = latin1;

CREATE TABLE `auteurs_pseudos`
(
    `ID_User`         int(11)     NOT NULL,
    `NomAuteurAbrege` varchar(79) NOT NULL,
    `Notation`        tinyint(4) DEFAULT NULL,
    PRIMARY KEY (`ID_User`, `NomAuteurAbrege`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `histoires_publications`
(
    `ID`              int(11)     NOT NULL AUTO_INCREMENT,
    `storycode`       varchar(19) NOT NULL,
    `publicationcode` varchar(12) NOT NULL,
    `issuenumber`     varchar(12) NOT NULL,
    `oldestdate`      date DEFAULT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `unique_index` (`publicationcode`, `issuenumber`, `storycode`),
    KEY `index_issue` (`publicationcode`, `issuenumber`) USING HASH,
    KEY `index_story` (`storycode`) USING HASH,
    KEY `index_oldestdate` (`oldestdate`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `numeros_simple`
(
    `ID_Utilisateur`  int(11)     NOT NULL,
    `Publicationcode` varchar(12) NOT NULL,
    `Numero`          varchar(12) NOT NULL,
    PRIMARY KEY (`ID_Utilisateur`, `Publicationcode`, `Numero`),
    KEY `ID_Utilisateur` (`ID_Utilisateur`),
    KEY `user_issue` (`Publicationcode`, `Numero`),
    CONSTRAINT `numeros_simple_auteurs_pseudos_ID_User_fk` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `auteurs_pseudos` (`ID_User`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `utilisateurs_histoires_manquantes`
(
    `ID`         int(11)     NOT NULL AUTO_INCREMENT,
    `ID_User`    int(11)     NOT NULL,
    `personcode` varchar(22) NOT NULL,
    `storycode`  varchar(19) NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `missing_issue_for_user` (`ID_User`, `personcode`, `storycode`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `utilisateurs_publications_manquantes`
(
    `ID`              int(11)             NOT NULL AUTO_INCREMENT,
    `ID_User`         int(11)             NOT NULL,
    `personcode`      varchar(22)         NOT NULL,
    `storycode`       varchar(19)         NOT NULL,
    `publicationcode` varchar(12)         NOT NULL,
    `issuenumber`     varchar(12)         NOT NULL,
    `oldestdate`      date DEFAULT NULL,
    `Notation`        tinyint(3) unsigned NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `unique_index` (`ID_User`, `personcode`, `storycode`, `publicationcode`, `issuenumber`),
    KEY `missing_user_issue` (`ID_User`, `publicationcode`, `issuenumber`),
    KEY `user_stories` (`ID_User`, `personcode`, `storycode`),
    KEY `suggested` (`ID_User`, `publicationcode`, `issuenumber`, `oldestdate`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

CREATE TABLE `utilisateurs_publications_suggerees`
(
    `ID`              int(11)     NOT NULL AUTO_INCREMENT,
    `ID_User`         int(11)     NOT NULL,
    `publicationcode` varchar(12) NOT NULL,
    `issuenumber`     varchar(12) NOT NULL,
    `oldestdate`      date DEFAULT NULL,
    `Score`           int(11)     NOT NULL,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `suggested_issue_for_user` (`ID_User`, `publicationcode`, `issuenumber`),
    KEY `suggested_issue_user` (`ID_User`),
    KEY `suggested_issue_oldestdate` (`oldestdate`),
    CONSTRAINT `utilisateurs_publications_suggerees_pseudos_fk` FOREIGN KEY (`ID_User`) REFERENCES `auteurs_pseudos` (`ID_User`)
) ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

