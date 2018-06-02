create table inducks_appearance
(
	storyversioncode varchar(19) null,
	charactercode varchar(62) null,
	number int(7) null,
	appearancecomment varchar(209) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_appearance (charactercode)
;

create index fk1
	on inducks_appearance (appearancecomment)
;

create index pk0
	on inducks_appearance (storyversioncode, charactercode)
;

create table inducks_character
(
	charactercode varchar(69) null,
	charactername text null,
	official enum('Y', 'N') null,
	onetime enum('Y', 'N') null,
	heroonly enum('Y', 'N') null,
	charactercomment varchar(671) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fulltext_inducks_character
	on inducks_character (charactername(255))
;

create index pk0
	on inducks_character (charactercode)
;

create table inducks_characteralias
(
	charactercode varchar(31) null,
	charactername varchar(58) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_characteralias (charactercode)
;

create index pk0
	on inducks_characteralias (charactername)
;

create table inducks_characterdetail
(
	charactername varchar(7) null,
	charactercode varchar(6) null,
	number int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_characterdetail (charactername)
;

create index pk0
	on inducks_characterdetail (charactercode)
;

create table inducks_charactername
(
	charactercode varchar(38) null,
	languagecode varchar(7) null,
	charactername varchar(83) null,
	preferred enum('Y', 'N') null,
	characternamecomment varchar(99) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_charactername (languagecode)
;

create index pk0
	on inducks_charactername (charactercode, languagecode, charactername)
;

create table inducks_characterreference
(
	fromcharactercode varchar(21) null,
	tocharactercode varchar(20) null,
	isgroupofcharacters enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_characterreference (tocharactercode)
;

create index pk0
	on inducks_characterreference (fromcharactercode, tocharactercode)
;

create table inducks_characterurl
(
	charactercode varchar(1) null,
	sitecode varchar(1) null,
	url varchar(1) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_characterurl (sitecode)
;

create index pk0
	on inducks_characterurl (charactercode, sitecode)
;

create table inducks_country
(
	countrycode varchar(2) null,
	countryname varchar(20) null,
	defaultlanguage varchar(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_country (countrycode)
;

create table inducks_countryname
(
	countrycode varchar(2) null,
	languagecode varchar(5) null,
	countryname varchar(56) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_countryname (languagecode)
;

create index pk0
	on inducks_countryname (countrycode, languagecode)
;

create table inducks_currency
(
	currencycode varchar(3) null,
	currencyname varchar(18) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_currency (currencycode)
;

create table inducks_currencyname
(
	currencycode varchar(3) null,
	languagecode varchar(2) null,
	shortcurrencyname varchar(18) null,
	longcurrencyname varchar(20) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_currencyname (languagecode)
;

create index pk0
	on inducks_currencyname (currencycode, languagecode)
;

create table inducks_entry
(
	entrycode varchar(22) null,
	issuecode varchar(17) null,
	storyversioncode varchar(19) null,
	languagecode varchar(7) null,
	includedinentrycode varchar(19) null,
	position varchar(9) null,
	printedcode varchar(88) null,
	guessedcode varchar(39) null,
	title varchar(235) null,
	reallytitle enum('Y', 'N') null,
	printedhero varchar(96) null,
	changes varchar(628) null,
	cut varchar(104) null,
	minorchanges varchar(558) null,
	missingpanels varchar(2) null,
	mirrored enum('Y', 'N') null,
	sideways enum('Y', 'N') null,
	startdate varchar(10) null,
	enddate varchar(10) null,
	identificationuncertain enum('Y', 'N') null,
	alsoreprint varchar(111) null,
	part varchar(5) null,
	entrycomment varchar(1715) null,
	error enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index entryTitleFullText
	on inducks_entry (title)
;

create index fk0
	on inducks_entry (issuecode)
;

create index fk1
	on inducks_entry (storyversioncode)
;

create index fk2
	on inducks_entry (languagecode)
;

create index fk3
	on inducks_entry (includedinentrycode)
;

create index fk4
	on inducks_entry (position)
;

create index pk0
	on inducks_entry (entrycode)
;

create table inducks_entry_nofulltext
(
	entrycode varchar(22) null,
	issuecode varchar(17) null,
	storyversioncode varchar(19) null,
	languagecode varchar(7) null,
	includedinentrycode varchar(19) null,
	position varchar(7) null,
	printedcode varchar(88) null,
	guessedcode varchar(39) null,
	title varchar(235) null,
	reallytitle enum('Y', 'N') null,
	printedhero varchar(96) null,
	changes varchar(628) null,
	cut varchar(100) null,
	minorchanges varchar(558) null,
	missingpanels varchar(23) null,
	mirrored enum('Y', 'N') null,
	sideways enum('Y', 'N') null,
	startdate varchar(10) null,
	enddate varchar(10) null,
	identificationuncertain enum('Y', 'N') null,
	alsoreprint varchar(66) null,
	part varchar(5) null,
	entrycomment varchar(3476) null,
	error enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_entry_nofulltext (issuecode)
;

create index fk1
	on inducks_entry_nofulltext (storyversioncode)
;

create index fk2
	on inducks_entry_nofulltext (languagecode)
;

create index fk3
	on inducks_entry_nofulltext (includedinentrycode)
;

create index fk4
	on inducks_entry_nofulltext (position)
;

create index pk0
	on inducks_entry_nofulltext (entrycode)
;

create table inducks_entrycharactername
(
	entrycode varchar(22) null,
	charactercode varchar(55) null,
	charactername varchar(88) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_entrycharactername (charactercode)
;

create index pk0
	on inducks_entrycharactername (entrycode, charactercode)
;

create table inducks_entryjob
(
	entrycode varchar(19) null,
	personcode varchar(50) null,
	transletcol varchar(1) null,
	entryjobcomment varchar(51) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_entryjob (personcode)
;

create index pk0
	on inducks_entryjob (entrycode, personcode, transletcol)
;

create table inducks_entryurl
(
	entrycode varchar(21) null,
	sitecode varchar(11) null,
	pagenumber int(7) null,
	url varchar(87) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_entryurl (entrycode)
;

create index fk1
	on inducks_entryurl (sitecode)
;

create index fk2
	on inducks_entryurl (url)
;

create table inducks_equiv
(
	issuecode varchar(15) null,
	equivid int(7) null,
	equivcomment varchar(2) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_equiv (equivid)
;

create index pk0
	on inducks_equiv (issuecode, equivid)
;

create table inducks_herocharacter
(
	storycode varchar(18) null,
	charactercode varchar(54) null,
	number int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_herocharacter (charactercode)
;

create index pk0
	on inducks_herocharacter (storycode, charactercode)
;

create table inducks_inputfile
(
	inputfilecode int(7) null,
	path varchar(11) null,
	filename varchar(22) null,
	layout varchar(10) null,
	locked enum('Y', 'N') null,
	maintenanceteamcode varchar(8) null,
	countrycode varchar(2) null,
	languagecode varchar(7) null,
	producercode varchar(15) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_inputfile (inputfilecode)
;

create table inducks_issue
(
	issuecode varchar(17) null,
	issuerangecode varchar(15) null,
	publicationcode varchar(12) null,
	issuenumber varchar(12) null,
	title varchar(158) null,
	size varchar(61) null,
	pages varchar(82) null,
	price varchar(103) null,
	printrun varchar(142) null,
	attached varchar(288) null,
	oldestdate varchar(10) null,
	fullyindexed enum('Y', 'N') null,
	issuecomment varchar(1270) null,
	error enum('Y', 'N') null,
	filledoldestdate varchar(10) null,
	locked enum('Y', 'N') null,
	inxforbidden enum('Y', 'N') null,
	inputfilecode int(7) null,
	maintenanceteamcode varchar(8) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_issue (issuerangecode)
;

create index fk1
	on inducks_issue (publicationcode)
;

create index pk0
	on inducks_issue (issuecode)
;

create table inducks_issuecollecting
(
	collectingissuecode varchar(17) null,
	collectedissuecode varchar(15) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_issuecollecting (collectedissuecode)
;

create index pk0
	on inducks_issuecollecting (collectingissuecode, collectedissuecode)
;

create table inducks_issuedate
(
	issuecode varchar(17) null,
	date varchar(10) null,
	kindofdate varchar(76) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_issuedate (issuecode, date)
;

create table inducks_issuejob
(
	issuecode varchar(17) null,
	personcode varchar(48) null,
	inxtransletcol varchar(1) null,
	issuejobcomment varchar(32) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_issuejob (personcode)
;

create index pk0
	on inducks_issuejob (issuecode, personcode, inxtransletcol)
;

create table inducks_issueprice
(
	issuecode varchar(17) null,
	amount varchar(43) null,
	currency varchar(14) null,
	comment varchar(64) null,
	sequencenumber int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_issueprice (issuecode, amount)
;

create table inducks_issuerange
(
	issuerangecode varchar(15) null,
	publicationcode varchar(9) null,
	title varchar(229) null,
	circulation varchar(25) null,
	issuerangecomment varchar(468) null,
	numbersarefake enum('Y', 'N') null,
	error enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_issuerange (publicationcode)
;

create index pk0
	on inducks_issuerange (issuerangecode)
;

create table inducks_issueurl
(
	issuecode varchar(14) null,
	sitecode varchar(12) null,
	url varchar(12) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_issueurl (sitecode)
;

create index pk0
	on inducks_issueurl (issuecode, sitecode)
;

create table inducks_language
(
	languagecode varchar(7) null,
	defaultlanguagecode varchar(5) null,
	languagename varchar(20) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_language (defaultlanguagecode)
;

create index pk0
	on inducks_language (languagecode)
;

create table inducks_languagename
(
	desclanguagecode varchar(5) null,
	languagecode varchar(7) null,
	languagename varchar(57) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_languagename (languagecode)
;

create index pk0
	on inducks_languagename (desclanguagecode, languagecode)
;

create table inducks_log
(
	number int(7) null,
	logkey varchar(41) null,
	storycode varchar(39) null,
	logid varchar(4) null,
	logtype varchar(1) null,
	par1 varchar(1847) null,
	par2 varchar(1846) null,
	par3 varchar(285) null,
	marked enum('Y', 'N') null,
	inputfilecode int(7) null,
	maintenanceteamcode varchar(8) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_log (number)
;

create table inducks_logdata
(
	logid varchar(4) null,
	category int(7) null,
	logtext varchar(108) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_logdata (logid)
;

create table inducks_logocharacter
(
	entrycode varchar(22) null,
	charactercode varchar(54) null,
	reallyintitle enum('Y', 'N') null,
	number int(7) null,
	logocharactercomment varchar(28) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_logocharacter (charactercode)
;

create index pk0
	on inducks_logocharacter (entrycode, charactercode)
;

create table inducks_movie
(
	moviecode varchar(14) null,
	title varchar(62) null,
	moviecomment varchar(570) null,
	appsummary text null,
	moviejobsummary text null,
	locked enum('Y', 'N') null,
	inputfilecode int(7) null,
	maintenanceteamcode varchar(7) null,
	aka varchar(81) null,
	creationdate varchar(10) null,
	moviedescription varchar(836) null,
	distributor varchar(50) null,
	genre varchar(3) null,
	orderer varchar(178) null,
	publicationdate varchar(10) null,
	source varchar(91) null,
	tim varchar(6) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_movie (moviecode)
;

create table inducks_moviecharacter
(
	moviecode varchar(13) null,
	charactercode varchar(36) null,
	istitlecharacter enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_moviecharacter (charactercode)
;

create index pk0
	on inducks_moviecharacter (moviecode, charactercode)
;

create table inducks_moviejob
(
	moviecode varchar(13) null,
	personcode varchar(39) null,
	role varchar(15) null,
	moviejobcomment varchar(82) null,
	indirect enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_moviejob (personcode)
;

create index pk0
	on inducks_moviejob (moviecode, personcode, role)
;

create table inducks_moviereference
(
	storycode varchar(17) null,
	moviecode varchar(14) null,
	referencereasonid int(7) null,
	frommovietostory enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_moviereference (moviecode)
;

create index fk1
	on inducks_moviereference (referencereasonid)
;

create index pk0
	on inducks_moviereference (storycode, moviecode)
;

create table inducks_person
(
	personcode varchar(79) null,
	nationalitycountrycode varchar(2) null,
	fullname text null,
	official enum('Y', 'N') null,
	personcomment varchar(221) null,
	unknownstudiomember enum('Y', 'N') null,
	isfake enum('Y', 'N') null,
	numberofindexedissues int(7) null,
	birthname text null,
	borndate varchar(10) null,
	bornplace varchar(30) null,
	deceaseddate varchar(10) null,
	deceasedplace varchar(31) null,
	education varchar(189) null,
	moviestext varchar(879) null,
	comicstext varchar(1023) null,
	othertext varchar(307) null,
	photofilename varchar(32) null,
	photocomment varchar(68) null,
	photosource varchar(67) null,
	personrefs varchar(180) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_person (nationalitycountrycode)
;


create index pk0
	on inducks_person (personcode)
;

create table inducks_personalias
(
	personcode varchar(31) null,
	surname varchar(48) null,
	givenname varchar(31) null,
	official enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_personalias (personcode)
;

create table inducks_personurl
(
	personcode varchar(24) null,
	sitecode varchar(15) null,
	url varchar(31) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_personurl (sitecode)
;

create index pk0
	on inducks_personurl (personcode, sitecode)
;

create table inducks_publication
(
	publicationcode varchar(12) null,
	countrycode varchar(2) null,
	languagecode varchar(7) null,
	title text null,
	size varchar(61) null,
	publicationcomment varchar(1417) null,
	circulation varchar(4) null,
	numbersarefake enum('Y', 'N') null,
	error enum('Y', 'N') null,
	locked enum('Y', 'N') null,
	inxforbidden enum('Y', 'N') null,
	inputfilecode int(7) null,
	maintenanceteamcode varchar(9) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_publication (countrycode)
;

create index fk1
	on inducks_publication (languagecode)
;


create index pk0
	on inducks_publication (publicationcode)
;

create table inducks_publicationcategory
(
	publicationcode varchar(12) null,
	category varchar(61) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_publicationcategory (publicationcode)
;

create table inducks_publicationname
(
	publicationcode varchar(9) null,
	publicationname varchar(62) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_publicationname (publicationcode)
;

create table inducks_publicationurl
(
	publicationcode varchar(10) null,
	sitecode varchar(16) null,
	url varchar(236) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_publicationurl (sitecode)
;

create index pk0
	on inducks_publicationurl (publicationcode, sitecode)
;

create table inducks_publisher
(
	publisherid varchar(84) null,
	publishername text null
)
engine=MyISAM collate=utf8_unicode_ci
;


create index pk0
	on inducks_publisher (publisherid)
;

create table inducks_publishingjob
(
	publisherid varchar(84) null,
	issuecode varchar(17) null,
	publishingjobcomment varchar(53) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_publishingjob (issuecode)
;

create index pk0
	on inducks_publishingjob (publisherid, issuecode)
;

create table inducks_referencereason
(
	referencereasonid int(7) null,
	referencereasontext varchar(87) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_referencereason (referencereasonid)
;

create table inducks_referencereasonname
(
	referencereasonid int(7) null,
	languagecode varchar(2) null,
	referencereasontranslation varchar(28) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_referencereasonname (languagecode)
;

create index pk0
	on inducks_referencereasonname (referencereasonid, languagecode)
;

create table inducks_site
(
	sitecode varchar(16) null,
	urlbase varchar(51) null,
	images enum('Y', 'N') null,
	sitename varchar(85) null,
	sitelogo varchar(107) null,
	properties varchar(1) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_site (sitecode)
;

create table inducks_statcharactercharacter
(
	charactercode varchar(45) null,
	cocharactercode varchar(45) null,
	total int(7) null,
	yearrange varchar(142) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statcharactercharacter (charactercode, total)
;

create table inducks_statcharactercountry
(
	charactercode varchar(45) null,
	countrycode varchar(2) null,
	total int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statcharactercountry (charactercode, countrycode)
;

create table inducks_statcharacterstory
(
	charactercode varchar(45) null,
	productionletter varchar(1) null,
	total int(7) null,
	yearrange varchar(105) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statcharacterstory (charactercode, productionletter)
;

create table inducks_statpersoncharacter
(
	personcode varchar(31) null,
	charactercode varchar(45) null,
	total int(7) null,
	yearrange varchar(106) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statpersoncharacter (personcode, total)
;

create table inducks_statpersoncountry
(
	personcode varchar(31) null,
	countrycode varchar(2) null,
	total int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statpersoncountry (personcode, countrycode)
;

create table inducks_statpersonperson
(
	personcode varchar(31) null,
	copersoncode varchar(31) null,
	total int(7) null,
	yearrange varchar(59) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statpersonperson (personcode, total)
;

create table inducks_statpersonstory
(
	personcode varchar(31) null,
	productionletter varchar(1) null,
	total int(7) null,
	yearrange varchar(62) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_statpersonstory (personcode, productionletter)
;

create table inducks_story
(
	storycode varchar(19) null,
	originalstoryversioncode varchar(19) null,
	creationdate varchar(21) null,
	firstpublicationdate varchar(10) null,
	endpublicationdate varchar(10) null,
	title text null,
	usedifferentcode varchar(20) null,
	storycomment varchar(664) null,
	error enum('Y', 'N') null,
	repcountrysummary text null,
	storyparts int(7) null,
	locked enum('Y', 'N') null,
	inputfilecode int(7) null,
	issuecodeofstoryitem varchar(14) null,
	maintenanceteamcode varchar(8) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_story (originalstoryversioncode)
;

create index fk1
	on inducks_story (firstpublicationdate)
;


create index pk0
	on inducks_story (storycode)
;

create table inducks_storycodes
(
	storycode varchar(19) null,
	alternativecode varchar(72) null,
	unpackedcode varchar(82) null,
	codecomment varchar(43) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_storycodes (alternativecode)
;

create index pk0
	on inducks_storycodes (storycode, alternativecode)
;

create table inducks_storydescription
(
	storyversioncode varchar(19) null,
	languagecode varchar(7) null,
	desctext varchar(2814) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_storydescription (languagecode)
;

create index pk0
	on inducks_storydescription (storyversioncode, languagecode)
;

create table inducks_storyheader
(
	storyheadercode varchar(12) null,
	level varchar(1) null,
	title varchar(195) null,
	storyheadercomment varchar(544) null,
	countrycode varchar(2) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_storyheader (storyheadercode, level)
;

create table inducks_storyjob
(
	storyversioncode varchar(19) null,
	personcode varchar(79) null,
	plotwritartink varchar(1) null,
	storyjobcomment varchar(141) null,
	indirect enum('Y', 'N') null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_storyjob (personcode)
;

create index pk0
	on inducks_storyjob (storyversioncode, personcode, plotwritartink)
;

create table inducks_storyreference
(
	fromstorycode varchar(18) null,
	tostorycode varchar(17) null,
	referencereasonid int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_storyreference (tostorycode)
;

create index fk1
	on inducks_storyreference (referencereasonid)
;

create index pk0
	on inducks_storyreference (fromstorycode, tostorycode)
;

create table inducks_storysubseries
(
	storycode varchar(18) null,
	subseriescode varchar(144) null,
	storysubseriescomment varchar(23) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_storysubseries (subseriescode)
;

create index pk0
	on inducks_storysubseries (storycode, subseriescode)
;

create table inducks_storyurl
(
	storycode varchar(13) null,
	sitecode varchar(15) null,
	url varchar(40) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_storyurl (sitecode)
;

create index pk0
	on inducks_storyurl (storycode, sitecode)
;

create table inducks_storyversion
(
	storyversioncode varchar(19) null,
	storycode varchar(19) null,
	entirepages int(7) null,
	brokenpagenumerator int(7) null,
	brokenpagedenominator int(7) null,
	brokenpageunspecified enum('Y', 'N') null,
	kind varchar(1) null,
	rowsperpage int(7) null,
	columnsperpage int(7) null,
	appisxapp enum('Y', 'N') null,
	what varchar(1) null,
	appsummary text null,
	plotsummary text null,
	writsummary text null,
	artsummary text null,
	inksummary text null,
	creatorrefsummary text null,
	keywordsummary text null,
	estimatedpanels int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk1
	on inducks_storyversion (storycode)
;


create index pk0
	on inducks_storyversion (storyversioncode)
;

create table inducks_storyversion_nofulltext
(
	storyversioncode varchar(19) null,
	storycode varchar(19) null,
	entirepages int(7) null,
	brokenpagenumerator int(7) null,
	brokenpagedenominator int(7) null,
	brokenpageunspecified enum('Y', 'N') null,
	kind varchar(1) null,
	rowsperpage int(7) null,
	columnsperpage int(7) null,
	appisxapp enum('Y', 'N') null,
	what varchar(1) null,
	appsummary text null,
	plotsummary text null,
	writsummary text null,
	artsummary text null,
	inksummary text null,
	creatorrefsummary text null,
	keywordsummary text null,
	estimatedpanels int(7) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk1
	on inducks_storyversion_nofulltext (storycode)
;

create index pk0
	on inducks_storyversion_nofulltext (storyversioncode)
;

create table inducks_studio
(
	studiocode varchar(23) null,
	countrycode varchar(2) null,
	studioname varchar(24) null,
	city varchar(12) null,
	description varchar(415) null,
	othertext varchar(94) null,
	photofilename varchar(18) null,
	photocomment varchar(40) null,
	photosource varchar(42) null,
	studiorefs varchar(204) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_studio (countrycode)
;

create index pk0
	on inducks_studio (studiocode)
;

create table inducks_studiowork
(
	studiocode varchar(23) null,
	personcode varchar(24) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_studiowork (personcode)
;

create index pk0
	on inducks_studiowork (studiocode, personcode)
;

create table inducks_subseries
(
	subseriescode varchar(50) null,
	subseriesname varchar(54) null,
	official enum('Y', 'N') null,
	subseriescomment varchar(285) null,
	subseriescategory varchar(40) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_subseries (subseriescode)
;

create table inducks_subseriesname
(
	subseriescode varchar(42) null,
	languagecode varchar(7) null,
	subseriesname varchar(137) null,
	preferred enum('Y', 'N') null,
	subseriesnamecomment varchar(29) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_subseriesname (languagecode)
;

create index pk0
	on inducks_subseriesname (subseriescode, languagecode)
;

create table inducks_substory
(
	storycode varchar(12) null,
	originalstoryversioncode varchar(12) null,
	superstorycode varchar(13) null,
	part varchar(3) null,
	firstpublicationdate varchar(10) null,
	title varchar(76) null,
	substorycomment varchar(349) null,
	error enum('Y', 'N') null,
	locked enum('Y', 'N') null,
	inputfilecode int(7) null,
	maintenanceteamcode varchar(8) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_substory (firstpublicationdate)
;

create index pk0
	on inducks_substory (storycode)
;

create table inducks_team
(
	teamcode varchar(13) null,
	teamdescriptionname varchar(33) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_team (teamcode)
;

create table inducks_teammember
(
	teamcode varchar(13) null,
	personcode varchar(3) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_teammember (teamcode)
;

create table inducks_ucrelation
(
	universecode varchar(28) null,
	charactercode varchar(45) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_ucrelation (charactercode)
;

create index pk0
	on inducks_ucrelation (universecode, charactercode)
;

create table inducks_universe
(
	universecode varchar(28) null,
	universecomment varchar(1) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index pk0
	on inducks_universe (universecode)
;

create table inducks_universename
(
	universecode varchar(28) null,
	languagecode varchar(5) null,
	universename varchar(76) null
)
engine=MyISAM collate=utf8_unicode_ci
;

create index fk0
	on inducks_universename (languagecode)
;

create index pk0
	on inducks_universename (universecode, languagecode)
;

