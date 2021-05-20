import axios from 'axios'

export class request {
	constructor() {
		this.loading = false
		this.error = null
		this.data = null
	}

	async getModul(modulcode) {
		await this.fetchData('search/', {
			modulcode: modulcode,
		})
		return this.data
	}

	async searchModul(name) {
		if (!name) name = ' '
		await this.fetchData('search/', {
			titel_de: name,
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
