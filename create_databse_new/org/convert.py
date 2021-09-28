import json
import pprint

with open("data/Lehrveranstaltungen/Veranstaltungen_SoSe21.json") as file:
	data = json.load(file)

i = ""
last = 0

out = {}

for elem in data:
	for key in data[elem]:
		if key not in out:
			out[key] = type(key)

pprint.pprint(out)