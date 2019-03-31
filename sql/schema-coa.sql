create table inducks_appearance
(
  storyversioncode varchar(19) not null,
  charactercode varchar(62) not null,
  number int(7) null,
  appearancecomment varchar(209) null,
  primary key (storyversioncode, charactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_appearance0
  on inducks_appearance (charactercode);

create index fk_inducks_appearance1
  on inducks_appearance (appearancecomment);

create table inducks_character
(
  charactercode varchar(69) not null
    primary key,
  charactername varchar(69) null,
  official enum('Y', 'N') null,
  onetime enum('Y', 'N') null,
  heroonly enum('Y', 'N') null,
  charactercomment varchar(671) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create fulltext index fulltext_inducks_character
  on inducks_character (charactername);

create table inducks_characteralias
(
  charactercode varchar(31) null,
  charactername varchar(58) not null
    primary key
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_characteralias0
  on inducks_characteralias (charactercode);

create table inducks_characterdetail
(
  charactername varchar(7) null,
  charactercode varchar(6) not null
    primary key,
  number int(7) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_characterdetail0
  on inducks_characterdetail (charactername);

create table inducks_charactername
(
  charactercode varchar(38) not null,
  languagecode varchar(7) not null,
  charactername varchar(83) not null,
  preferred enum('Y', 'N') null,
  characternamecomment varchar(99) null,
  primary key (charactercode, languagecode, charactername)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_charactername0
  on inducks_charactername (languagecode);

create table inducks_characterreference
(
  fromcharactercode varchar(21) not null,
  tocharactercode varchar(20) not null,
  isgroupofcharacters enum('Y', 'N') null,
  primary key (fromcharactercode, tocharactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_characterreference0
  on inducks_characterreference (tocharactercode);

create table inducks_characterurl
(
  charactercode varchar(1) not null,
  sitecode varchar(1) not null,
  url varchar(1) null,
  primary key (charactercode, sitecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_characterurl0
  on inducks_characterurl (sitecode);

create table inducks_country
(
  countrycode varchar(2) not null
    primary key,
  countryname varchar(20) null,
  defaultlanguage varchar(7) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_countryname
(
  countrycode varchar(2) not null,
  languagecode varchar(5) not null,
  countryname varchar(56) null,
  primary key (countrycode, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_countryname0
  on inducks_countryname (languagecode);

create table inducks_currency
(
  currencycode varchar(3) not null
    primary key,
  currencyname varchar(18) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_currencyname
(
  currencycode varchar(3) not null,
  languagecode varchar(2) not null,
  shortcurrencyname varchar(18) null,
  longcurrencyname varchar(20) null,
  primary key (currencycode, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_currencyname0
  on inducks_currencyname (languagecode);

create table inducks_entry
(
  entrycode varchar(22) not null
    primary key,
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
  engine=MyISAM collate=utf8_unicode_ci;

create fulltext index entryTitleFullText
  on inducks_entry (title);

create index fk_inducks_entry0
  on inducks_entry (issuecode);

create index fk_inducks_entry1
  on inducks_entry (storyversioncode);

create index fk_inducks_entry2
  on inducks_entry (languagecode);

create index fk_inducks_entry3
  on inducks_entry (includedinentrycode);

create index fk_inducks_entry4
  on inducks_entry (position);

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
  engine=MyISAM collate=utf8_unicode_ci;

create index fk0
  on inducks_entry_nofulltext (issuecode);

create index fk1
  on inducks_entry_nofulltext (storyversioncode);

create index fk2
  on inducks_entry_nofulltext (languagecode);

create index fk3
  on inducks_entry_nofulltext (includedinentrycode);

create index fk4
  on inducks_entry_nofulltext (position);

create index pk0
  on inducks_entry_nofulltext (entrycode);

create table inducks_entrycharactername
(
  entrycode varchar(22) not null,
  charactercode varchar(55) not null,
  charactername varchar(88) null,
  primary key (entrycode, charactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_entrycharactername0
  on inducks_entrycharactername (charactercode);

create table inducks_entryjob
(
  entrycode varchar(19) not null,
  personcode varchar(50) not null,
  transletcol varchar(1) not null,
  entryjobcomment varchar(51) null,
  primary key (entrycode, personcode, transletcol)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_entryjob0
  on inducks_entryjob (personcode);

create table inducks_entryurl
(
  entrycode varchar(21) null,
  sitecode varchar(11) null,
  pagenumber int(7) null,
  url varchar(87) null,
  id int auto_increment
    primary key
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_entryurl0
  on inducks_entryurl (entrycode);

create index fk_inducks_entryurl1
  on inducks_entryurl (sitecode);

create index fk_inducks_entryurl2
  on inducks_entryurl (url);

create table inducks_equiv
(
  issuecode varchar(15) not null,
  equivid int(7) not null,
  equivcomment varchar(3) null,
  primary key (issuecode, equivid)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_equiv0
  on inducks_equiv (equivid);

create table inducks_herocharacter
(
  storycode varchar(18) not null,
  charactercode varchar(54) not null,
  number int(7) null,
  primary key (storycode, charactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_herocharacter0
  on inducks_herocharacter (charactercode);

create table inducks_inputfile
(
  inputfilecode int(7) not null
    primary key,
  path varchar(11) null,
  filename varchar(22) null,
  layout varchar(10) null,
  locked enum('Y', 'N') null,
  maintenanceteamcode varchar(8) null,
  countrycode varchar(2) null,
  languagecode varchar(7) null,
  producercode varchar(15) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_issue
(
  issuecode varchar(17) not null
    primary key,
  issuerangecode varchar(15) null,
  publicationcode varchar(12) null,
  issuenumber varchar(12) null,
  title varchar(158) null,
  size varchar(61) null,
  pages varchar(82) null,
  price varchar(160) null,
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
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_issue0
  on inducks_issue (issuerangecode);

create index fk_inducks_issue1
  on inducks_issue (publicationcode);

create table inducks_issuecollecting
(
  collectingissuecode varchar(17) not null,
  collectedissuecode varchar(15) not null,
  primary key (collectingissuecode, collectedissuecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_issuecollecting0
  on inducks_issuecollecting (collectedissuecode);

create table inducks_issuedate
(
  issuecode varchar(17) not null,
  date varchar(10) not null,
  kindofdate varchar(76) null,
  primary key (issuecode, date)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_issuejob
(
  issuecode varchar(17) not null,
  personcode varchar(48) not null,
  inxtransletcol varchar(1) not null,
  issuejobcomment varchar(32) null,
  primary key (issuecode, personcode, inxtransletcol)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_issuejob0
  on inducks_issuejob (personcode);

create table inducks_issueprice
(
  issuecode varchar(17) not null,
  amount varchar(86) not null,
  currency varchar(14) null,
  comment varchar(64) null,
  sequencenumber int(7) null,
  primary key (issuecode, amount)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_issuerange
(
  issuerangecode varchar(15) not null
    primary key,
  publicationcode varchar(9) null,
  title varchar(229) null,
  circulation varchar(25) null,
  issuerangecomment varchar(468) null,
  numbersarefake enum('Y', 'N') null,
  error enum('Y', 'N') null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_issuerange0
  on inducks_issuerange (publicationcode);

create table inducks_issueurl
(
  issuecode varchar(14) not null,
  sitecode varchar(12) not null,
  url varchar(12) null,
  primary key (issuecode, sitecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_issueurl0
  on inducks_issueurl (sitecode);

create table inducks_language
(
  languagecode varchar(7) not null
    primary key,
  defaultlanguagecode varchar(5) null,
  languagename varchar(20) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_language0
  on inducks_language (defaultlanguagecode);

create table inducks_languagename
(
  desclanguagecode varchar(5) not null,
  languagecode varchar(7) not null,
  languagename varchar(57) null,
  primary key (desclanguagecode, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_languagename0
  on inducks_languagename (languagecode);

create table inducks_log
(
  number int(7) not null
    primary key,
  logkey varchar(57) null,
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
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_logdata
(
  logid varchar(4) not null
    primary key,
  category int(7) null,
  logtext varchar(108) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_logocharacter
(
  entrycode varchar(22) not null,
  charactercode varchar(54) not null,
  reallyintitle enum('Y', 'N') null,
  number int(7) null,
  logocharactercomment varchar(28) null,
  primary key (entrycode, charactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_logocharacter0
  on inducks_logocharacter (charactercode);

create table inducks_movie
(
  moviecode varchar(14) not null
    primary key,
  title varchar(62) null,
  moviecomment varchar(570) null,
  appsummary varchar(523) null,
  moviejobsummary varchar(1291) null,
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
  engine=MyISAM collate=utf8_unicode_ci;

create fulltext index fulltext_inducks_movie
  on inducks_movie (appsummary, moviejobsummary);

create table inducks_moviecharacter
(
  moviecode varchar(13) not null,
  charactercode varchar(36) not null,
  istitlecharacter enum('Y', 'N') null,
  primary key (moviecode, charactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_moviecharacter0
  on inducks_moviecharacter (charactercode);

create table inducks_moviejob
(
  moviecode varchar(13) not null,
  personcode varchar(39) not null,
  role varchar(15) not null,
  moviejobcomment varchar(82) null,
  indirect enum('Y', 'N') null,
  primary key (moviecode, personcode, role)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_moviejob0
  on inducks_moviejob (personcode);

create table inducks_moviereference
(
  storycode varchar(17) not null,
  moviecode varchar(14) not null,
  referencereasonid int(7) null,
  frommovietostory enum('Y', 'N') null,
  primary key (storycode, moviecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_moviereference0
  on inducks_moviereference (moviecode);

create index fk_inducks_moviereference1
  on inducks_moviereference (referencereasonid);

create table inducks_person
(
  personcode varchar(79) not null
    primary key,
  nationalitycountrycode varchar(2) null,
  fullname varchar(79) null,
  official enum('Y', 'N') null,
  personcomment varchar(221) null,
  unknownstudiomember enum('Y', 'N') null,
  isfake enum('Y', 'N') null,
  numberofindexedissues int(7) null,
  birthname varchar(37) null,
  borndate varchar(10) null,
  bornplace varchar(30) null,
  deceaseddate varchar(10) null,
  deceasedplace varchar(31) null,
  education varchar(189) null,
  moviestext varchar(879) null,
  comicstext varchar(927) null,
  othertext varchar(307) null,
  photofilename varchar(32) null,
  photocomment varchar(68) null,
  photosource varchar(67) null,
  personrefs varchar(180) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_person0
  on inducks_person (nationalitycountrycode);

create fulltext index fulltext_inducks_person
  on inducks_person (fullname, birthname);

create table inducks_personalias
(
  personcode varchar(31) null,
  surname varchar(48) null,
  givenname varchar(31) null,
  official enum('Y', 'N') null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_personalias0
  on inducks_personalias (personcode);

create table inducks_personurl
(
  personcode varchar(24) not null,
  sitecode varchar(15) not null,
  url varchar(31) null,
  primary key (personcode, sitecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_personurl0
  on inducks_personurl (sitecode);

create table inducks_publication
(
  publicationcode varchar(12) not null
    primary key,
  countrycode varchar(2) null,
  languagecode varchar(7) null,
  title varchar(131) null,
  size varchar(61) null,
  publicationcomment varchar(1354) null,
  circulation varchar(4) null,
  numbersarefake enum('Y', 'N') null,
  error enum('Y', 'N') null,
  locked enum('Y', 'N') null,
  inxforbidden enum('Y', 'N') null,
  inputfilecode int(7) null,
  maintenanceteamcode varchar(9) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_publication0
  on inducks_publication (countrycode);

create index fk_inducks_publication1
  on inducks_publication (languagecode);

create fulltext index fulltext_inducks_publication
  on inducks_publication (title);

create table inducks_publicationcategory
(
  publicationcode varchar(12) not null
    primary key,
  category varchar(61) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_publicationname
(
  publicationcode varchar(9) not null
    primary key,
  publicationname varchar(62) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_publicationurl
(
  publicationcode varchar(10) not null,
  sitecode varchar(16) not null,
  url varchar(236) null,
  primary key (publicationcode, sitecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_publicationurl0
  on inducks_publicationurl (sitecode);

create table inducks_publisher
(
  publisherid varchar(84) not null
    primary key,
  publishername varchar(84) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create fulltext index fulltext_inducks_publisher
  on inducks_publisher (publishername);

create table inducks_publishingjob
(
  publisherid varchar(84) not null,
  issuecode varchar(17) not null,
  publishingjobcomment varchar(67) null,
  primary key (publisherid, issuecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_publishingjob0
  on inducks_publishingjob (issuecode);

create table inducks_referencereason
(
  referencereasonid int(7) not null
    primary key,
  referencereasontext varchar(96) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_referencereasonname
(
  referencereasonid int(7) not null,
  languagecode varchar(2) not null,
  referencereasontranslation varchar(28) null,
  primary key (referencereasonid, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_referencereasonname0
  on inducks_referencereasonname (languagecode);

create table inducks_site
(
  sitecode varchar(16) not null
    primary key,
  urlbase varchar(51) null,
  images enum('Y', 'N') null,
  sitename varchar(85) null,
  sitelogo varchar(107) null,
  properties varchar(1) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statcharactercharacter
(
  charactercode varchar(45) not null,
  cocharactercode varchar(45) null,
  total int(7) not null,
  yearrange varchar(142) null,
  primary key (charactercode, total)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statcharactercountry
(
  charactercode varchar(45) not null,
  countrycode varchar(2) not null,
  total int(7) null,
  primary key (charactercode, countrycode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statcharacterstory
(
  charactercode varchar(45) not null,
  productionletter varchar(1) not null,
  total int(7) null,
  yearrange varchar(105) null,
  primary key (charactercode, productionletter)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statpersoncharacter
(
  personcode varchar(31) not null,
  charactercode varchar(45) null,
  total int(7) not null,
  yearrange varchar(111) null,
  primary key (personcode, total)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statpersoncountry
(
  personcode varchar(31) not null,
  countrycode varchar(2) not null,
  total int(7) null,
  primary key (personcode, countrycode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statpersonperson
(
  personcode varchar(31) not null,
  copersoncode varchar(31) null,
  total int(7) not null,
  yearrange varchar(59) null,
  primary key (personcode, total)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_statpersonstory
(
  personcode varchar(31) not null,
  productionletter varchar(1) not null,
  total int(7) null,
  yearrange varchar(62) null,
  primary key (personcode, productionletter)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_story
(
  storycode varchar(19) not null
    primary key,
  originalstoryversioncode varchar(19) null,
  creationdate varchar(21) null,
  firstpublicationdate varchar(10) null,
  endpublicationdate varchar(10) null,
  title varchar(222) null,
  usedifferentcode varchar(20) null,
  storycomment varchar(664) null,
  error enum('Y', 'N') null,
  repcountrysummary varchar(91) null,
  storyparts int(7) null,
  locked enum('Y', 'N') null,
  inputfilecode int(7) null,
  issuecodeofstoryitem varchar(14) null,
  maintenanceteamcode varchar(8) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_story0
  on inducks_story (originalstoryversioncode);

create index fk_inducks_story1
  on inducks_story (firstpublicationdate);

create fulltext index fulltext_inducks_story
  on inducks_story (title, repcountrysummary);

create table inducks_storycodes
(
  storycode varchar(19) not null,
  alternativecode varchar(72) not null,
  unpackedcode varchar(82) null,
  codecomment varchar(43) null,
  primary key (storycode, alternativecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storycodes0
  on inducks_storycodes (alternativecode);

create table inducks_storydescription
(
  storyversioncode varchar(19) not null,
  languagecode varchar(7) not null,
  desctext varchar(2814) null,
  primary key (storyversioncode, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storydescription0
  on inducks_storydescription (languagecode);

create table inducks_storyheader
(
  storyheadercode varchar(12) not null,
  level varchar(1) not null,
  title varchar(195) null,
  storyheadercomment varchar(544) null,
  countrycode varchar(2) null,
  primary key (storyheadercode, level)
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_storyjob
(
  storyversioncode varchar(19) not null,
  personcode varchar(79) not null,
  plotwritartink varchar(1) not null,
  storyjobcomment varchar(141) null,
  indirect enum('Y', 'N') null,
  primary key (storyversioncode, personcode, plotwritartink)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storyjob0
  on inducks_storyjob (personcode);

create table inducks_storyreference
(
  fromstorycode varchar(18) not null,
  tostorycode varchar(17) not null,
  referencereasonid int(7) null,
  primary key (fromstorycode, tostorycode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storyreference0
  on inducks_storyreference (tostorycode);

create index fk_inducks_storyreference1
  on inducks_storyreference (referencereasonid);

create table inducks_storysubseries
(
  storycode varchar(18) not null,
  subseriescode varchar(144) not null,
  storysubseriescomment varchar(23) null,
  primary key (storycode, subseriescode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storysubseries0
  on inducks_storysubseries (subseriescode);

create table inducks_storyurl
(
  storycode varchar(13) not null,
  sitecode varchar(15) not null,
  url varchar(40) null,
  primary key (storycode, sitecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storyurl0
  on inducks_storyurl (sitecode);

create table inducks_storyversion
(
  storyversioncode varchar(19) not null
    primary key,
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
  appsummary varchar(620) null,
  plotsummary varchar(271) null,
  writsummary varchar(271) null,
  artsummary varchar(338) null,
  inksummary varchar(338) null,
  creatorrefsummary varchar(1671) null,
  keywordsummary varchar(4219) null,
  estimatedpanels int(7) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_storyversion1
  on inducks_storyversion (storycode);

create fulltext index fulltext_inducks_storyversion
  on inducks_storyversion (appsummary, plotsummary, writsummary, artsummary, inksummary, creatorrefsummary, keywordsummary);

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
  engine=MyISAM collate=utf8_unicode_ci;

create index fk1
  on inducks_storyversion_nofulltext (storycode);

create index pk0
  on inducks_storyversion_nofulltext (storyversioncode);

create table inducks_studio
(
  studiocode varchar(23) not null
    primary key,
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
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_studio0
  on inducks_studio (countrycode);

create table inducks_studiowork
(
  studiocode varchar(23) not null,
  personcode varchar(24) not null,
  primary key (studiocode, personcode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_studiowork0
  on inducks_studiowork (personcode);

create table inducks_subseries
(
  subseriescode varchar(50) not null
    primary key,
  subseriesname varchar(54) null,
  official enum('Y', 'N') null,
  subseriescomment varchar(285) null,
  subseriescategory varchar(46) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_subseriesname
(
  subseriescode varchar(44) not null,
  languagecode varchar(7) not null,
  subseriesname varchar(137) null,
  preferred enum('Y', 'N') null,
  subseriesnamecomment varchar(54) null,
  primary key (subseriescode, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_subseriesname0
  on inducks_subseriesname (languagecode);

create table inducks_substory
(
  storycode varchar(12) not null
    primary key,
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
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_substory0
  on inducks_substory (firstpublicationdate);

create table inducks_team
(
  teamcode varchar(13) not null
    primary key,
  teamdescriptionname varchar(33) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_teammember
(
  teamcode varchar(13) not null
    primary key,
  personcode varchar(3) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_ucrelation
(
  universecode varchar(28) not null,
  charactercode varchar(45) not null,
  primary key (universecode, charactercode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_ucrelation0
  on inducks_ucrelation (charactercode);

create table inducks_universe
(
  universecode varchar(28) not null
    primary key,
  universecomment varchar(1) null
)
  engine=MyISAM collate=utf8_unicode_ci;

create table inducks_universename
(
  universecode varchar(28) not null,
  languagecode varchar(5) not null,
  universename varchar(76) null,
  primary key (universecode, languagecode)
)
  engine=MyISAM collate=utf8_unicode_ci;

create index fk_inducks_universename0
  on inducks_universename (languagecode);

create table numeros_cpt
(
  Pays varchar(6) not null,
  Magazine varchar(8) not null,
  publicationcode varchar(15) not null,
  Numero varchar(8) not null,
  Cpt int null,
  primary key (publicationcode, Numero)
)
  collate=utf8_unicode_ci;

create index numeros_cpt_Pays_Magazine_uindex
  on numeros_cpt (publicationcode);

