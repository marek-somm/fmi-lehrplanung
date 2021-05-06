<template>
	<div class="server-test--container">
		<input class="search" v-model="data.input" placeholder="Modul Title" />
		<h3>Ergebnisse</h3>
		<div class="results" v-if="data.data">
			<span v-for="(elem, index) in data.data" :key="index"
				>{{ elem.name }} [{{ elem.type }}] ({{ elem.lp }}LP,
				{{ elem.sws }}SWS)</span
			>
		</div>
		<span v-show="data.error">{{ data.error }}</span>
		<div class="break"></div>
	</div>
</template>

<script>
import { onMounted, reactive, watch } from "vue";
import axios from "axios";
export default {
	setup() {
		const data = reactive({
			input: "",
			data: null,
			loading: false,
			error: null,
		});

		watch(
			() => data.input,
			() => {
				fetchData(data.input);
			}
		);

		onMounted(() => {
			fetchData(data.input);
		});

		async function fetchData(name) {
			data.loading = true;
			data.error = null;

			if (!name) {
				name = " ";
			}
			await axios
				.get("https://arktur.fmi.uni-jena.de/api/search/", {
					headers: { "Content-Type": "application/json" },
					params: {
						name: name,
					},
				})
				.then((res) => {
					data.data = res.data;
					data.loading = false;
				})
				.catch((err) => {
					data.error = err;
					data.loading = false;
				});
		}

		return {
			data,
		};
	},
};
</script>

<style lang="scss" scoped>
.server-test--container {
	display: flex;
	flex-direction: column;
	align-items: center;

	* {
		margin-bottom: 1rem;
	}

	.search {
		width: 15rem;
	}

	.results {
		display: flex;
		flex-direction: column;
	}

	.break {
		margin: 3rem;
	}
}
</style>