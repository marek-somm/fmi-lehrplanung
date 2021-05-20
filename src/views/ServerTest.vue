<template>
	<div class="server-test--container">
		<div class="info" :class="{show: data.modul}">
			<h2 class="titel">Modulinfo</h2>
			<div class="modul" v-for="modul in data.modul" :key="modul.modulcode">
				<div class="modul-item" v-for="(item, key, index) in modul" :key="index">
					<b>{{ key }}</b> : {{ item }}
				</div>
			</div>
		</div>
		<div class="search" :class="{small: data.modul}">
			<input
				class="searchbar"
				v-model="data.input"
				placeholder="Modul Title"
			/>
			<h3>Ergebnisse</h3>
			<div class="result" v-if="data.data">
				<a
					class="modul"
					v-for="(elem, index) in data.data"
					:key="index"
					@click="getModul(elem.modulcode)"
				>
					{{ elem.titel_de }} [{{ elem.modulcode }}]
				</a>
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
			data: null,
			loading: false,
			modul: null,
		});

		watch(
			() => data.input,
			() => {
				searchModul(data.input);
			}
		);
		onMounted(() => {
			searchModul(data.input);
		});

		async function searchModul(name) {
			data.data = await rq.searchModul(name);
		}
		async function getModul(modulcode) {
			data.modul = await rq.getModul(modulcode);
		}

		return {
			data,
			getModul,
		};
	},
};
</script>

<style lang="scss" scoped>
.server-test--container {
	display: flex;
	flex-direction: row;
	padding-bottom: .5rem;

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
		padding: 0 .7rem 1rem .7rem;

		.modul {
			.modul-item {
				margin: .4rem 0 .4rem 0;
			}
		}
	}

	.show {
		transition: all .5s ease;
		color: #2c3e50;
		border: 1px black solid;
		width: 500px;
	}

	.small {
		transition: width .5s ease;
		width: calc(100% - 800px) !important;
	}
}
</style>