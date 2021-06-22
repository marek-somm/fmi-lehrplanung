import axios from 'axios'
import store from '@/store'
import { useRouter } from "vue-router";

export class request {
	constructor() {
		this.loading = false
		this.error = null
		this.data = null
	}

	async getVeranstaltung(vnr, semester) {
		if (store.state.local) {
			return results.veranstaltung
		}
		await this.fetchData('search/', {
			typ: 'v',
			vnr: vnr,
			semester: semester,
		})
		return this.data
	}

	async searchVeranstaltung(titel, limit) {
		if (store.state.local) {
			return results.veranstaltungen
		}
		titel = titel.trim()
		await this.fetchData('search/', {
			typ: 'v',
			titel: titel,
			limit: limit,
		})
		if(titel == '') return null
		else return this.data
	}

	async getModul(modulcode) {
		if (store.state.local) {
			return results.modul
		}
		await this.fetchData('search/', {
			typ: 'm',
			modulcode: modulcode,
		})
		return this.data
	}

	async searchModul(titel, limit) {
		if (store.state.local) {
			return results.module
		}
		titel = titel.trim()
		await this.fetchData('search/', {
			typ: 'm',
			titel: titel,
			limit: limit,
		})
		if(titel == '') return null
		else return this.data
	}

	async session() {
		if(store.state.local) {
			return { success: true, level: store.state.seclevel }
		}
		await this.fetchData('session/test.php', {})
		return this.data
	}
	
	async login(user, pwd) {
		if(store.state.local) {
			return { success: true, level: store.state.seclevel }
		}
		await this.fetchData('session/login.php', {
			user: user,
			pwd: pwd,
		})
		return this.data
	}

	async logout() {
		const router = useRouter()
		await this.fetchData('session/logout.php', {})
		store.dispatch('User/setLogin', false)
		store.dispatch('User/setLevel', 0)
      router.push({name: 'Home'});
	}

	async saveVeranstaltung(veranstaltung){
		console.log(veranstaltung)
		await this.sendData('update/create.php', {
			data: veranstaltung
		})
		console.log("done")
		return this.data
		// speichert die übergebene veranstaltung in db
			// veranstaltung hat gleiches format wie return wert von getVeranstaltung
		//  return: ausgabecode
			// 0: falls speichern erfolgreich
			// 1: falls veranstaltungsnummer + semester kombination bereits vorhanden
			// -1: anderer fehler
	}

	async editVeranstaltung(veranstaltung, vnr, semester){
		console.log(veranstaltung, vnr, semester)
		await this.sendData('update/create.php', {
			data: veranstaltung
		})
		console.log("done")
		return this.data
		// speichert die übergebene veranstaltung in db
		// wobei veranstalltung mit gegebenen vnr und semester überschrieben wird
			// veranstaltung hat gleiches format wie return wert von getVeranstaltung
			// vnr und semester sind wie bei getVeranstaltung
		//  return: ausgabecode
			// 0: falls speichern erfolgreich
			// 1: falls veranstaltungsnummer + semester kombination bereits vorhanden
			// -1: anderer fehler
	}
	
	async toggleAktiv(vnr, semester){
		console.log(vnr, semester)
		// wechselt den aktivstatus der gegebenen veranstaltung
	}

	async fetchData(path, params) {
		this.loading = true
		this.error = null

		await axios
			.get('https://arktur.fmi.uni-jena.de/api/' + path, {
				headers: { 'Content-Type': 'application/json' },
				params,
			})
			.then((res) => {
				this.loading = false
				this.data = res.data
			})
			.catch((err) => {
				this.error = err
				this.loading = false
				this.data = null
			})
	}

	async sendData(path, params) {
		this.loading = true
		this.error = null

		await axios
			.get('https://arktur.fmi.uni-jena.de/api/' + path, {
				headers: { 'Content-Type': 'application/json' },
				params,
			})
			.then((res) => {
				this.loading = false
				this.data = res.data
			})
			.catch((err) => {
				this.error = err
				this.loading = false
				this.data = null
			})
	}
}

