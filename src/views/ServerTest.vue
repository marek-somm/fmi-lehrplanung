<template>
	<div class="server-test--container">
		<input class="search" v-model="data.input" />
		<span>Loading: {{ data.loading }}</span>
		<span v-show="data.data">{{ data.data }}</span>
		<span v-show="data.error">{{ data.error }}</span>
	</div>
</template>

<script>
import { reactive, watch } from "vue";
import axios from "axios";
export default {
	setup() {
		const data = reactive({
			input: "",
			data: null,
			loading: false,
			error: null,
		});

		watch(() => data.input, () => {
			fetchData(data.input);
		})

		function fetchData(name) {
			data.loading = true;

			axios
				.get("http://localhost:8000/api/data/test", {
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
}
</style>