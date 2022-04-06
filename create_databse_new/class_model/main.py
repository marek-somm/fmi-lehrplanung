import sqlite3
import time
import json

import classes.konto as konto
import classes.studiengang as studiengang
import classes.fach as fach
import classes.abschluss as abschluss
import classes.modul as modul
import classes.veranstaltung as veranstaltung
import classes.person as person

modules = modul.init()
events = veranstaltung.init()
persons = person.init()

categories = konto.init()
field_of_studies = studiengang.init()
subjects = fach.init()
degrees = abschluss.init()


def getTime():
	return time.strftime('%Y-%m-%d %H:%M:%S', time.localtime())


conn = sqlite3.connect('database.sqlite')
cur = conn.cursor()


def fillModules():
	cur.execute('''DELETE FROM modules''')
	for module in modules:
		time_now = getTime()
		cur.execute('''INSERT INTO modules
			(id,code,active_from,active_to,active,ects,presence_time,workload,rotation,title_de,title_en,composition,prior_knowledge,type,content,requirement_creditpoints,requirement_exam,requirement_admission,additional_info,literature,created_at,updated_at)
				VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)''',
			[module.id,module.modulcode,module.aktiv_von,module.aktiv_bis,module.aktiv,module.ects,module.praesenzzeit,module.workload,module.turnus,module.titel_de,module.titel_en,module.zusammensetzung,module.vorkenntnisse,module.art,module.inhalt,module.vorr_leistungspunkte,module.vorr_pruefung,module.vorr_zulassung,module.zusatzinfos,module.literatur,time_now,time_now]
		)


def fillEvents():
	cur.execute('''DELETE FROM events''')
	for event in events:
		time_now = getTime()
		cur.execute('''INSERT INTO events
			(id,vnr,semester,title,active,sws,type,rotation,changed,created_at,updated_at)
				VALUES (?,?,?,?,?,?,?,?,?,?,?)''',
			[event.id,event.vnr,event.semester,event.titel,event.aktiv,event.sws,event.art,event.turnus,event.changed,time_now,time_now]
		)


def fillPersons():
	cur.execute('''DELETE FROM users''')
	for person in persons:
		time_now = getTime()
		cur.execute('''INSERT INTO users
			(forename, surname, created_at, updated_at)
				VALUES (?,?,?,?)''',
			[person.vorname, person.nachname, time_now, time_now]
		)


def fillCategories():
	cur.execute('''DELETE FROM categories''')
	for category in categories:
		time_now = getTime()
		cur.execute('''INSERT INTO categories
			(id,field_of_study_id,parent_id,name,obligational,nr,created_at,updated_at)
				VALUES (?,?,?,?,?,?,?,?)''',
			[category.id,category.studiengang,category.parent,category.name,category.pflicht,category.konto_nr,time_now,time_now]
		)


def fillFieldOfStudies():
	cur.execute('''DELETE FROM field_of_studies''')
	for field_of_study in field_of_studies:
		time_now = getTime()
		cur.execute('''INSERT INTO field_of_studies
			(id,degree_id,subject_id,active_from,active_to,po_version,name,name_short,created_at,updated_at)
				VALUES (?,?,?,?,?,?,?,?,?,?)''',
			[field_of_study.id,field_of_study.abschluss,field_of_study.fach,field_of_study.aktiv_von,field_of_study.aktiv_bis,field_of_study.po,field_of_study.name,field_of_study.name_kurz,time_now,time_now]
		)


def fillSubjects():
	cur.execute('''DELETE FROM subjects''')
	for subject in subjects:
		time_now = getTime()
		cur.execute('''INSERT INTO subjects
			(id,name,name_short,created_at,updated_at)
				VALUES (?,?,?,?,?)''',
			[subject.id,subject.name,subject.name_kurz,time_now,time_now]
		)


def fillDegrees():
	cur.execute('''DELETE FROM degrees''')
	for degree in degrees:
		time_now = getTime()
		cur.execute('''INSERT INTO degrees
			(id,name,name_medium,name_short,created_at,updated_at)
				VALUES (?,?,?,?,?,?)''',
			[degree.id,degree.name,degree.name_mittel,degree.name_kurz,time_now,time_now]
		)


def fillCategoryModule():
	elements = None
	with open("data/Modulzuordnung.json", "r", encoding="utf-8") as f:
		elements = json.load(f)
	
	cur.execute('''DELETE FROM category_module''')
	for element in elements:
		category_id = int(element["Konto"])
		module_id = int(element["Modul"])
		time_now = getTime()
		cur.execute('''INSERT INTO category_module
			(category_id,module_id,created_at,updated_at)
				VALUES (?,?,?,?)''',
			[category_id,module_id,time_now,time_now])


def fillEventModule():
	cur.execute('''DELETE FROM event_module''')
	for event in events:
		exams = event.pruefungen

		if exams != None:
			for exam in exams:
				module_id = cur.execute('SELECT id FROM modules WHERE code is ?', [exam.modulcode]).fetchall()
				module_id = module_id[0][0] if len(module_id) > 0 else None
				time_now = getTime()
				
				if module_id is not None:
					cur.execute('''
						INSERT INTO event_module
							(event_id,module_id,pnr,description,title,changed,created_at,updated_at)
								VALUES (?,?,?,?,?,?,?,?)''',
						[event.id,module_id,exam.pnr,None,exam.titel,exam.changed,time_now,time_now])


def getUserId(person):
	user_id = cur.execute("SELECT id FROM users WHERE surname is ?", [person.nachname]).fetchall()
	if len(user_id) > 1:
		user_id = cur.execute("SELECT id FROM users WHERE forename is ? and surname is ?", [person.vorname, person.nachname]).fetchall()
	return user_id[0][0] if len(user_id) > 0 else None


def fillEventUser():
	cur.execute('''DELETE FROM event_user''')
	for event in events:
		persons = event.personen

		if persons != None:
			for person in persons:
				user_id = getUserId(person)
				time_now = getTime()
				
				if user_id is not None:
					cur.execute('''INSERT INTO event_user
						(event_id, user_id, created_at, updated_at) 
							VALUES(?,?,?,?)''',
                  [event.id, user_id, time_now, time_now])


def fillModuleUser():
	for module in modules:
		persons = module.personen
		
		if persons != None:
			for person in persons:
				user_id = getUserId(person)
				time_now = getTime()

				if(user_id is not None):
					cur.execute('''INSERT INTO module_user
						(module_id, user_id, created_at, updated_at) 
						VALUES(?,?,?,?)''', 
							[module.id, user_id, time_now, time_now])


fillModules()
fillEvents()
fillPersons()

fillCategories()
fillFieldOfStudies()
fillSubjects()
fillDegrees()

fillCategoryModule()
fillEventModule()
fillEventUser()
fillModuleUser()

conn.commit()
conn.close()


def debugList(list):
	#print(list)
	with open("output.json", "w", encoding="utf-8") as f:
		f.write(str(list).replace("'", '"').replace(";;;", "'").replace(":::", '\\"').replace("None", 'null'))


#debugList(modules)