const results = {
	veranstaltung: {"data":{"titel":"ONLINE im SoSe 21: Objektorientierte Programmierung","veranstaltungsnummer":10018,"semester":20210,"friedolinID":184234,"aktiv":1,"sws":2,"turnus":"Jedes 2. Semester","art":"Vorlesung"},"content":{"Zielgruppe":null},"people":{"":[{"vorname":"Wolfram","nachname":"Amme","grad":"apl. Prof. Dr.\n","friedolinID":832},{"vorname":"Sven","nachname":"Sickert","grad":"Dr. rer. nat.\n","friedolinID":9747},{"vorname":"Andr\u00e9","nachname":"Sch\u00e4fer","grad":null,"friedolinID":12488}]},"exams":{"":[{"titel":"Deklarative und objektorientierte Programmierung: Vorlesungen","pnr":51181,"Modulcode":"FMI-IN0118"},{"titel":"Deklarative und objektorientierte Programmierung: Vorlesungen","pnr":51181,"Modulcode":"FMI-IN0118"},{"titel":"Objektorientierte Programmierung: Vorlesung","pnr":50411,"Modulcode":"FMI-IN0041"},{"titel":"Objektorientierte Programmierung: Vorlesung","pnr":50751,"Modulcode":"FMI-IN0075"}]}},
	veranstaltungen: {"data":{"SoSe 2021":[{"nr":10018,"titel":"ONLINE im SoSe 21: Objektorientierte Programmierung","semester":20210,"aktiv":1},{"nr":10026,"titel":"ONLINE im SoSe 21: Verfahren der Numerischen Mathematik und des Wissenschaftlichen Rechnens im Einsatz","semester":20210,"aktiv":1},{"nr":10027,"titel":"ONLINE im SoSe 21: Theoretische Informatik - Logik","semester":20210,"aktiv":0},{"nr":10030,"titel":"ONLINE-PLUS im SoSe 21: Didaktik der Mathematik A (Lehramt Gymnasium)","semester":20210,"aktiv":1},{"nr":10053,"titel":"ONLINE im SoSe 21: Rechnerstrukturen","semester":20210,"aktiv":1},{"nr":10078,"titel":"ONLINE im SoSe 21: Algorithmische Grundlagen des maschinellen Lernens (Statistische Lerntheorie)","semester":20210,"aktiv":1},{"nr":10080,"titel":"ONLINE im SoSe 21: Lineare Algebra und analytische Geometrie I (B.Sc. Physik)","semester":20210,"aktiv":1},{"nr":10083,"titel":"Grundlagen der Rechnerarithmetik","semester":20210,"aktiv":0},{"nr":10095,"titel":"Grundlagen der Modellierung neuronaler Systeme","semester":20210,"aktiv":0},{"nr":10098,"titel":"PRAESENZ im SoSe 21: Rechnersehen II","semester":20210,"aktiv":1},{"nr":10111,"titel":"ONLINE im SoSe 21: H\u00f6here Analysis 1","semester":20210,"aktiv":1},{"nr":10124,"titel":"ONLINE im SoSe 21: Mathematik 2 (B.Sc. Werkstoffwissenschaften, Geowissenschaften)","semester":20210,"aktiv":1},{"nr":10125,"titel":"ONLINE im SoSe 21: Mathematik 2 (B.Sc. Werkstoffwissenschaften, Geowissenschaften)","semester":20210,"aktiv":1},{"nr":10129,"titel":"Intercultural communication","semester":20210,"aktiv":0},{"nr":10131,"titel":"ONLINE im SoSe 21: Programmieren mit C#","semester":20210,"aktiv":1},{"nr":10133,"titel":"ONLINE im SoSe 21: Spezialverfahren der medizinischen Bildverarbeitung (MED-MDS003)","semester":20210,"aktiv":1},{"nr":10134,"titel":"ONLINE im SoSe 21: The Application-driven Hardware Revolution","semester":20210,"aktiv":1},{"nr":10135,"titel":"Portaltechnologien (Verteilte Systeme - Spezialisierung I)","semester":20210,"aktiv":0},{"nr":10139,"titel":"ONLINE im SoSe 21: Mustererkennung","semester":20210,"aktiv":0},{"nr":10142,"titel":"Aperiodische Ordnung","semester":20210,"aktiv":0}]},"count":20},
	modul: {"data":{"praesenzzeit":60,"workload":180,"lp":6,"turnus":"unregelm\u00e4\u00dfig, siehe gegebenenfalls zus\u00e4tzliche Informationen","titel_de":"3D-Strukturen biologischer Makromolek\u00fcle","titel_en":"3D Structures of Biological Makromolecules","zusammensetzung":"2V + 2\u00dc"},"content":{"Art":"Wahlpflichtmodul f\u00fcr den B.Sc. Bioinformatik (Wahlpflichtbereich 1)\nWahlpflichtmodul f\u00fcr den M.Sc. Bioinformatik (Bereich Bioinformatik)\nWahlpflichtmodul f\u00fcr den M.Sc. Computational Science - Anwendungen:\nBereich Bioinformatik und Neurowissenschaften","Inhalte":"Struktur und Eigenschaften der proteinogenen Aminos\u00e4uren, Sekund\u00e4r-, Supersekund\u00e4r- und Terti\u00e4rstrukturen von Proteinen, Arten der Bindungen in biologischen Makromolek\u00fclen, Modelle der Proteinfaltung, thermodynamische Eigenschaften von Proteinen, innere Koordinaten, Proteinstruktur-vorhersage, Nukleins\u00e4urestrukturen, Wirkstoff-Forschung und \u2013Design.","Vorkentnisse":"FMI-BI0027 (Biochemie)\nFMI-BI0028 (Grundlagen molekularer Strukturen), o.\u00e4.","Vorraussetzungen Leistungspunkte":"Klausur oder m\u00fcndliche Pr\u00fcfung zur Vorlesung","Vorraussetzungen Pr\u00fcfungen":"50 % der erreichbaren Punkte aus den \u00dcbungsaufgaben oder Abschlusskolloquium","Vorraussetzungen Zulassung":"keine","Literatur":"T. Schlick: Molecular Modeling and Simulation, Springer 2002. M.\nDaune: Molecular Biophysics, Oxford University Press 2006. A.\nTramontano: Protein Structure Prediction. Wiley-VCH 2006.","Zusatzinfos":null},"exams":{"WiSe 2020":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: Vorlesung","pnr":52211,"Vnr":19134,"semester":20201},{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: \u00dcbung","pnr":52211,"Vnr":55382,"semester":20201}],"WiSe 2019":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: Vorlesung","pnr":52211,"Vnr":19134,"semester":20191},{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: \u00dcbung","pnr":52211,"Vnr":55382,"semester":20191}],"WiSe 2018":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: Vorlesung","pnr":52211,"Vnr":19134,"semester":20181},{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: \u00dcbung","pnr":52211,"Vnr":55382,"semester":20181}],"SoSe 2018":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: Vorlesung","pnr":52211,"Vnr":19134,"semester":20180},{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: \u00dcbung","pnr":52211,"Vnr":55382,"semester":20180}],"WiSe 2016":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: Vorlesung","pnr":52211,"Vnr":19134,"semester":20161},{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: \u00dcbung","pnr":52211,"Vnr":55382,"semester":20161}],"WiSe 2015":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: Vorlesung","pnr":52211,"Vnr":19134,"semester":20151},{"titel":"3D-Strukturen biologischer Makromolek\u00fcle: \u00dcbung","pnr":52211,"Vnr":55382,"semester":20151}]},"people":{"":[{"vorname":"Stefan","nachname":"Schuster","grad":"Universit\u00e4tsprofessor Dr.\n","friedolinID":913}]},"modulcode":"FMI-BI0001"},
	module: {"data":{"":[{"titel":"3D-Strukturen biologischer Makromolek\u00fcle","nr":"FMI-BI0001","aktiv":1},{"titel":"Algorithmische Phylogenetik","nr":"FMI-BI0002","aktiv":1},{"titel":"Einf\u00fchrung in die Bioinformatik I","nr":"FMI-BI0003","aktiv":1},{"titel":"Einf\u00fchrung in die Bioinformatik II","nr":"FMI-BI0004","aktiv":1},{"titel":"Grundlagen der Systembiologie","nr":"FMI-BI0005","aktiv":1},{"titel":"Mathematische Biologie I","nr":"FMI-BI0006","aktiv":1},{"titel":"Projekt Data Mining und Sequenzanalyse","nr":"FMI-BI0007","aktiv":1},{"titel":"Algorithmische Massenspektrometrie","nr":"FMI-BI0008","aktiv":1},{"titel":"Sequenzanalyse","nr":"FMI-BI0009","aktiv":1},{"titel":"Bioinformatische Methoden in der Genomforschung","nr":"FMI-BI0011","aktiv":1},{"titel":"Analyse der Genexpression","nr":"FMI-BI0012","aktiv":1},{"titel":"Beruf und Karriere f\u00fcr Bioinformatiker","nr":"FMI-BI0013","aktiv":1},{"titel":"Biosystemanalyse","nr":"FMI-BI0014","aktiv":1},{"titel":"Metabolische und regulatorische Netzwerke","nr":"FMI-BI0015","aktiv":1},{"titel":"Elektronische Fachinformationen f\u00fcr Bioinformatiker","nr":"FMI-BI0016","aktiv":1},{"titel":"Logik lebender Systeme","nr":"FMI-BI0017","aktiv":1},{"titel":"Mathematische Biologie II","nr":"FMI-BI0018","aktiv":1},{"titel":"Optimalit\u00e4tsprinzipien in der Evolution","nr":"FMI-BI0019","aktiv":1},{"titel":"Projektmodul","nr":"FMI-BI0020","aktiv":1},{"titel":"Seminar Bioinformatik 1","nr":"FMI-BI0021","aktiv":1}]},"count":20}
}