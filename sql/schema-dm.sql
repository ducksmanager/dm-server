create table achats
(
  ID_Acquisition int auto_increment
    primary key,
  ID_User int not null,
  Date date not null,
  Style_couleur varchar(9) null,
  Style_soulignement enum('Aucun', 'Simple', 'Double', 'Triple', 'Pointillé', 'Zig-zag', 'Double zig-zag', 'Ondulé', 'Double ondulé') null,
  Style_entourage enum('Aucun', 'Simple', 'Double', 'Pointillé', 'Rectangulaire') null,
  Style_marquage enum('Aucun', '*', '+', '!') null,
  Description varchar(100) not null,
  constraint user_date_description_unique
    unique (ID_User, Date, Description)
)
  engine=MyISAM;

create table auteurs
(
  ID_auteur int auto_increment
    primary key,
  NomAuteur varchar(20) not null,
  NbHistoires int not null,
  NbHistoires_old int not null,
  DateMAJ date not null
)
  engine=MyISAM;

create table auteurs_pseudos
(
  NomAuteur varchar(50) charset utf8 not null,
  NomAuteurAbrege varchar(30) charset latin1 default '' not null,
  ID_user int not null,
  NbNonPossedesFrance int default 0 not null,
  NbNonPossedesEtranger int default 0 not null,
  NbPossedes int not null,
  DateStat date default '0000-00-00' not null,
  Notation tinyint default -1 not null,
  primary key (NomAuteurAbrege, ID_user, DateStat)
)
  engine=MyISAM;

create table auteurs_pseudos_simple
(
  ID_User int not null,
  NomAuteurAbrege varchar(79) collate utf8_unicode_ci not null,
  Notation tinyint(1) null,
  primary key (ID_User, NomAuteurAbrege)
)
  engine=MyISAM;

create index index_auteur_inducks
  on auteurs_pseudos_simple (NomAuteurAbrege);

create table bibliotheque_acces_externes
(
  ID_Utilisateur int not null,
  Cle varchar(16) not null,
  primary key (ID_Utilisateur, Cle)
)
  charset=utf8;

create table bibliotheque_contributeurs
(
  ID int auto_increment
    primary key,
  Nom varchar(30) null,
  Texte text null
)
  engine=MyISAM;

create table bibliotheque_ordre_magazines
(
  Pays varchar(3) default '' not null,
  Magazine varchar(6) default '' not null,
  Ordre int(3) default 0 not null,
  ID_Utilisateur int default 0 not null,
  primary key (Pays, Magazine, Ordre, ID_Utilisateur)
)
  engine=MyISAM;

create table bouquineries
(
  ID int auto_increment
    primary key,
  Nom varchar(25) charset latin1 not null,
  Adresse text charset latin1 null,
  AdresseComplete text not null,
  CodePostal int null,
  Ville varchar(20) charset latin1 null,
  Pays varchar(20) charset latin1 default 'France' null,
  Commentaire text charset latin1 not null,
  ID_Utilisateur int null,
  CoordX float default 0 not null,
  CoordY float default 0 not null,
  DateAjout timestamp default current_timestamp() not null,
  Actif tinyint(1) default 1 not null
)
  engine=MyISAM charset=utf8;

create table demo
(
  DateDernierInit datetime not null
    primary key
)
  engine=MyISAM;

create table emails_ventes
(
  ID int auto_increment
    primary key,
  username_achat varchar(50) not null,
  username_vente varchar(50) not null,
  date datetime not null,
  constraint emails_ventes__username_achat_username_vente_date_uindex
    unique (username_achat, username_vente, date)
)
  engine=MyISAM;

create table images_myfonts
(
  ID int auto_increment,
  Font varchar(150) collate latin1_german2_ci null,
  Color varchar(10) collate latin1_german2_ci null,
  ColorBG varchar(10) collate latin1_german2_ci null,
  Width varchar(7) collate latin1_german2_ci null,
  Texte varchar(150) collate utf8_bin null,
  Precision_ varchar(5) collate latin1_german2_ci null,
  constraint ID
    unique (ID)
)
  engine=MyISAM charset=utf8;

alter table images_myfonts
  add primary key (ID);

