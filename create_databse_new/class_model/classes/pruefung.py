import json


class Pruefung:
	def __init__(self, info):
		self.modulcode = info["Modul"]
		self.pnr = int(info["PNr"])
		self.titel = info["VETitel"]
		self.changed = 0

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["code"] = self.modulcode
		info["pnr"] = self.pnr
		info["title"] = self.titel
		info["changed"] = self.changed
		return str(info)


def init(data):
	pruefungen = []

	for element in data:
		pruefung = Pruefung(element)
		pruefungen.append(pruefung)

	return pruefungen
