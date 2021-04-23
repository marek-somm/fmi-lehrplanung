<template>
	<div>
		{{ data.loading }} <br />
		{{ data.data }} <br>
		{{ data.error }}
	</div>
</template>

<script>
import { onMounted, reactive } from "vue";
import axios from "axios";
export default {
	setup() {
		const data = reactive({
			data: null,
			loading: true,
			error: null
		})

		function fetchData() {
			data.loading = true;

			axios
				.get("http://localhost", {
					headers: {"Content-Type": "application/json"}
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

		onMounted(() => {
			fetchData();
		});

		return {
			data,
		};
	},
};
</script>

<style lang="scss" scoped>
</style>