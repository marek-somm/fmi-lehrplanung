<template>
	<div id="nav">
		<div class="top">
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
		<div class="bottom">
			<router-link class="link" :to="{ name: 'Home' }">Home</router-link>
			<router-link class="link" :to="{ name: 'ServerTest' }"
				>Server-Test</router-link
			>
			<router-link class="link" :to="{ name: 'Login' }">Login</router-link>
		</div>
	</div>
</template>

<script>
export default {
	setup() {
		return {};
	},
};
</script>

<style lang="scss" scoped>
#nav {
	padding: 1rem;
	padding-left: 10rem;
	background: #008198;
	margin-bottom: 3rem;
	display: flex;
	flex-direction: column;
	justify-content: center;

	.top {
		display: flex;
		flex-direction: row;
		justify-content: center;

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
						background-image: url("https://www.fmi.uni-jena.de/skin/mirz/_images/fsu_word_mark_hover.svg");
					}
				}

				.identity {
					height: 3.5rem;
					background: url("https://www.fmi.uni-jena.de/pub/theme/vorlage_fe/skin/_images/fsu_word_mark.png");
					background-repeat: no-repeat;
				}

				.subidentity {
					position: absolute;
					top: 2.4rem;
					left: 4.0rem;
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

	.bottom {
		padding: 3rem 0 0 0;
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
</style>
