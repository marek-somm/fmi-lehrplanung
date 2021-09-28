import sqlite3
import json
import time

# data

with open("data/AlleModule.json") as file:
	data = json.load(file)
with open("data/Personen.txt", encoding='utf8') as file:
	allePersonen = file.readlines()

conn = sqlite3.connect('database.sqlite')
c = conn.cursor()

personen = {}

for person in allePersonen:
	if person.strip() != "":
		split = person.split(";:;")
		personenVorname = split[0]
		personenNachname = split[1]
		friedolinID = split[2]
		personenDegree = split[3]
		personenName = ""
		if(personenVorname != ""):
			personenName += personenVorname
		if(personenNachname != ""):
			if(personenVorname != ""):
				personenName += " "
			personenName += personenNachname

		if personenVorname.strip() == "":
			personenVorname = None
		if personenNachname.strip() == "":
			personenNachname = None
		if personenDegree.strip() == "":
			personenDegree = None
		if friedolinID.strip() == "":
			friedolinID = None

		personen[personenName] = {
			"id": len(personen),
			"friedolinID": friedolinID,
			"vorname": personenVorname,
			"nachname": personenNachname,
			"grad": personenDegree
      }

modulturnus = {
	"KEINE ANGABE": 0,
	"jedes Semester": 1,
	"jedes 2. Semester (jährlich)": 2,
	"jedes 2. Semester (ab Sommersemester)": 3,
	"jedes 2. Semester (ab Wintersemester)": 4,
	"alle 2 Jahre (ab Sommersemester)": 5,
	"alle 2 Jahre (ab Wintersemester)": 6,
	"jedes 3. Semester": 7,
	"Sommersemester, ggf. auch Wintersemester": 8,
	"Wintersemester, ggf. auch Sommersemester": 9,
	"unregelmäßig, siehe gegebenenfalls zusätzliche Informationen": 10,
	"jedes 3. Wintersemester": 11,
	"jedes 3. Sommersemester": 12
}

def getTime():
	return time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())

# clean tables

c.execute('''DELETE FROM users''')
c.execute('''DELETE FROM modules''')
c.execute('''DELETE FROM module_user''')

# INSERT PERSONEN
for person in personen:
	person = personen[person]
	time_now = getTime()
	c.execute('INSERT INTO users (forename, surname, created_at, updated_at) VALUES (?,?,?,?)',
				[person['vorname'], person['nachname'], time_now, time_now])


for elem in data:
	# INSERT MODUL
	modulcode = data[elem]['Modulcode']
	aktivvon = 0
	aktivbis = 1000000
	ects = str(data[elem]['ECTS'])
	praesenzzeit = str(data[elem]['Präsenzzeit'])
	workload = str(data[elem]['Workload'])
	#pnr = str(data[elem]['PNr'])
	turnus = str(data[elem]['Modulturnus'])
	titel = str(data[elem]['Modultitel'])
	zusammensetzung = str(data[elem]['Zusammensetzung']) if "Zusammensetzung" in data[elem] else None
	vorkenntnisse = str(data[elem]['Vorkenntnisse']) if "Vorkenntnisse" in data[elem] else None
	art = str(data[elem]['Modulart']) if "Modulart" in data[elem] else None
	inhalte = str(data[elem]['Inhalte']) if "Inhalte" in data[elem] else None
	vor_lp = str(data[elem]['Voraussetzung_Leistungspunkte']) if "Voraussetzung_Leistungspunkte" in data[elem] else None
	vor_pruefung = str(data[elem]['Voraussetzung_Modulpruefung']) if "Voraussetzung_Modulpruefung" in data[elem] else None
	vor_zulassung = str(data[elem]['Voraussetzung_Modulzulassung']) if "Voraussetzung_Modulzulassung" in data[elem] else None
	zusatzinfos = str(data[elem]['Zusatzinformationen']) if "Zusatzinformationen" in data[elem] and data[elem]["Zusatzinformationen"] != "" else None
	literatur = str(data[elem]['Literatur']) if "Literatur" in data[elem] else None
	time_now = getTime()

	c.execute('''INSERT INTO modules
		(modulecode,aktiv_from,aktiv_to,ects,presence_time,workload,rotation,title_de,title_en,composition,prior_knowledge,type,content,requirement_creditpoints,requirement_exam,requirement_admission,additional_info,literature,created_at,updated_at)
		VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)''',
		[modulcode,aktivvon,aktivbis,ects,praesenzzeit,workload,turnus,titel,"",zusammensetzung,vorkenntnisse,art,inhalte,vor_lp,vor_pruefung,vor_zulassung,zusatzinfos,literatur,time_now,time_now]
	)

	# INSERT MODUL_VERANTWORTUNG
	pers = str(data[elem]['Modulverantwortung']) if "Modulverantwortung" in data[elem] else "N.N."
	pers = pers.replace("Hochschullehrer der AG Stochastik", "N.N.")
	pers = pers.replace("Hochschullehrer des Instituts für Mathematik", "N.N.")
	pers = pers.replace("Betreuer der Bachelor-Arbeit entsprechend Prüfungsordnung §20(3)", "N.N.")
	pers = pers.replace("Betreuer der Master-Arbeit entsprechend Prüfungsordnung §20(3)", "N.N.")
	pers = pers.replace("Dozenten des Institutes für Mathematik", "N.N.")
	pers = pers.replace("Der Fachvertreter des gewählten Bereiches (siehe Inhalte)", "N.N.")
	pers = pers.replace("\n", "").split(", ") if modulcode not in ["FMI-MA3807", "FMI-MA3808"] else []
	for person in pers:
		names = person.split(" ")[::-1]
		forename = names[-1] if len(names) >= 2 else None
		surname = names[0]
		surname = surname if not surname == "N.N." else None
		#print(surname)
		#print(modulcode)
		user_id = c.execute("SELECT id FROM users WHERE surname is ?", [surname]).fetchall()

		if len(user_id) > 1:
			user_id = c.execute("SELECT id FROM users WHERE forename is ? and surname is ?", [forename, surname]).fetchall()
		
		user_id = user_id[0][0] if len(user_id) > 0 else None

		if(user_id is not None):
			c.execute('''
				INSERT INTO module_user (module_id, user_id, created_at, updated_at) 
				VALUES(
					(SELECT id FROM modules WHERE modulecode is ?), 
					?,
					?,?
					)''', 
					[modulcode, user_id, time_now, time_now])
		


