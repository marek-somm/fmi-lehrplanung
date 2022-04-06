import json


class Person:
	def __init__(self, info):
		self.vorname = info[0] if info[0] != "" else None
		self.nachname = info[1]

	def __str__(self):
		return self.__repr__()

	def __repr__(self):
		info = {}
		info["forename"] = self.vorname
		info["surname"] = self.nachname
		return str(info)


def initFromModule(elements, modulcode):
	elements = elements.replace("Hochschullehrer der AG Stochastik", "N.N.")
	elements = elements.replace("Hochschullehrer des Instituts für Mathematik", "N.N.")
	elements = elements.replace("Betreuer der Bachelor-Arbeit entsprechend Prüfungsordnung §20(3)", "N.N.")
	elements = elements.replace("Betreuer der Master-Arbeit entsprechend Prüfungsordnung §20(3)", "N.N.")
	elements = elements.replace("Dozenten des Institutes für Mathematik", "N.N.")
	elements = elements.replace("Der Fachvertreter des gewählten Bereiches (siehe Inhalte)", "N.N.")
	elements = elements.replace("\n", "").split(", ") if modulcode not in ["FMI-MA3807", "FMI-MA3808"] else []

	personen = []
	for person in elements:
		names = person.split(" ")[::-1]
		forename = names[-1] if len(names) >= 2 else None
		surname = names[0]
		surname = surname if not surname == "N.N." else None
		personen.append(Person([forename, surname]))
	
	return personen
		


def initArray(array):
	elements = array

	personen = []

	for element in elements:
		if element.strip() != "":
			split = element.split(",")
			person = Person([split[1], split[0]])
			personen.append(person)

	return personen


def init():
	elements = None
	with open("data/Personen.txt", "r", encoding="utf-8") as f:
		elements = f.readlines()
	
	personen = []

	for element in elements:
		if element.strip() != "":
			split = element.strip().split(";:;")
			person = Person(split)
			personen.append(person)

	return personen