create table magazines
(
  PaysAbrege varchar(4) charset latin1 not null,
  NomAbrege varchar(7) charset latin1 not null,
  NomComplet varchar(70) not null,
  RedirigeDepuis varchar(7) default '' not null,
  NeParaitPlus tinyint(1) null,
  primary key (PaysAbrege, NomAbrege, RedirigeDepuis)
)
  engine=MyISAM;

create table numeros
(
  Pays varchar(3) not null,
  Magazine varchar(6) not null,
  Numero varchar(8) collate utf8_bin not null,
  Etat enum('mauvais', 'moyen', 'bon', 'indefini') not null,
  ID_Acquisition int default -1 not null,
  AV tinyint(1) not null,
  ID_Utilisateur int not null,
  DateAjout timestamp default current_timestamp() not null,
  ID int auto_increment
    primary key,
  constraint Pays
    unique (Pays, Magazine, Numero, ID_Utilisateur)
)
  engine=MyISAM;

create index Pays_Magazine_Numero
  on numeros (Pays, Magazine, Numero);

create index Pays_Magazine_Numero_DateAjout
  on numeros (DateAjout, Pays, Magazine, Numero);

create index Utilisateur
  on numeros (ID_Utilisateur);

create table numeros_popularite
(
  Pays varchar(3) not null,
  Magazine varchar(6) not null,
  Numero varchar(8) not null,
  Popularite int not null,
  primary key (Pays, Magazine, Numero)
)
  engine=MyISAM charset=utf8;

create table numeros_simple
(
  ID_Utilisateur int not null,
  Publicationcode varchar(12) collate utf8_unicode_ci not null,
  Numero varchar(12) collate utf8_unicode_ci not null,
  primary key (ID_Utilisateur, Publicationcode, Numero)
)
  engine=MyISAM;

create index ID_Utilisateur
  on numeros_simple (ID_Utilisateur);

create index Numero
  on numeros_simple (Numero);

create index Publicationcode
  on numeros_simple (Publicationcode);

create table tranches_doublons
(
  Pays varchar(3) not null,
  Magazine varchar(6) not null,
  Numero varchar(8) not null,
  NumeroReference varchar(8) not null,
  TrancheReference int null,
  primary key (Pays, Magazine, Numero)
)
  engine=MyISAM;

create table tranches_pretes
(
  ID int auto_increment
    primary key,
  publicationcode varchar(12) default '' not null,
  issuenumber varchar(10) default '' not null,
  dateajout timestamp default current_timestamp() not null,
  points int null,
  constraint tranchespretes_unique
    unique (publicationcode, issuenumber)
)
  engine=MyISAM;

create index tranches_pretes_dateajout_index
  on tranches_pretes (dateajout);

create index tranches_pretes_publicationcode_issuenumber_index
  on tranches_pretes (publicationcode, issuenumber);

create table tranches_pretes_contributeurs
(
  publicationcode varchar(15) not null,
  issuenumber varchar(30) not null,
  contributeur int not null,
  contribution enum('photographe', 'createur') default 'createur' not null,
  primary key (publicationcode, issuenumber, contributeur, contribution)
)
  engine=MyISAM charset=utf8;

create index tranches_pretes_contributeurs_contributeur_index
  on tranches_pretes_contributeurs (contributeur);

create index tranches_pretes_contributeurs_publicationcode_issuenumber_index
  on tranches_pretes_contributeurs (publicationcode, issuenumber);

create table users
(
  ID int auto_increment
    primary key,
  username varchar(25) collate utf8_bin not null,
  password varchar(40) charset latin1 not null,
  AccepterPartage tinyint(1) default 1 not null,
  DateInscription date default '0000-00-00' not null,
  EMail varchar(50) charset latin1 not null,
  RecommandationsListeMags tinyint(1) default 1 not null,
  BetaUser tinyint unsigned default 0 not null,
  AfficherVideo tinyint(1) default 1 not null,
  Bibliotheque_Texture1 varchar(20) charset latin1 default 'bois' not null,
  Bibliotheque_Sous_Texture1 varchar(50) charset latin1 default 'HONDURAS MAHOGANY' not null,
  Bibliotheque_Texture2 varchar(20) charset latin1 default 'bois' not null,
  Bibliotheque_Sous_Texture2 varchar(50) charset latin1 default 'KNOTTY PINE' not null,
  Bibliotheque_Grossissement double unsigned default 1.5 not null,
  DernierAcces timestamp default current_timestamp() not null on update current_timestamp(),
  constraint username
    unique (username)
)
  engine=MyISAM;

