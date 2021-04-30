<template>
	<div>
		<h1>This is Home</h1>
		<div id="boxes">
			<Box v-for="index in 3" :key="index" :number="index"/>
			{{ data.data }}
		</div>
	</div>
</template>

<script>
import { reactive } from 'vue';
import Box from "../components/Box.vue";

export default {
	components: { Box },
	setup() {
		const data = reactive({
		});
		return {
			data
		};
	},
};
</script>

<style lang="scss" scoped>
#boxes {
	display: flex;
	justify-content: center;
}
</style>