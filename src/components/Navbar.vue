<template>
	<div id="head-wrapper">
		<div class="head-top">
			<div class="nav-container left">
				<router-link class="identity-link" :to="{ name: 'Home' }">
					<div class="identity"></div>
					<span class="name">Arktur</span>
					<span class="subidentity"
						>Fakultät für Mathematik und Informatik</span
					>
				</router-link>
			</div>
			<div class="nav-container center"></div>
			<div class="nav-container"></div>
		</div>
		<div class="head-bottom">
			<router-link v-if="user" class="link" :to="{ name: 'Veranstaltungen' }"
				>Veranstaltungen</router-link
			>
			<router-link v-if="user" class="link" :to="{ name: 'Module' }"
				>Module</router-link
			>
			<router-link class="link" v-if="!user" :to="{ name: 'Login' }"
				>Login</router-link
			>
			<router-link class="link" v-if="user" :to="{ name: 'Logout' }"
				>Logout</router-link
			>
		</div>
	</div>
	<div id="history-wrapper" v-show="getPathElements()[0] != ''">
		<ul class="history">
			<li class="history-item home">
				<router-link class="link" :to="{name: 'Home' }">
					Startseite
				</router-link>
			</li>
			<li class="history-item" v-for="(item, index) in getPathElements()" :key="index">
				<router-link class="link" :to="{name: item}">
					{{ item }}
				</router-link>
			</li>
		</ul>
	</div>
</template>

<script>
import { useStore } from "vuex";
import { computed } from "vue";
import { useRoute } from "vue-router";

export default {
	setup() {
		const store = useStore();
		const route = useRoute();

		const user = computed(() => store.state.User.user);
		const path = computed(() => route.path);

		function getPathElements() {
			var path = route.path
			var nodes = path.slice(1).split("/")
			nodes = nodes.map(node => node.charAt(0).toUpperCase() + node.slice(1))
			return nodes
		}

		return {
			user,
			path,
			getPathElements
		};
	},
};
</script>

<style lang="scss" scoped>
#head-wrapper {
	background: #008198;
	display: flex;
	flex-direction: column;
	justify-content: center;
	position: sticky;
	top: 0;

	.head-top {
		display: flex;
		flex-direction: row;
		justify-content: center;
		margin: 1rem 10rem 1rem 10rem;

		.nav-container {
			width: 33%;
			height: 3.5rem;
			display: flex;
			align-items: center;
		}

		.left {
			height: max-content;

			.identity-link {
				position: relative;
				width: 19rem;

				&:hover {
					.subidentity,
					.name {
						color: #002350;
					}

					.identity {
						background-image: url("https://arktur.fmi.uni-jena.de/assets/fsu_word_mark_hover.svg");
					}
				}

				.identity {
					height: 3.5rem;
					background: url("https://arktur.fmi.uni-jena.de/assets/fsu_word_mark.png");
					background-repeat: no-repeat;
				}

				.subidentity {
					position: absolute;
					top: 2.4rem;
					left: 4rem;
					font-size: 0.75em;
					font-family: "Roboto", Arial, Geneva, sans-serif;
					letter-spacing: 0.05em;
					line-height: normal;
					color: white;
				}

				.name {
					position: absolute;
					top: 0.2rem;
					left: 10rem;
					font-size: 1.5em;
					font-family: "Roboto", Arial, Geneva, sans-serif;
					font-style: italic;
					letter-spacing: 0.05em;
					line-height: normal;
					color: white;
				}
			}
		}

		.center {
			justify-content: center;
		}
	}

	.head-bottom {
		padding: 1rem 0 0 0;
		margin: 1rem 10rem 1rem 10rem;
		text-align: left;

		.link {
			font-weight: normal;
			color: white;
			padding: 1rem;
			text-decoration: none;
			transition: color 0.2s ease;

			&.router-link-exact-active {
				color: white !important;
			}

			&:hover {
				text-decoration: underline;
			}
		}
	}
}

#history-wrapper {
	background: #174488;
	color: white;
	line-height: 3.125rem;

	.history {
		margin: 0 11rem 0 11rem;
		padding: 0rem;
		text-align: left;
		list-style: none;
		display: flex;
		flex-direction: row;

		.history-item {
			padding: 0 1rem 0 1rem;
			width: max-content;
			background: url('https://arktur.fmi.uni-jena.de/assets/history_default.svg') no-repeat left center;

			.link {
				font-size: .9em;
				opacity: .7;
				display: block;
				color: white;
				text-decoration: none;
				width: max-content;

				&:hover {
					text-decoration: underline;
				}
			}
		}

		.home {
			background: url('https://arktur.fmi.uni-jena.de/assets/history_home.svg') no-repeat left center;
		}
	}
}

.router-link-exact-active.link {
	background: #174488;
}
</style>
