<template>
	<div class="results--container" v-if="data.all" :class="{ filter: filterActive }">
		<div class="category" v-for="(category, key) in data.all.data" :key="key">
			<h3>&nbsp;{{ key }}&nbsp;</h3>
			<div
				class="item"
				:class="{ deactive: !item.aktiv }"
				v-for="(item, index) in category"
				:key="index"
				@click="select(item.nr, item.semester)"
			>
				<a class="text"> {{ item.titel }} [{{ item.nr }}] </a>
			</div>
		</div>
		<button
			class="load-more"
			@click="loadMore"
			v-show="data.all.count == data.limit"
		>
			...
		</button>
	</div>
</template>

<script>
export default {
	props: {
		data: Object,
		filterActive: Boolean
	},
	setup(props, { emit }) {
		function loadMore() {
			emit('loadMore')
		}

		function select(nr, semester) {
			emit('select', nr, semester)
		}

		return {
			loadMore,
			select
		};
	},
};
</script>

<style lang="scss" scoped>
.filter {
	height: calc(100vh - 17.675rem - #{$filterHeight}) !important;
}
.results--container {
	display: flex;
	flex-flow: column;
	flex-grow: 1;
	align-items: center;
	overflow-y: auto;
	height: calc(100vh - 17.675rem);
	padding: 0 0 1rem 0;
	transition: height .2s ease;

	.category {
		width: 40%;

		.item {
			border: 1px black solid;
			transition: all 0.1s ease;
			padding: 2rem;
			margin-bottom: 0.5rem;

			&:hover {
				cursor: pointer;
				background-color: rgb(240, 240, 240);
				transform: scale(1.01);
			}
		}

		.deactive {
			color: #7f868f;
			background-color: rgb(240, 240, 240);

			&:hover {
				background-color: rgb(230, 230, 230);
			}
		}
	}

	.load-more {
		width: 35%;
		margin: 1rem 0 1rem 0;
		padding: 0.5rem;
		border: 1px black solid;
		transition: background 0.2s ease, transform 0.2s ease;
		font-size: 1.5rem;
		background: white;
		color: #2c3e50;

		&:hover {
			cursor: pointer;
			background-color: rgb(240, 240, 240);
			transform: scale(1.01);
		}
	}

	.small {
		transition: width 0.5s ease;
		width: calc(100% - 800px) !important;
	}
}
</style>