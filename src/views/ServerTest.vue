<template>
	<div class="server-test--container">
		<div class="info" :class="{ show: data.veranstaltungen.selected }">
			<h2 class="titel">Modulinfo</h2>
			<div
				class="modul"
				v-for="veranstaltung in data.veranstaltungen.selected"
				:key="veranstaltung.vnr"
			>
				<router-link
					class="link"
					:to="{
						name: 'Instanziieren',
						params: { id: veranstaltung.vnr },
					}"
					>Instanziieren</router-link
				>
				<div
					class="modul-item"
					v-for="(item, key, index) in veranstaltung"
					:key="index"
				>
					<b>{{ key }}</b> : {{ item }}
				</div>
			</div>
		</div>
		<div class="search" :class="{ small: data.veranstaltungen.selected }">
			<input
				class="searchbar"
				v-model="data.input"
				placeholder="Modul Title"
			/>
			<h3>Ergebnisse</h3>
			<div class="result" v-if="data.veranstaltungen.all">
				<div class="item" v-for="(elem, index) in data.veranstaltungen.all" :key="index">
					<a class="modul" @click="getVeranstaltung(elem.vnr)">
						{{ elem.titel }} [{{ elem.semester }}]
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { onMounted, reactive, watch } from "vue";
import { request } from "@/scripts/request.js";
export default {
	setup() {
		const rq = new request();
		const data = reactive({
			input: "",
			veranstaltungen: {
				limit: 100,
				selected: null,
				all: null
			}
		});

		watch(
			() => data.input,
			() => {
				data.limit = 100;
				searchVeranstaltung(data.input);
			}
		);
		onMounted(() => {
			searchVeranstaltung(data.input);
		});

		async function searchVeranstaltung(name) {
			data.veranstaltungen.all = await rq.searchVeranstaltung(name, data.veranstaltungen.limit);
		}
		async function getVeranstaltung(modulcode) {
			data.modul = await rq.getVeranstaltung(modulcode);
		}

		return {
			data,
			getVeranstaltung,
		};
	},
};
</script>

<style lang="scss" scoped>
.server-test--container {
	display: flex;
	flex-direction: row;
	padding-bottom: 0.5rem;

	.search {
		display: flex;
		flex-direction: column;
		align-items: center;
		width: 100%;

		.searchbar {
			width: 15rem;
			margin-bottom: 1rem;
		}

		.result {
			display: flex;
			flex-direction: column;
			align-items: center;

			.modul {
				transition: all 0.2s ease;
				width: max-content;
				margin-bottom: 1rem;

				&:hover {
					cursor: pointer;
					text-shadow: 1px 1px rgba(0, 0, 0, 0.4);
					transform: scale(1.05);
				}
			}
		}
	}

	.info {
		color: transparent;
		width: 0;
		text-align: left;
		padding: 0 0.7rem 1rem 0.7rem;

		.modul {
			.modul-item {
				margin: 0.4rem 0 0.4rem 0;
			}
		}
	}

	.show {
		transition: all 0.5s ease;
		color: #2c3e50;
		border: 1px black solid;
		width: 500px;
	}

	.small {
		transition: width 0.5s ease;
		width: calc(100% - 800px) !important;
	}
}
</style>