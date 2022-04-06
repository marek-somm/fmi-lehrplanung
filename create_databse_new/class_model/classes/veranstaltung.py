import json

import classes.pruefung as pruefung
import classes.person as person


class Veranstaltung:
	def __init__(self, info):
		self.id = int(info["id"])
		self.vnr = int(info["Veranstaltungsnummer"])
		self.semester = int(info["Semester"])
		self.titel = info["Titel"]
		self.aktiv = info["aktiv"]
		self.sws = int(info["SWS"]) if "SWS" in info and info["SWS"] != "" else None
		self.art = info["Veranstaltungsart"]
		self.turnus = int(info["Rhythmus"]) if "Rhythmus" in info else None
		self.changed = 0
		self.pruefungen = pruefung.init(info["Prüfungen"]) if "Prüfungen" in info else None
		self.personen = info["Personen"]

	def get_id(self):
		return str(self.id)

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["id"] = self.id
		info["vnr"] = self.vnr
		info["semester"] = self.semester
		info["title"] = self.titel
		info["active"] = self.aktiv
		info["sws"] = self.sws
		info["type"] = self.art
		info["rotation"] = self.turnus
		info["changed"] = self.changed
		info["exams"] = self.preufungen
		return str(info)


def convertSemester(string_semester):
	semester = string_semester.replace("Veranstaltungen_", "")
	semester += "1" if semester.__contains__("WiSe") else "0"
	semester = semester.replace("SoSe", "20").replace("WiSe", "20")
	return int(semester)


def processFile(file, length):
	rotation = {
   	"keine Übernahme": 0,
   	"Jedes Semester": 1,
   	"Jedes 2. Semester": 2
	}

	elements = None
	with open("data/Lehrveranstaltungen/" + file + ".json", "r", encoding="utf-8") as f:
		elements = json.load(f)

	veranstaltungen = []

	for element in elements:
		element = elements[element]
		element["id"] = int(length) + len(veranstaltungen) + 1
		element["Semester"] = convertSemester(file)

		if "Personen" in element:
			groups = element["Personen"]
			persons = []
			if "verantwortlich" in groups: persons += person.initArray(groups["verantwortlich"])
			if "organisatorisch" in groups: persons += person.initArray(groups["organisatorisch"])
			if "begleitend" in groups: persons += person.initArray(groups["begleitend"])
			element["Personen"] = persons
		else:
			element["Personen"] = None

		if "Rhythmus" in element:
			element["Rhythmus"] = rotation[element["Rhythmus"]]

		veranstaltung = Veranstaltung(element)
		veranstaltungen.append(veranstaltung)

	return veranstaltungen



def init():
	files = ['Veranstaltungen_SoSe15', 'Veranstaltungen_WiSe15', 'Veranstaltungen_SoSe16', 'Veranstaltungen_WiSe16', 'Veranstaltungen_SoSe17', 'Veranstaltungen_WiSe17',
        'Veranstaltungen_SoSe18', 'Veranstaltungen_WiSe18', 'Veranstaltungen_SoSe19', 'Veranstaltungen_WiSe19', 'Veranstaltungen_SoSe20', 'Veranstaltungen_WiSe20', 'Veranstaltungen_SoSe21']

	veranstaltungen = []

	for file in files:
		veranstaltungen += processFile(file, len(veranstaltungen))
	
	return veranstaltungen