<template>
	<div class="search-panel-container">
		<input
			@click="show(true)"
			@input="move(0)"
			@keyup.esc="show(false)"
			@keyup.down="move(1)"
			@keyup.up="move(-1)"
			@keypress.enter="setValue(getSuggestions()[data.hover])"
			class="input"
			:placeholder="placeholder"
			v-model="data.value"
			ref="input"
		/>
		<div class="suggestions" v-show="data.focus" v-click-away="onClickAway">
			<div>
				<li
					class="suggestion"
					v-for="(suggestion, index) in getSuggestions()"
					:key="index"
					@click="setValue(suggestion)"
					:class="{ hover: index == data.hover }"
				>
					{{ suggestion }}
				</li>
			</div>
		</div>
	</div>
</template>

<script>
import { reactive, ref } from "@vue/reactivity";
import { mixin as VueClickAway } from "vue3-click-away";

export default {
	mixins: [VueClickAway],
	props: {
		modelValue: String,
		placeholder: String,
		suggestions: {
			type: Array,
			default: function () {
				return [];
			},
		},
	},
	setup(props, { emit }) {
		const input = ref(null);
		const data = reactive({
			value: "",
			focus: false,
			open: false,
			selected: props.modelValue,
			hover: -1,
		});

		function show(state) {
			if(state != data.focus)
			data.value = "";
			data.open = state;
			data.hover = -1;
			if (getSuggestions().length > 0) {
				data.focus = state;
			}
			if (!state) {
				input.value.blur();
			}
		}

		function getSuggestions() {
			return props.suggestions.filter((suggestion) =>
				suggestion.toUpperCase().includes(data.value.toUpperCase())
			);
		}

		function setValue(value) {
			data.selected = value;
			show(false);
			updateModel();
			emit("enter", value);
		}

		function updateModel(value) {
			emit("input", value);
			emit("update:modelValue", data.selected);
		}

		function onClickAway() {
			if (data.focus && !data.open) {
				show(false);
			}
			if (data.open) {
				data.open = false;
			}
		}

		function move(direction) {
			if(direction == 0) {
				data.hover == -1;
			} else if (direction < 0 && data.hover > -1) {
				data.hover += direction;
			} else if (direction > 0 && data.hover < getSuggestions().length - 1) {
				data.hover += direction;
			}
			if(data.hover > getSuggestions().length-1) {
				data.hover = getSuggestions().length -1
			}
			data.selected = getSuggestions()[data.hover]
			updateModel();
		}

		return {
			data,
			show,
			getSuggestions,
			setValue,
			updateModel,
			onClickAway,
			input,
			move,
		};
	},
};
</script>

<style lang="scss" scoped>
.search-panel-container {
	display: flex;
	flex-direction: row;
	position: relative;

	.input {
		width: 100%;
		display: block;
		box-sizing: border-box;
		height: 1.5rem;
		padding: 1rem 0.5rem;
		border: 1px solid #8c8c8c;
		color: inherit;
		font-size: 0.9rem;
		font-family: inherit;

		&:focus,
		&:hover {
			outline: 0;
			border: 1px solid #2285ff;
		}
	}

	.suggestions {
		background: white;
		position: absolute;
		top: calc(2rem + 1px);
		width: calc(100% - 2px - 1rem);
		height: max-content;
		border: 1px solid #2285ff;
		border-top: 1px solid rgba(140, 140, 140, 0.4);
		padding: 0.4rem 0.5rem;
		max-height: 15rem;
		overflow: auto;
		z-index: 1;

		.suggestion {
			list-style: none;
			font-size: 0.9rem;
			padding: 0.3rem 0.5rem;

			&:hover {
				background: rgba(240, 240, 240, 0.9);
				border-radius: 0.3rem;
			}
		}

		.hover {
			background: rgba(240, 240, 240, 0.9);
			border-radius: 0.3rem;
		}
	}
}
</style>