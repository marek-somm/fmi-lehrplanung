import axios from 'axios'

export class request {
	constructor() {
		this.loading = false
		this.error = null
		this.data = null
	}

	async getVeranstaltung(vnr, semester) {
		await this.fetchData('search/', {
			typ: 'v',
			vnr: vnr,
			semester: semester,
		})
		return this.data
	}

	async searchVeranstaltung(titel, limit) {
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
		await this.fetchData('search/', {
			typ: 'm',
			modulcode: modulcode,
		})
		return this.data
	}

	async searchModul(titel, limit) {
		titel = titel.trim()
		await this.fetchData('search/', {
			typ: 'm',
			titel: titel,
			limit: limit,
		})
		if(titel == '') return null
		else return this.data
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
}
