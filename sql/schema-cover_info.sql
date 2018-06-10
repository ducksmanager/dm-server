create table cover_imports
(
	coverid int auto_increment
		primary key,
	imported datetime null,
	import_error varchar(200) null,
	constraint uniquefieldset_cover_imports
		unique (coverid, imported, import_error)
)
engine=MyISAM charset=utf8
;

create table covers
(
	ID int auto_increment
		primary key,
	issuecode varchar(17) not null,
	sitecode varchar(11) not null,
	url varchar(98) not null,
	constraint uniquefieldset_covers
		unique (issuecode, url)
)
engine=MyISAM charset=utf8
;

