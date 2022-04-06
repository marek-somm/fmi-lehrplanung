import json

import classes.person as person


class Modul:
	def __init__(self, info1, info2):
		self.id = int(info1["id"])
		self.modulcode = info1["Modulcode"]
		self.pr_nr = int(info1["Prüfungsnummer"])
		self.aktiv_von = 0
		self.aktiv_bis = 1000000
		self.aktiv = 1
		self.ects = info2["ECTS"]
		self.praesenzzeit = info2["Präsenzzeit"]
		self.workload = info2["Workload"]
		self.selbststudium = info2["Selbststudium"]
		self.person = info2["Modulverantwortung"] if "Modulverantwortung" in info2 else None
		self.turnus = info2["Modulturnus"]
		self.titel_de = info1["Modultitel_de"] if "Modultitel_de" in info1 else None
		self.titel_en = info1["Modultitel_en"] if "Modultitel_en" in info1 else None
		self.zusammensetzung = info2["Zusammensetzung"] if "Zusammensetzung" in info2 else None
		self.vorkenntnisse = info2["Vorkenntnisse"] if "Vorkenntnisse" in info2 else None
		self.art = info2["Modulart"] if "Modulart" in info2 else None
		self.inhalt = info2["Inhalte"] if "Inhalte" in info2 else None
		self.vorr_leistungspunkte = info2["Voraussetzung_Leistungspunkte"] if "Voraussetzung_Leistungspunkte" in info2 else None
		self.vorr_pruefung = info2["Voraussetzung_Modulpruefung"] if "Voraussetzung_Modulpruefung" in info2 else None
		self.vorr_zulassung = info2["Voraussetzung_Modulzulassung"] if "Voraussetzung_Modulzulassung" in info2 else None
		self.zusatzinfos = info2["Zusatzinformationen"] if "Zusatzinformationen" in info2 and info2["Zusatzinformationen"].strip() != "" else None
		self.literatur = info2["Literatur"] if "Literatur" in info2 else None
		self.ziele = info2["Qualifikationsziele"] if "Qualifikationsziele" in info2 else None
		self.personen = info2["Personen"]

	def get_id(self):
		return self.id

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["id"] = self.id
		info["code"] = self.modulcode
		info["active_from"] = self.aktiv_bis
		info["active_to"] = self.aktiv_bis
		info["active"] = self.aktiv
		info["ects"] = self.ects
		info["presence_time"] = self.praesenzzeit
		info["workload"] = self.workload
		info["rotation"] = self.turnus
		info["title_de"] = self.titel_de
		info["title_en"] = self.titel_en
		info["composition"] = self.zusammensetzung
		info["prior_knowledge"] = self.vorkenntnisse
		info["type"] = self.art
		info["content"] = self.inhalt
		info["requirement_creditpoints"] = self.vorr_leistungspunkte
		info["requirement_exam"] = self.vorr_pruefung
		info["requirement_admission"] = self.vorr_zulassung
		info["additional_info"] = self.zusatzinfos
		info["literature"] = self.literatur
		return str(info)
	

def init():
	elements = None
	with open("data/AlleModuleExtra.json", "r", encoding="utf-8") as f:
		elements = json.load(f)

	infos = None
	with open("data/AlleModule.json", "r", encoding="utf-8") as f:
		infos = json.load(f)

	module = []

	for element in elements:
		info = infos[element["Modulcode"]]
		info["Personen"] = person.initFromModule(info["Modulverantwortung"], info["Modulcode"]) if "Modulverantwortung" in info else None
		modul = Modul(element, info)
		module.append(modul)

	return module
