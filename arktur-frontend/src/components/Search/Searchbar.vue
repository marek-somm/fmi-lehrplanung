<template>
	<div class="searchbar--container">
		<div class="searchbar-wrapper">
			<a
				v-if="data.route.name == 'Veranstaltungen'"
				class="filter-button"
				@click="createNewEvent"
				>Neu</a
			>
			<input
				class="searchbar"
				ref="searchbar"
				v-model="data.input"
				@input="updateModel"
				:placeholder="placeholder"
			/>
			<a
				class="filter-button"
				@click="toggleFilter"
				:class="{ 'filter-active': data.filterActive }"
				>Filter</a
			>
		</div>
		<div class="filter" :class="{ show: data.filterActive }">
			<div class="filter-item" v-if="false">
				<label class="label">Studiengang: </label>
				<select v-model="filter.study">
					<option :value="null">Alle</option>
					<option value="info">Informatik</option>
					<option value="mathe">Mathematik</option>
					<option value="bioinfo">Bioinformatik</option>
				</select>
			</div>
			<div class="filter-item">
				<label class="label">Zeige Inaktive: </label>
				<input type="checkbox" v-model="filter.inactive">
			</div>
		</div>
	</div>
</template>

<script>
import { onMounted, reactive, ref, watchEffect } from "vue";
import { useRouter } from "vue-router";
export default {
	props: {
		placeholder: String,
	},
	setup(props, { emit }) {
		const router = useRouter();
		const searchbar = new ref(null);
		const data = reactive({
			input: "",
			filterActive: false,
			route: router.currentRoute,
		});
		const filter = reactive({
			study: null,
			inactive: false,
		});

		watchEffect(() => {
			console.log("change")
			emit("changeFilter", filter)
		})

		onMounted(() => {
			searchbar.value.focus();
		});

		function updateModel() {
			emit("update", data.input);
		}

		function toggleFilter() {
			data.filterActive = !data.filterActive;
			emit("toggleFilter", data.filterActive);
		}

		function createNewEvent() {
			router.push({
				name: "Neu",
				params: {},
			});
		}


		return {
			data,
			filter,
			searchbar,
			toggleFilter,
			createNewEvent,
			updateModel,
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
			background-color: grayscale($color: #00000025);
			padding: 0.3rem;

			&.filter-active {
				background-color: grayscale($color: #00000055);
			}

			&:hover {
				cursor: pointer;
				background-color: grayscale($color: #00000060);
			}
		}
	}

	.filter {
		display: flex;
		justify-content: flex-start;
		align-items: flex-start;
		flex-direction: column;

		height: 0px;
		overflow: hidden;
		transition: all 0.2s ease;

		width: max-content;
		margin: auto;

		.filter-item {
			height: max-content;
			margin: 0.5rem 0;

			.label {
				color: white;
			}
		}
	}

	.show {
		height: $filterHeight;
	}
}
</style>