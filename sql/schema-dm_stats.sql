create table auteurs_histoires
(
	personcode varchar(22) not null,
	storycode varchar(19) not null,
	primary key (personcode, storycode)
);

create index index_storycode
	on auteurs_histoires (storycode);

create table auteurs_pseudos
(
	ID_User int not null,
	NomAuteurAbrege varchar(79) not null,
	Notation tinyint null,
	primary key (ID_User, NomAuteurAbrege)
);

create table histoires_publications
(
	storycode varchar(19) not null,
	publicationcode varchar(12) not null,
	issuenumber varchar(12) not null,
	primary key (publicationcode, issuenumber, storycode)
);

create index index_issue
	on histoires_publications (publicationcode, issuenumber);

create index index_story
	on histoires_publications (storycode);

create table numeros_simple
(
	ID_Utilisateur int not null,
	Publicationcode varchar(12) not null,
	Numero varchar(12) not null,
	primary key (ID_Utilisateur, Publicationcode, Numero),
	constraint numeros_simple_auteurs_pseudos_ID_User_fk
		foreign key (ID_Utilisateur) references auteurs_pseudos (ID_User)
);

create index ID_Utilisateur
	on numeros_simple (ID_Utilisateur);

create index issue
	on numeros_simple (Publicationcode, Numero);

create table utilisateurs_histoires_manquantes
(
	ID_User int not null,
	personcode varchar(22) not null,
	storycode varchar(19) not null,
	primary key (ID_User, personcode, storycode)
);

create table utilisateurs_publications_manquantes
(
	ID_User int not null,
	personcode varchar(22) not null,
	storycode varchar(19) not null,
	publicationcode varchar(12) not null,
	issuenumber varchar(12) not null,
	Notation tinyint unsigned not null,
	primary key (ID_User, personcode, storycode, publicationcode, issuenumber)
);

create index issue
	on utilisateurs_publications_manquantes (ID_User, publicationcode, issuenumber);

create index user_stories
	on utilisateurs_publications_manquantes (ID_User, personcode, storycode);

create table utilisateurs_publications_suggerees
(
	ID_User int not null,
	publicationcode varchar(12) not null,
	issuenumber varchar(12) not null,
	Score int not null,
	primary key (ID_User, publicationcode, issuenumber),
	constraint utilisateurs_publications_suggerees_pseudos_fk
		foreign key (ID_User) references auteurs_pseudos (ID_User)
);

create index user
	on utilisateurs_publications_suggerees (ID_User);

