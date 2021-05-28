import sqlite3

conn = sqlite3.connect('arktur.db')
c = conn.cursor()

# CLEAR TABLES

tables = c.execute('''SELECT name FROM sqlite_master WHERE type="table"''').fetchall()
for table in tables:
	c.execute('''DROP TABLE IF EXISTS {}'''.format(table[0]))

# CREATE TABLES 

c.execute('''CREATE TABLE MODUL(
	[ID] integer PRIMARY KEY,
	[modulcode] text UNIQUE NOT NULL
);''')

c.execute('''CREATE TABLE MODUL_INFO (
	[modulID] integer NOT NULL,
	[aktivvon] integer NOT NULL,
	[aktivbis] integer NOT NULL,
	[ects] integer NOT NULL,
	[praesenzzeit] integer NOT NULL,
	[workload] integer NOT NULL,
	[turnus] integer NOT NULL,
	[lp] integer NOT NULL,
	[titel_de] text NOT NULL,
	[titel_en] text NOT NULL,
	[zusammensetzung] text,
	[vorkenntnisse] text,
	[art] text,
	[inhalte] text,
	[vor_lp] text,
	[vor_pruefung] text,
	[vor_zulassung] text,
	[zusatzinfos] text,
	[literatur] text
)''')

c.execute('''CREATE TABLE MODUL_TURNUS (
	[ID] integer PRIMARY KEY,
	[name] text
)''')

c.execute('''CREATE TABLE PERSON (
	[ID] integer PRIMARY KEY,
	[friedolinID] integer,
	[vorname] text,
	[nachname] text,
	[grad] text
)''')

c.execute('''CREATE TABLE ABSCHLUSS (
	[ID] integer PRIMARY KEY,
	[name] text NOT NULL,
	[name_mittel] text NOT NULL,
	[name_kurz] text NOT NULL
)''')

c.execute('''CREATE TABLE FACH (
	[ID] integer PRIMARY KEY,
	[name] text NOT NULL,
	[name_kurz] text NOT NULL
)''')

c.execute('''CREATE TABLE STUDIENGANG (
	[ID] integer PRIMARY KEY,
	[AKTIVVON] integer NOT NULL,
	[AKTIVBIS] integer NOT NULL,
	[abschlussID] integer NOT NULL,
	[fachID] integer NOT NULL,
	[po] integer NOT NULL,
	[name] text NOT NULL,
	[name_kurz] text NOT NULL
)''')

c.execute('''CREATE TABLE KONTO (
	[ID] integer PRIMARY KEY,
	[studiengangID] integer NOT NULL,
	[konto_nr] integer NOT NULL,
	[mutterkonto] integer,
	[name] text NOT NULL,
	[pflicht] tinyint NOT NULL
)''')



c.execute('''CREATE TABLE LEHRVERANSTALTUNG (
	[ID] integer PRIMARY KEY,
	[veranstaltungsnummer] integer UNIQUE NOT NULL,
	[art] string NOT NULL,
	[rhythmus] integer NOT NULL
)''')

c.execute('''CREATE TABLE LEHRVERANSTALTUNG_INFO (
	[ID] integer PRIMARY KEY,
	[lehrvID] integer NOT NULL,
	[semester] integer NOT NULL,
	[titel] string NOT NULL,
	[friedolinID] integer NOT NULL,
	[aktiv] tinyint NOT NULL,
	[sws] tinyint
)''')

c.execute('''CREATE TABLE LEHRVERANSTALTUNG_RHYTMUS (
	[ID] integer PRIMARY KEY,
	[name] string NOT NULL
)''')

c.execute('''CREATE TABLE PRUEFUNG (
	[VENR] integer PRIMARY KEY,
	[pnr] integer NOT NULL,
	[modulcode] text NOT NULL,
	[titel] text NOT NULL
)''')


# Mapping

c.execute('''CREATE TABLE MAP_MODUL_PERSON (
	[modulID] integer NOT NULL,
	[personenID] integer NOT NULL
)''')

c.execute('''CREATE TABLE MODUL_ZUORDNUNG (
	[studiengangID] integer NOT NULL,
	[kontoID] integer NOT NULL,
	[modulID] integer NOT NULL
)''')

# infoID -> lehrveranstaltung_info.ID
c.execute('''CREATE TABLE MAP_LEHRVERANSTALTUNG_PERSON (
	[infoID] integer NOT NULL,
	[personenID] integer NOT NULL,
	[rolle] string NOT NULL
)''')

c.execute('''CREATE TABLE MAP_PRUEFUNG_LEHRVERANSTALTUNG (
	[venr] integer NOT NULL,
	[lehrvInfoID] integer NOT NULL
)''')




conn.commit()
conn.close()
