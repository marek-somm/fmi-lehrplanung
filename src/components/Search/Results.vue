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
				:class="{ deactive: !elem.aktiv }"
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
	setup(props, { emit }) {
		const rq = new request();

		const veranstaltungen = reactive({
			limit: null,
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
			);
		}

		async function getVeranstaltung(vnr, semester) {
			emit('selected', await rq.getVeranstaltung(vnr, semester))
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
	flex-flow: column;
	align-items: center;
	overflow-y: auto;
	height: calc(100vh - 14.55rem);

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

	.small {
		transition: width 0.5s ease;
		width: calc(100% - 800px) !important;
	}
}
</style>