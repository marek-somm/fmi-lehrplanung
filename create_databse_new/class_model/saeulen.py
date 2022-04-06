import json


class Mutterkonto:
	def __init__(self, info):
		self.name = info["Name"]
		self.id = info["id"]
		self.studiengang = info["Studiengang"]
		self.pflicht = info["Pflicht"]
		self.konto_nr = info["Kontonr"]
		self.kinder = []

	def add_child(self, kind):
		self.kinder.append(kind)

	def get_id(self):
		return self.id

	def is_konto(self, konto):
		if self.konto_nr == konto:
			return True
		return False

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["Name"] = self.name
		info["id"] = self.id
		info["Studiengang"] = self.studiengang
		info["Pflicht"] = self.pflicht
		info["Kontonr"] = self.konto_nr
		info["Kinder"] = [kind for kind in self.kinder]
		return str(info)


def add_kind_zu_mutterkonten(mutterkonto, kind):
	mutterkonto.add_child(kind.get_id())


if __name__ == "__main__":
	konten = None
	mutterkonten = []
	with open("data/Konten.json", "r", encoding="utf-8") as f:
		konten = json.load(f)

	not_found = []

	kontonr_to_konto = {}
	for konto in konten:
		if konto["Mutterkonto"] == '-1':
			mk = Mutterkonto(konto)
			mutterkonten.append(mk)
			kontonr_to_konto[konto["Kontonr"]] = mk
		else:
			mutter_nr = konto["Mutterkonto"]
			if mutter_nr in kontonr_to_konto:
				mk = Mutterkonto(konto)
				add_kind_zu_mutterkonten(
					 kontonr_to_konto[konto["Mutterkonto"]], mk)
				mutterkonten.append(mk)
				kontonr_to_konto[konto["Kontonr"]] = mk
			else:
				not_found.append(konto)

	assert len(not_found) == 0, "there are things we cannot decide."

	with open("saeulen_umstrukturiert.json", "w") as f:
		f.write(str(mutterkonten).replace("'", '"'))

		with open("saeulen_umstrukturiert.json", "r") as f:
			konten = json.load(f)
