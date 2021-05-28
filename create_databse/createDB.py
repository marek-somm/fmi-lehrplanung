import sqlite3

conn = sqlite3.connect('arktur.db')
c = conn.cursor()

# CLEAR TABLES

tables = c.execute('''SELECT name FROM sqlite_master WHERE type="table"''').fetchall()
for table in tables:
	c.execute('''DROP TABLE IF EXISTS {}'''.format(table[0]))

# CREATE TABLES 

# Zuordnung
c.execute('''CREATE TABLE [Abschluss] (
  [abschlussID] integer,
  [name] text NOT NULL,
  [name_mittel] text NOT NULL,
  [name_kurz] text NOT NULL,
  PRIMARY KEY ([abschlussID])
)''')

c.execute('''CREATE TABLE [Konto] (
  [kontoID] integer,
  [studiengangID] integer NOT NULL,
  [konto_nr] integer NOT NULL,
  [mutterkonto] integer,
  [name] text NOT NULL,
  [pflicht] integer NOT NULL,
  PRIMARY KEY ([kontoID])
)''')

c.execute('''CREATE TABLE [Fach] (
  [fachID] text,
  [name] text NOT NULL,
  [name_kurz] text NOT NULL,
  PRIMARY KEY ([fachID])
)''')

c.execute('''CREATE TABLE [Studiengang] (
  [studiengangID] integer,
  [aktiv_von] integer NOT NULL,
  [aktiv_bis] integer NOT NULL,
  [abschlussID] integer NOT NULL,
  [fachID] integer NOT NULL,
  [po] integer NOT NULL,
  [name] text NOT NULL,
  [name_kurz] text NOT NULL,
  PRIMARY KEY ([studiengangID])
)''')


# Modul
c.execute('''CREATE TABLE [Modul] (
  [modulcode] text,
  [aktiv_von] integer,
  [aktiv_bis] integer NOT NULL,
  [ects] integer NOT NULL,
  [praesenzzeit] integer NOT NULL,
  [workload] integer NOT NULL,
  [turnusID] integer NOT NULL,
  [lp] integer NOT NULL,
  [titel_de] text NOT NULL,
  [titel_en] text NOT NULL,
  [zusammensetzung] text,
  [vorkentnisse] text,
  [art] text,
  [inhalte] text,
  [vor_lp] text,
  [vor_pruefungen] text,
  [vor_zulassung] text,
  [zusatzinfos] text,
  [literatur] text,
  PRIMARY KEY ([modulcode], [aktiv_von])
)''')

c.execute('''CREATE TABLE [Modul_Turnus] (
  [turnusID] integer,
  [name] text NOT NULL,
  PRIMARY KEY ([turnusID])
)''')


# Lehrveranstaltung
c.execute('''CREATE TABLE [Lehrveranstaltung] (
  [veranstaltungsnummer] integer,
  [art] text NOT NULL,
  [rhythmusID] integer NOT NULL,
  PRIMARY KEY ([veranstaltungsnummer])
)''')

c.execute

c.execute('''CREATE TABLE [Lehrveranstaltung_Info] (
  [lehrvID] integer,
  [veranstaltungsnummer] integer NOT NULL,
  [semester] integer NOT NULL,
  [titel] text NOT NULL,
  [friedolinID] integer NOT NULL,
  [aktiv] integer NOT NULL,
  [sws] integer,
  PRIMARY KEY ([lehrvID])
)''')

c.execute('''CREATE TABLE [Lehrveranstaltung_Inhalt] (
  [lehrvID] integer,
  [kommentar] text,
  [literatur] text,
  [bemerkung] text,
  [zielgruppe] text,
  [lerninhalte] text,
  [leistungsnachweis] text,
  PRIMARY KEY ([lehrvID])
)''')

c.execute('''CREATE TABLE [Lehrveranstaltung_Rhytmus] (
  [rhythmusID] integer,
  [name] text NOT NULL,
  PRIMARY KEY ([rhythmusID])
)''')


# Andere
c.execute('''CREATE TABLE [Person] (
  [personenID] integer,
  [friedolinID] integer,
  [vorname] text,
  [nachname] text,
  [grad] text,
  PRIMARY KEY ([personenID])
)''')

c.execute('''CREATE TABLE [Pruefung] (
  [VENR] integer,
  [pnr] integer NOT NULL,
  [modulcode] text NOT NULL,
  [titel] text NOT NULL,
  PRIMARY KEY ([VENR])
)''')


# Bridges
c.execute('''CREATE TABLE [BRIDGE_Modul_Konto] (
  [modulcode] text,
  [kontoID] integer,
  PRIMARY KEY ([modulcode], [kontoID])
)''')

c.execute('''CREATE TABLE [BRIDGE_Modul_Person] (
  [modulcode] text,
  [personenID] integer,
  PRIMARY KEY ([modulcode], [personenID])
)''')

c.execute('''CREATE TABLE [BRIDGE_Lehrveranstaltung_Person] (
  [lehrvID] integer NOT NULL,
  [personenID] integer NOT NULL,
  [rolle] text NOT NULL
)''')

c.execute('''CREATE TABLE [BRIDGE_Lehrveranstaltung_Pruefung] (
  [lehrvID] integer NOT NULL,
  [VENR] integer NOT NULL
)''')


conn.commit()
conn.close()
