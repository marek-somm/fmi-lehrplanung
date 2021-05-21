<template>
	<div class="results--container" v-if="veranstaltungen.all">
		<div
			class="semester"
			v-for="(semester, key) in veranstaltungen.all"
			:key="key"
		>
			<h3>{{ key }}</h3>
			<div
				class="item"
				v-for="(elem, index) in semester"
				:key="index"
				@click="getVeranstaltung(elem.vnr, elem.semester)"
			>
				<a class="text"> {{ elem.titel }} [{{ elem.semester }}] </a>
			</div>
		</div>
	</div>
</template>

<script>
import { reactive, watch } from "vue";
import { request } from "@/scripts/request.js";

export default {
	props: {
		input: {
			type: String,
		},
	},
	setup(props) {
		const rq = new request();

		const veranstaltungen = reactive({
			defaultLimit: 20,
			limit: null,
			selected: null,
			all: [],
		});

		watch(
			() => props.input,
			() => {
				veranstaltungen.limit = veranstaltungen.defaultLimit;
				searchVeranstaltung(props.input);
			}
		);

		async function searchVeranstaltung(name) {
			veranstaltungen.all = await rq.searchVeranstaltung(
				name,
				veranstaltungen.limit
					? veranstaltungen.limit
					: veranstaltungen.defaultLimit
			);
		}
		async function getVeranstaltung(vnr, semester) {
			console.log(vnr + ", " + semester);
			veranstaltungen.selected = await rq.getVeranstaltung(vnr, semester);
		}

		return {
			veranstaltungen,
			getVeranstaltung,
		};
	},
};
</script>

<style lang="scss" scoped>
.results--container {
	display: flex;
	flex-direction: column;
	align-items: center;

	.semester {
		width: 40%;

		.item {
			border: 1px black solid;
			transition: all 0.2s ease;
			padding: 2rem;
			margin-bottom: 0.5rem;

			&:hover {
				cursor: pointer;
				background-color: rgb(240, 240, 240);
				transform: scale(1.01);
			}
		}
	}

	.small {
		transition: width 0.5s ease;
		width: calc(100% - 800px) !important;
	}
}
</style>