<template>
	<div class="results--container" v-if="veranstaltungen.all">
		<div
			class="semester"
			v-for="(semester, key) in veranstaltungen.all.data"
			:key="key"
		>
			<h3>{{ key }}</h3>
			<div
				class="item"
				:class="{ deactive: !elem.aktiv }"
				v-for="(elem, index) in semester"
				:key="index"
				@click="getVeranstaltung(elem.vnr, elem.semester)"
			>
				<a class="text"> {{ elem.titel }} [{{ elem.vnr }}] </a>
			</div>
		</div>
		<button
			class="load-more"
			@click="loadMore"
			v-show="veranstaltungen.all.count == veranstaltungen.limit"
		>
			...
		</button>
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
	setup(props, { emit }) {
		const rq = new request();

		const veranstaltungen = reactive({
			defaultLimit: 20,
			limit: 20,
			all: null,
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
			);
		}

		async function getVeranstaltung(vnr, semester) {
			emit("selected", await rq.getVeranstaltung(vnr, semester));
		}

		function loadMore() {
			veranstaltungen.limit += veranstaltungen.defaultLimit;
			searchVeranstaltung(props.input);
		}

		return {
			veranstaltungen,
			getVeranstaltung,
			loadMore,
		};
	},
};
</script>

<style lang="scss" scoped>
.results--container {
	display: flex;
	flex-flow: column;
	flex-grow: 1;
	align-items: center;
	overflow-y: auto;
	height: calc(100vh - 14.55rem);
	padding: 0 0 1rem 0;

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
		transition: cursor, background, transform 0.2s ease;
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