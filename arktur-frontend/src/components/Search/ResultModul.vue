<template>
	<div
		class="results--container"
		v-if="data.all && !data.all.data.errors"
		:class="{ filter: filterActive }"
		@scroll="scroll"
	>
		<div class="spacer"></div>
		<div
			class="category"
		>
			<div
				class="item"
				:class="{ deactive: !item.active }"
				v-for="(item, index) in data.all.data"
				:key="index"
				@click="select(item.modulecode)"
			>
				<a class="text"> {{ item.title_de }} ({{ item.modulecode }}) </a>
			</div>
		</div>
		<button
			class="load-more"
			@click="loadMore"
			v-show="data.count == data.limit"
		>
			...
		</button>
	</div>
</template>

<script>
import { debounce } from "debounce";

export default {
	props: {
		data: Object,
		filterActive: Boolean,
	},
	setup(props, { emit }) {
		function loadMore() {
			emit("loadMore");
		}

		function select(modulecode) {
			emit("select", modulecode);
		}

		function scroll(data) {
			let scrollTop = data.target.scrollTop;
			let scrollTopMax = data.target.scrollTopMax;
			if (scrollTopMax - (scrollTopMax / props.data.count) * 5 < scrollTop) {
				loadMore();
			}
		}

		return {
			loadMore,
			select,
			scroll: debounce(scroll, 400),
		};
	},
};
</script>

<style lang="scss" scoped>
@import "result.scss";
</style>