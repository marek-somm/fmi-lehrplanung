<template>
	<div class="searchbar--container">
		<div class="searchbar-wrapper">
			<input
				class="searchbar"
				ref="searchbar"
				v-model="data.input"
				:placeholder="placeholder"
			/>
			<a class="filter-button" @click="toggleFilter" style="display: none;"
				>{{ data.filterActive ? "&#x02c4;" : "&#x02c5;" }} Filter</a
			>
		</div>
		<div class="filter" :class="{ show: data.filterActive }">
			<select>
				<option value="info">Informatik</option>
				<option value="info">Informatik</option>
			</select>
		</div>
	</div>
</template>

<script>
import { onMounted, reactive, ref, watch } from "vue";
export default {
	props: {
		placeholder: String,
	},
	setup(props, { emit }) {
		const searchbar = new ref(null);
		const data = reactive({
			input: "",
			filterActive: false,
		});

		watch(
			() => data.input,
			() => {
				emit("input", data.input);
			}
		);

		onMounted(() => {
			searchbar.value.focus();
		});

		function toggleFilter() {
			data.filterActive = !data.filterActive;
			emit("toggleFilter", data.filterActive);
		}

		return {
			data,
			searchbar,
			toggleFilter,
		};
	},
};
</script>

<style lang="scss" scoped>
.searchbar--container {
	background: $color2_light;
	width: 100%;

	.searchbar-wrapper {
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: center;
		margin: 1rem;
		
		.searchbar {
			width: 15rem;
			margin: 0 1rem;
			padding: 0.3rem 0.7rem 0.3rem 0.7rem;
			text-align: center;
		}

		.filter-button {
			color: white;
			background: grayscale($color: #00000025);
			padding: 0.3rem;

			&:hover {
				cursor: pointer;
				background: grayscale($color: #00000060);
			}
		}
	}

	.filter {
		height: 0px;
		overflow: hidden;
		transition: all 0.2s ease;
	}

	.show {
		height: $filterHeight;
	}
}
</style>