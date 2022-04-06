import json


class Konto:
	def __init__(self, info):
		self.id = int(info["id"])
		self.studiengang = int(info["Studiengang"])
		self.parent = int(info["Parent"]) if "Parent" in info else None
		self.name = info["Name"]
		self.pflicht = int(info["Pflicht"])
		self.konto_nr = int(info["Kontonr"])

	def get_id(self):
		return self.id

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["id"] = self.id
		info["field_of_study_id"] = self.studiengang
		info["parent_id"] = self.parent
		info["name"] = self.name
		info["obligational"] = self.pflicht
		info["nr"] = self.konto_nr
		return str(info)


def init():
	konten = None
	mutterkonten = []
	with open("data/Konten.json", "r", encoding="utf-8") as f:
		konten = json.load(f)

	not_found = []

	kontonr_to_konto = {}
	for konto in konten:
		if konto["Mutterkonto"] == '-1':
			mk = Konto(konto)
			mutterkonten.append(mk)
			kontonr_to_konto[konto["Kontonr"]] = mk
		else:
			mutter_nr = konto["Mutterkonto"]
			if mutter_nr in kontonr_to_konto:
				konto["Parent"] = kontonr_to_konto[konto["Mutterkonto"]].get_id()
				mk = Konto(konto)
				mutterkonten.append(mk)
				kontonr_to_konto[konto["Kontonr"]] = mk
			else:
				not_found.append(konto)

	assert len(not_found) == 0, "there are things we cannot decide."

	return mutterkonten
