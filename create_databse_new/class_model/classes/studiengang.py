import json


class Studiengang:
	def __init__(self, info):
		self.id = int(info["id"])
		self.abschluss = int(info["Abschluss"])
		self.fach = int(info["Fach"])
		self.aktiv_von = int(info["Aktiv_von"])
		self.aktiv_bis = int(info["Aktiv_bis"])
		self.po = int(info["PO-Version"])
		self.name = info["Name"]
		self.name_kurz = info["Name_Kurz"]

	def get_id(self):
		return self.id

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["id"] = self.id
		info["degree_id"] = self.abschluss
		info["subject_id"] = self.fach
		info["active_from"] = self.aktiv_von
		info["active_to"] = self.aktiv_bis
		info["po_version"] = self.po
		info["name"] = self.name
		info["name_short"] = self.name_kurz
		return str(info)


def init():
	elements = None
	with open("data/Studiengänge.json", "r", encoding="utf-8") as f:
		elements = json.load(f)

	studiengaenge = []

	for element in elements:
		studiengang = Studiengang(element)
		studiengaenge.append(studiengang)

	return studiengaenge
