<template>
	<i
		class="fa fa-cog settings"
		aria-hidden="true"
		@click="data.modalOpen = true"
	></i>
	<teleport to="#app">
		<div class="modal-wrapper" :class="{ hidden: !data.modalOpen }">
			<div class="background" @click="data.modalOpen = false"></div>
			<div class="modal">
				<div class="head-wrapper">
					<div class="title">Einstellungen</div>
					<div class="level" v-if="level > 0">
						Rolle:&nbsp;{{ level == 1 ? "Lehre" : "Prüfungsamt" }}
					</div>
				</div>
				<div class="body-wrapper">
					<div class="list-item">
						<button
							class="name clickable"
							@click="toggle('fachrichtung')"
						>
							Fachrichtungen
							{{
								settings.fachrichtung.expanded ? "&#x02c4;" : "&#x02c5;"
							}}
						</button>
						<div
							class="options"
							:class="{ hidden: !settings.fachrichtung.expanded }"
						>
							<div class="option-item">
								<input
									type="checkbox"
									name="mathe"
									v-model="settings.fachrichtung.mathe"
								/>
								<label for="mathe">Mathematik</label><br />
							</div>
							<div class="option-item">
								<input
									type="checkbox"
									name="info"
									v-model="settings.fachrichtung.info"
								/>
								<label for="info">Informatik</label><br />
							</div>
							<div class="option-item">
								<input
									type="checkbox"
									name="bioinfo"
									v-model="settings.fachrichtung.bioinfo"
								/>
								<label for="bioinfo">Bioinformatik</label><br />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</teleport>
</template>

<script>
import { reactive, computed } from "@vue/reactivity";
import { useStore } from "vuex";

export default {
	setup() {
		const store = useStore();
		const data = reactive({
			modalOpen: false,
		});

		const settings = reactive({
			fachrichtung: {
				expanded: false,
				mathe: true,
				info: true,
				bioinfo: true,
			},
		});

		const level = computed(() => store.state.User.level);

		function toggle(list) {
			this.settings[list].expanded = !this.settings[list].expanded;
		}

		return {
			data,
			settings,
			level,
			toggle,
		};
	},
};
</script>

<style lang="scss" scoped>
.hidden {
	display: none !important;
}

.settings {
	&:hover {
		cursor: pointer;
		color: darken($font_color, 10%);
	}
}

.modal-wrapper {
	display: inherit;

	.background {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		background-color: rgba(0, 0, 0, 0.5);
	}
	.modal {
		$width: 20rem;
		$height: 30rem;
		position: absolute;
		left: calc(50% - #{$width}/ 2);
		top: calc(50% - #{$height}/ 2);
		width: $width;
		height: $height;
		background-color: white;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;

		.head-wrapper {
			display: flex;
			flex-direction: row;
			justify-content: space-between;
			width: 100%;
			font-size: 1.2rem;
			background: $color1;
			color: $font_color;

			.title {
				padding: 1rem;
				width: max-content;
				font-weight: bold;
			}

			.level {
				padding: 1rem;
				width: max-content;
			}
		}

		.body-wrapper {
			display: flex;
			flex-direction: row;
			width: 100%;
			flex-grow: 1;

			.list-item {
				margin: 1rem;
				height: max-content;

				.name {
					text-align: left;
					margin-bottom: 0.4rem;
					padding: 0.4rem;
					border: none;
					background: rgb(240, 240, 240);
					font: inherit;
					color: inherit;
				}

				.options {
					text-align: left;
					margin-left: 0.7rem;
					display: flex;
					flex-direction: column;

					.option-item {
						margin: 0.1rem 0;
					}
				}
			}

			.clickable {
				&:hover {
					cursor: pointer;
				}
			}
		}
	}
}
</style>