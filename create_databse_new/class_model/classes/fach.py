import json


class Fach:
	def __init__(self, info):
		self.id = int(info["id"])
		self.name = info["Name"]
		self.name_kurz = info["Name_Kurz"]

	def get_id(self):
		return self.id

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["id"] = self.id
		info["name"] = self.name
		info["name_short"] = self.name_kurz
		return str(info)


def init():
	elements = None
	with open("data/Fächer.json", "r", encoding="utf-8") as f:
		elements = json.load(f)

	faecher = []

	for element in elements:
		fach = Fach(element)
		faecher.append(fach)

	return faecher