create table users_permissions
(
  ID int auto_increment
    primary key,
  username varchar(25) not null,
  role varchar(20) not null,
  privilege enum('Admin', 'Edition', 'Affichage') not null,
  constraint permission_username_role
    unique (username, role)
)
  engine=MyISAM;

create table users_points
(
  ID_Utilisateur int not null,
  TypeContribution enum('photographe', 'createur', 'duckhunter') not null,
  NbPoints int default 0 null,
  primary key (ID_Utilisateur, TypeContribution)
);


create procedure reset_issue_popularities()
BEGIN
  -- Cleanup: prevents problems with issues having the same issuenumber but with a different case
  UPDATE numeros n
    INNER JOIN (
      SELECT DISTINCT
        n_inner.Pays,
        n_inner.Magazine,
        n_inner.Numero
      FROM numeros n_inner, numeros n2_inner
      WHERE n_inner.NUMERO NOT REGEXP '^[0-9]+$' AND n2_inner.NUMERO NOT REGEXP '^[0-9]+$' AND
          LOWER(n_inner.Numero) = LOWER(n2_inner.Numero) AND n_inner.Numero != n2_inner.Numero
    ) n2
  SET n.Numero = LOWER(n.Numero)
  WHERE n.Pays = n2.Pays AND n.Magazine = n2.Magazine AND n.Numero = n2.Numero;

  -- Set issues' popularity. This number will vary over time
  TRUNCATE numeros_popularite;
  INSERT INTO numeros_popularite(Pays,Magazine,Numero,Popularite)
  SELECT DISTINCT
    n.Pays,
    n.Magazine,
    REPLACE(n.Numero, ' ', ''),
    COUNT(*) AS Popularite
  FROM numeros n
  WHERE
      n.ID_Utilisateur NOT IN (
      SELECT u.ID
      FROM users u
      WHERE u.username LIKE 'test%'
    ) AND
      n.DateAjout < DATE_SUB(NOW(), INTERVAL -1 MONTH)
  GROUP BY n.Pays, n.Magazine, REPLACE(n.Numero, ' ', '');

  -- Associate issues' popularity with edges. This will not vary over time: we only modify the edges that don't have their popularity set
  UPDATE tranches_pretes tp
  SET points = (
    SELECT Popularite
    FROM numeros_popularite np
    WHERE
        np.Pays = SUBSTRING(tp.publicationcode, 1, POSITION('/' IN tp.publicationcode) - 1) AND
        np.Magazine = SUBSTRING(tp.publicationcode, POSITION('/' IN tp.publicationcode) + 1) AND
        np.Numero = tp.issuenumber
  )
  WHERE points IS NULL;

  -- Update the users' points
  TRUNCATE users_points;
  INSERT INTO users_points(ID_Utilisateur, TypeContribution, NbPoints)
  SELECT
    contributions.contributeur,
    contributions.type_contribution,
    sum(contributions.Popularite) AS points
  FROM (
    SELECT
     tp.*,
     tpc.contributeur,
     tpc.contribution AS type_contribution,
     (
       SELECT np.Popularite
       FROM numeros_popularite np
       WHERE
         np.Pays = SUBSTRING_INDEX(tp.publicationcode, '/', 1) AND
         np.Magazine = SUBSTRING_INDEX(tp.publicationcode, '/', -1) AND
         np.Numero = tp.issuenumber
     ) AS Popularite
    FROM tranches_pretes tp
    INNER JOIN tranches_pretes_contributeurs tpc USING (publicationcode, issuenumber)
  ) contributions
  INNER JOIN users ON contributions.contributeur = users.ID
  GROUP BY contributions.contributeur, contributions.type_contribution
  HAVING sum(contributions.Popularite) > 0
  ORDER BY sum(contributions.Popularite);
END;

