import axios from 'axios'

export class request {
	constructor() {
		this.loading = false
		this.error = null
		this.data = null
	}

	async getVeranstaltung(vnr, semester) {
		await this.fetchData('search/', {
			vnr: vnr,
			semester: semester
		})
		return this.data
	}

	async searchVeranstaltung(titel, limit) {
		await this.fetchData('search/', {
			titel: titel,
			limit: limit
		})
		return this.data
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
