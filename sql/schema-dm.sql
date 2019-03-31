create schema dm collate latin1_swedish_ci;

create table achats
(
  ID_Acquisition int auto_increment
    primary key,
  ID_User int not null,
  Date date not null,
  Description varchar(100) not null,
  constraint user_date_description_unique
    unique (ID_User, Date, Description)
)
  engine=MyISAM;

create table auteurs_pseudos
(
  NomAuteurAbrege varchar(30) charset latin1 not null,
  ID_user int not null,
  Notation int(4) default -1 not null,
  primary key (NomAuteurAbrege, ID_user)
)
  engine=MyISAM collate=utf8_bin;

create table bibliotheque_contributeurs
(
  ID int auto_increment
    primary key,
  Nom varchar(30) null,
  Texte text null
)
  engine=MyISAM collate=latin1_german2_ci;

create table bibliotheque_ordre_magazines
(
  ID int auto_increment
    primary key,
  ID_Utilisateur int not null,
  publicationcode varchar(12) not null,
  Ordre int(3) not null,
  constraint bibliotheque_ordre_magazines_uindex
    unique (ID_Utilisateur, publicationcode)
)
  engine=MyISAM collate=latin1_german2_ci;

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
  CoordX double not null,
  CoordY double not null,
  DateAjout timestamp default current_timestamp() not null,
  Actif tinyint(1) default 0 not null
)
  engine=MyISAM charset=utf8;

create table demo
(
  DateDernierInit datetime not null
    primary key
)
  engine=MyISAM collate=latin1_german2_ci;

create table magazines
(
  PaysAbrege varchar(4) charset latin1 not null,
  NomAbrege varchar(7) charset latin1 not null,
  NomComplet varchar(70) not null,
  RedirigeDepuis varchar(7) not null,
  NeParaitPlus tinyint(1) null,
  primary key (PaysAbrege, NomAbrege, RedirigeDepuis)
)
  engine=MyISAM collate=utf8_bin;

create table numeros
(
  ID int auto_increment
    primary key,
  Pays varchar(3) not null,
  Magazine varchar(6) not null,
  Numero varchar(8) collate utf8_bin not null,
  Numero_nospace varchar(8) as (replace(`Numero`,' ','')),
  Etat enum('mauvais', 'moyen', 'bon', 'indefini') default 'indefini' not null,
  ID_Acquisition int default -1 not null,
  AV tinyint(1) not null,
  ID_Utilisateur int not null,
  DateAjout timestamp default current_timestamp() not null,
  constraint Numero_Utilisateur
    unique (Pays, Magazine, Numero, ID_Utilisateur)
)
  engine=MyISAM collate=latin1_german2_ci;

create index Numero_nospace_Utilisateur
  on numeros (Pays, Magazine, Numero_nospace, ID_Utilisateur);

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

create table tranches_doublons
(
  ID int auto_increment
    primary key,
  Pays varchar(3) not null,
  Magazine varchar(6) not null,
  Numero varchar(8) not null,
  NumeroReference varchar(8) not null,
  TrancheReference int null,
  constraint tranches_doublons_Pays_Magazine_Numero_uindex
    unique (Pays, Magazine, Numero)
)
  engine=MyISAM collate=latin1_german2_ci;

create index tranches_doublons_tranches_pretes_ID_fk
  on tranches_doublons (TrancheReference);

create table tranches_pretes
(
  ID int auto_increment
    primary key,
  publicationcode varchar(12) not null,
  issuenumber varchar(10) not null,
  dateajout timestamp default current_timestamp() not null,
  points int null,
  constraint tranchespretes_unique
    unique (publicationcode, issuenumber)
)
  engine=MyISAM collate=latin1_german2_ci;

create index tranches_pretes_dateajout_index
  on tranches_pretes (dateajout);

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
  BetaUser tinyint(1) default 0 not null,
  AfficherVideo tinyint(1) default 1 not null,
  Bibliotheque_Texture1 varchar(20) charset latin1 default 'bois' not null,
  Bibliotheque_Sous_Texture1 varchar(50) charset latin1 default 'HONDURAS MAHOGANY' not null,
  Bibliotheque_Texture2 varchar(20) charset latin1 default 'bois' not null,
  Bibliotheque_Sous_Texture2 varchar(50) charset latin1 default 'KNOTTY PINE' not null,
  DernierAcces timestamp default current_timestamp() not null on update current_timestamp(),
  constraint username
    unique (username)
)
  engine=MyISAM collate=latin1_german2_ci;

create table users_password_tokens
(
  ID int auto_increment
    primary key,
  ID_User int not null,
  Token varchar(16) not null,
  constraint users_password_tokens_unique
    unique (ID_User, Token)
)
  collate=utf8_unicode_ci;

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
  engine=MyISAM collate=latin1_german2_ci;

create table users_points
(
  ID int auto_increment
    primary key,
  ID_Utilisateur int not null,
  TypeContribution enum('photographe', 'createur', 'duckhunter') not null,
  NbPoints int default 0 null
);

create definer = root@`%` procedure reset_issue_popularities()
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


create function get_sprite_range(issuenumber varchar(10), rangewidth int(3)) returns varchar(30)
RETURN concat(issuenumber - mod(issuenumber - 1, rangewidth), '-',
              issuenumber - mod(issuenumber - 1, rangewidth) + rangewidth - 1);

create function get_sprite_name(publicationcode varchar(12), suffix varchar(30)) returns varchar(48)
RETURN concat('edges-', REPLACE(publicationcode, '/', '-'), '-', suffix);

create procedure generate_sprite_names()
BEGIN
  TRUNCATE tranches_pretes_sprites;

  INSERT INTO tranches_pretes_sprites(ID_Tranche, Sprite_name, Sprite_size)
  SELECT tp.ID,
         get_sprite_name(tp.publicationcode, 'full'),
         (SELECT COUNT(*) FROM tranches_pretes tp2 where tp2.publicationcode = tp.publicationcode)
  FROM tranches_pretes tp;

  INSERT INTO tranches_pretes_sprites(ID_Tranche, Sprite_name, Sprite_size)
  SELECT tp.ID, get_sprite_name(tp.publicationcode, get_sprite_range(tp.issuenumber, 10)), 10
  from tranches_pretes tp
  WHERE tp.issuenumber regexp '^[0-9]+$';

  INSERT INTO tranches_pretes_sprites(ID_Tranche, Sprite_name, Sprite_size)
  SELECT tp.ID, get_sprite_name(tp.publicationcode, get_sprite_range(tp.issuenumber, 20)), 20
  from tranches_pretes tp
  WHERE tp.issuenumber regexp '^[0-9]+$';

  INSERT INTO tranches_pretes_sprites(ID_Tranche, Sprite_name, Sprite_size)
  SELECT tp.ID, get_sprite_name(tp.publicationcode, get_sprite_range(tp.issuenumber, 50)), 50
  from tranches_pretes tp
  WHERE tp.issuenumber regexp '^[0-9]+$';

  INSERT INTO tranches_pretes_sprites(ID_Tranche, Sprite_name, Sprite_size)
  SELECT tp.ID, get_sprite_name(tp.publicationcode, get_sprite_range(tp.issuenumber, 100)), 100
  from tranches_pretes tp
  WHERE tp.issuenumber regexp '^[0-9]+$';

END;