# data AlleModuleExtra

with open("data/AlleModuleExtra.json", encoding='utf8') as file:
   data = json.load(file)

modulcodes = {}

for elem in data:
	modulcode = elem['Modulcode']
	modulcodes[elem['id']] = modulcode
	titelDE = elem['Modultitel_de']
	titelEN = elem['Modultitel_en']

	c.execute('''UPDATE modules SET title_de =?, title_en=? WHERE modulecode=?''',
		[titelDE, titelEN, modulcode]
	)

"""
# data Abschlüsse

with open("data/Abschlüsse.json", encoding='utf8') as file:
   data = json.load(file)

for elem in data:
	id = elem['id']
	name = elem['Name']
	nameM = elem['Name_Mittel']
	nameK = elem['Name_Kurz']
	
	c.execute('INSERT INTO Abschluss VALUES (?,?,?,?)',
		[id, name, nameM, nameK]
	)

# data Fächer

with open("data/Fächer.json", encoding='utf8') as file:
   data = json.load(file)

for elem in data:
	id = elem['id']
	name = elem['Name']
	nameK = elem['Name_Kurz']
	
	c.execute('INSERT INTO Fach VALUES (?,?,?)',
		[id, name, nameK]
	)

# data Studiengänge

with open("data/Studiengänge.json", encoding='utf8') as file:
   data = json.load(file)

for elem in data:
	abschluss = elem['Abschluss']
	fach = elem['Fach']
	po = elem['PO-Version']
	name = elem['Name']
	nameK = elem['Name_Kurz']
	von = elem['Aktiv_von']
	bis = elem['Aktiv_bis']
	
	c.execute('INSERT INTO Studiengang (aktiv_von,aktiv_bis,abschlussID,fachID,po,name,name_kurz) VALUES (?,?,?,?,?,?,?)',
		[von,bis,abschluss,fach,po,name,nameK]
	)

# data Konten

with open("data/Konten.json", encoding='utf8') as file:
   data = json.load(file)

for elem in data:
	studiengang = elem['Studiengang']
	kontonr = elem['Kontonr']
	mutterkonto = None if elem['Mutterkonto'] == "-1" else elem['Mutterkonto']
	name = elem['Name']
	pflicht = elem['Pflicht']
	
	c.execute('INSERT INTO Konto (studiengangID,konto_nr,mutterkonto,name,pflicht) VALUES (?,?,?,?,?)',
		[studiengang,kontonr,mutterkonto,name,pflicht]
	)

# data Modulzuordnung

with open("data/Modulzuordnung.json", encoding='utf8') as file:
   data = json.load(file)

for elem in data:
	studiengang = elem['Studiengang']
	konto = elem['Konto']
	modulID = elem['Modul']
	
	c.execute('INSERT INTO BRIDGE_Modul_Konto VALUES (?,?)',
		[modulcodes[modulID], konto]
	)

"""
conn.commit()
conn.close()
