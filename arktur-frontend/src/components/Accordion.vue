<template>
	<div class="accordion-container">
		<div v-for="(time, key, indexT) in content.data" :key="indexT" class="section">
			<div v-for="(semester, index) in helper.sortObj(time)" :key="index + getIndex(indexT)">
				<button class="accordion" :class="{ active: content.selected == index + getIndex(indexT) }" @click="
					content.selected =
					content.selected == index + getIndex(indexT)
						? -1
						: index + getIndex(indexT)
					">
					{{ indexT == 1 ? "Dieses Semester" : helper.convertSemester(semester[0]) }}
				</button>
				<div class="panel" :style="{
					maxHeight:
						9 *
						semester[1].length *
						(content.selected == index + getIndex(indexT)) +
						'rem',
				}">
					<div class="content" :class="{ deactive: !value.active }" v-for="(value, index) in semester[1]" :key="index"
						@click="click(value)">
						<h3>{{ value.title }} ({{ value.vnr ? value.vnr : "nicht vorhanden" }})</h3>
						<p>
							<b>[{{ value.type }}]</b>
							{{ convertTurnus(value.rotation) }}
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import helper from "@/services/HelperService.js";
import { useRouter } from "vue-router";

export default {
	emits: ["select"],
	props: {
		content: Object,
	},
	setup(props, { emit }) {
		const router = useRouter();

		function convertTurnus(value) {
			return helper.convertTurnus(value);
		}

		function click(event) {
			if (
				event["semester_org"] &&
				event["semester_org"] != event["semester"]
			) {

				router.push({
					name: 'Neu',
					query: {
						ref: event["id"],
						sem: event["semester"],
					},
				});
			} else {
				emit("select", event.id);
			}
		}

		function getIndex(current) {
			let result = 0;
			for (let i = 0; i < current; i++) {
				result += Object.keys(
					props.content.data[Object.keys(props.content.data)[i]]
				).length;
			}
			return result;
		}

		return {
			helper,
			convertTurnus,
			click,
			getIndex,
		};
	},
};
</script>

<style lang="scss" scoped>
.accordion-container {
	.section {
		margin: 1.5rem 0;

		.accordion {
			background-color: #1d60bd;
			color: white;
			cursor: pointer;
			padding: 18px;
			width: 100%;
			text-align: left;
			border: none;
			outline: none;
			transition: 0.4s;
			font-size: 1rem;
			font-weight: bold;

			&:hover {
				background-color: #174488;
			}
		}

		.active {
			background-color: #174488;
		}

		.panel {
			padding: 0 18px;
			background-color: white;
			max-height: 0;
			overflow: auto;
			transition: max-height 0.3s ease-out;

			.content {
				border: 1px solid rgb(182, 182, 182);
				margin: 0.5rem;
				transition: background 0.1s ease;

				&:hover {
					cursor: pointer;
					background-color: rgb(245, 245, 245);
				}
			}

			.deactive {
				background: #d1d1d1;
				font-style: italic;
				color: rgb(100, 100, 100);

				&:hover {
					background-color: rgb(230, 230, 230);
				}
			}
		}
	}
}
</style>