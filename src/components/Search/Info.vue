<template>
	<div class="info--container">
		<div class="info-content" v-if="selected">
			<h3>{{ selected.data.titel }}</h3>
			<div class="block">
				<p class="attrib">Vnr: {{ selected.data.veranstaltungsnummer }}</p>
				<p class="attrib">Friedolin ID: {{ selected.data.friedolinID }}</p>
				<p class="attrib">
					Semester: {{ convertSemester(selected.data.semester) }}
				</p>
				<p class="attrib">Turnus: {{ selected.data.turnus }}</p>
				<p class="attrib">
					Aktiv: {{ selected.data.aktiv ? "Ja" : "Nein" }}
				</p>
			</div>
			<div class="block">
				<p class="attrib">Art: {{ selected.data.art }}</p>
				<p class="attrib">SWS: {{ selected.data.sws }}</p>
			</div>
			<div class="block">
				<div
					class="attrib-container"
					v-for="(value, key) in selected.content"
					:key="key"
				>
					<p class="attrib">{{ key }}:</p>
					<div class="box" v-html="value" v-if="value"></div>
				</div>
			</div>
			<br />
			<h3><u>Prüfungen:</u></h3>
			<div class="block">
				<div class="box" v-for="exam in selected.exams" :key="exam.pnr">
					<p>
						<b>{{ exam.titel }}</b>
					</p>
					<br />
					<p>Pnr: {{ exam.pnr }}</p>
					<p>Modulcode: {{ exam.modulcode }}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		selected: {
			type: Object,
		},
	},
	setup() {
		function convertSemester(code) {
			if (code % 10 == 0) {
				return "SoSe " + parseInt(code / 10);
			} else {
				return "WiSe " + parseInt(code / 10);
			}
		}

		return {
			convertSemester,
		};
	},
};
</script>

<style lang="scss" scoped>
.info--container {
	position: sticky;
	top: 0;
	height: calc(100vh - 14.55rem);
	overflow-y: auto;

	.info-content {
		.block {
			margin-bottom: 1.5rem;
		}
	}
}

p {
	margin: 0;
	margin: 0.2rem 0 0.2rem 0;
}

.box {
	border: 1px gray solid;
	padding: 0.5rem;
	margin-bottom: 0.5rem;
}
</style>