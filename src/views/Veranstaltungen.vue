<template>
	<div class="veranstaltungen--container">
		<Searchbar @input="updateInput" placeholder="Veranstaltungstitel" />
		<div class="veranstaltungen-content">
			<Info
				:selected="data.selected"
				:class="{ show: data.show }"
				@close="close"
			/>
			<Results @loadMore="loadMore" @select="updateSelected" :data="veranstaltungen"/>
		</div>
	</div>
</template>

<script>
import Searchbar from "@/components/Search/Searchbar.vue";
import Results from "@/components/Search/Results.vue";
import Info from "@/components/Search/InfoVeranstaltung.vue";
import { reactive } from "vue";
import { request } from "@/scripts/request.js";

export default {
	components: {
		Searchbar,
		Results,
		Info,
	},
	setup() {
		const rq = new request();

		const veranstaltungen = reactive({
			defaultLimit: 20,
			limit: 20,
			all: null
		})

		const data = reactive({
			input: "",
			show: false,
			selected: null,
		});

		async function updateSelected(value, semester) {
			console.log(value + ", " + semester)
			data.selected = await rq.getVeranstaltung(value, semester);
			data.show = true;
		}

		function close() {
			data.show = false;
		}

		function loadMore() {
			veranstaltungen.limit += veranstaltungen.defaultLimit;
			searchVeranstaltung(data.input);
		}

		function updateInput(input) {
			data.input = input;
			veranstaltungen.limit = veranstaltungen.defaultLimit;
			searchVeranstaltung(data.input);
		}

		async function searchVeranstaltung(name) {
			veranstaltungen.all = await rq.searchVeranstaltung(
				name,
				veranstaltungen.limit
			)
		}

		return {
			data,
			updateInput,
			updateSelected,
			close,
			veranstaltungen,
			loadMore
		};
	},
};
</script>

<style lang="scss" scoped>
.veranstaltungen--container {
	display: flex;
	flex-direction: column;
	//padding-bottom: 0.5rem;
	justify-content: center;

	.veranstaltungen-content {
		display: flex;
		flex-direction: row;
	}
}
</style>