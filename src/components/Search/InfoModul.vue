<template>
	<div class="info--container">
		<div class="header">
			<h3 class="title" v-if="selected">{{ selected.modulcode }}</h3>
			<button class="close-button" @click="close">X</button>
		</div>
		<div class="info-content" v-if="selected">
			<div class="block">
				<br>
				<h4 class="title">Titel DE: {{ selected.data.titel_de }}</h4>
				<h4 class="title">Titel EN: {{ selected.data.titel_en }}</h4>
			</div>
			<div class="block">
				<p>ECTS: {{ selected.data.lp }} LP</p>
				<p>Präsenzzeit: {{ selected.data.praesenzzeit }}</p>
				<p>Workload: {{ selected.data.workload }}</p>
			</div>
			<div class="block">
				<p>Turnus: {{ selected.data.turnus }}</p>
				<p>Zusammensetzung: {{ selected.data.zusammensetzung }}</p>
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
		</div>
	</div>
</template>

<script>
import { onMounted } from "vue";
export default {
	props: {
		selected: {
			type: Object,
		},
	},
	setup(props, { emit }) {
		function convertSemester(code) {
			if (code % 10 == 0) {
				return "SoSe " + parseInt(code / 10);
			} else {
				return "WiSe " + parseInt(code / 10);
			}
		}

		onMounted(() => {
			window.addEventListener("keyup", function (event) {
				if (event.key === "Escape") {
					close();
				}
			});
		});

		function close() {
			emit("close");
		}

		return {
			convertSemester,
			close,
		};
	},
};
</script>

<style lang="scss" scoped>
.info--container {
	height: calc(100vh - 13.51rem);
	width: 0;
	text-align: left;
	transition: all 0.5s ease;
	color: transparent;
	overflow: hidden;

	display: flex;
	flex-direction: column;

	.info-content {
		overflow: auto;
		padding: 0 0.7rem 0rem 0.7rem;
		border-right: 1px gray solid;
		background: #eee;
		flex-grow: 1;

		.block {
			margin-bottom: 1.5rem;
		}
	}

	.header {
		display: flex;
		justify-content: flex-end;
		background-color: lightgray;
		border-right: 1px gray solid;
		border-bottom: 1px gray solid;

		.title {
			padding: 0 0.7rem 0 0.7rem;
			text-align: center;
			margin: 1rem auto 1rem auto;
		}

		.close-button {
			height: 2rem;
			width: 2rem;
			border: none;
			background-color: rgb(255, 220, 220);

			&:hover {
				cursor: pointer;
				background-color: rgb(255, 100, 100);
			}
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

.show {
	color: #2c3e50;
	width: 35%;
	overflow-y: auto;
}
</style>