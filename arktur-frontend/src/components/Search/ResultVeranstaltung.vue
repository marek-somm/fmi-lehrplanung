<template>
	<div
		class="results--container"
		v-if="data.all && !data.all.data.errors"
		:class="{ filter: filterActive }"
		@scroll="scroll"
	>
		<div
			class="category"
			v-for="(category, semester) in data.all.data"
			:key="semester"
		>
			<h3>&nbsp;{{ semester }}&nbsp;</h3>
			<div
				class="item"
				:class="{ deactive: !item.active }"
				v-for="(item, index) in category"
				:key="index"
				@click="select(item.vnr, item.semester)"
			>
				<a class="text"> {{ item.title }} ({{ item.vnr }}) </a>
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

		function select(nr, semester) {
			emit("select", nr, semester);
		}

		function scroll(data) {
			console.log('scroll')
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