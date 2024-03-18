<template>
	<div class="settings-container">
		<button @click="prevSemester">Vorheriges Semester</button>
		{{ helper.convertSemester(helper.getCurrentSemester()) }}
		<button @click="nextSemester()">Nächstes Semester</button>
	</div>
</template>

<script setup>
import helper from "@/services/HelperService.js";
import update from "@/services/UpdateService.js";
import search from "@/services/SearchService.js";
import { useStore } from "vuex";

const store = useStore();

async function nextSemester() {
	let value = confirm("Sind sie sicher, dass sie das Semester wechseln möchten? Dieser Schritt kann nicht rückgängig gemacht werden!");
	if (value == true) {
		let semester = helper.addTurnus(store.state.currentSemester, 1);
		let response = await update.setting("semester", semester);
		store.dispatch('setCurrentSemester', response.data.semester);
	}
}

async function prevSemester() {
	let value = confirm("Sind sie sicher, dass sie das Semester wechseln möchten? Dieser Schritt kann nicht rückgängig gemacht werden!");
	if (value == true) {
		let semester = helper.addTurnus(store.state.currentSemester, -1);
		console.log(semester);
		let response = await update.setting("semester", semester);
		store.dispatch('setCurrentSemester', response.data.semester);
	}
}
</script>

<style lang="scss" scoped>
.settings-container {
	padding-top: 5rem;

	button {
		// color: white;
		// background-color: $color2_dark;
		padding: 0.5rem;
		margin: 0rem 1rem;
		border-radius: 0;
		border: 1px solid gray;

		&:hover {
			cursor: pointer;
			// background-color: $color2_dark;
		}
	}
}
</style>