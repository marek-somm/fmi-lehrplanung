<template>
	<div class="search-panel-container">
		<input
			class="input"
			:value="modelValue"
			:placeholder="placeholder"
			ref="input"
			@focus="onFocus"
			@blur="onBlur"
			@input="onInput"
			@keydown.tab="onTab"
			@keydown.down="move(1)"
			@keydown.up="move(-1)"
			@keydown.enter="onEnter"
			@keydown.esc="onEsc"
			@keydown="prevent"
		/>
		<div class="dropdown" v-if="dropdown" @click="focusInput">&#x02c5;</div>
		<div
			class="suggestions"
			v-show="data.suggestions.show"
			v-click-away="onClickAway"
		>
			<div>
				<li
					class="suggestion"
					v-for="(suggestion, index) in getSuggestions()"
					:key="index"
					@click="onClick(suggestion)"
					:class="{ selected: index == data.suggestions.selected }"
					:ref="itemRef"
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
import { onBeforeUpdate } from "vue";

export default {
	mixins: [VueClickAway],
	props: {
		modelValue: String,
		placeholder: String,
		suggestions: {
			default: [],
		},
		regex: {
			default: null,
		},
		dropdown: {
			type: Boolean,
			default: false,
		},
	},
	emits: ["focus", "blur", "update:modelValue", "enter"],
	setup(props, { emit }) {
		const data = reactive({
			input: {
				focused: false,
			},
			suggestions: {
				show: false,
				selected: 0,
			},
		});
		let itemRefs = [];
		const itemRef = (el) => {
			if (el) {
				itemRefs.push(el);
			}
		};
		const input = ref(null);

		onBeforeUpdate(() => {
			itemRefs = [];
		});

		function getSuggestions() {
			let result = props.suggestions;
			if (input.value) {
				result = props.suggestions.filter((suggestion) =>
					suggestion
						.toUpperCase()
						.includes(input.value.value.toUpperCase())
				);
			}

			if (data.suggestions.selected > result.length - 1) {
				data.suggestions.selected = result.length - 1;
			}
			return result;
		}

		function onFocus() {
			data.input.focused = true;
			if (getSuggestions().length > 0) {
				data.suggestions.show = true;
			}
			data.suggestions.selected = 0;
			emit("focus");
		}

		function onBlur() {
			data.input.focused = false;
			emit("blur");
		}

		function onInput() {
			if (getSuggestions().length == 0) {
				data.suggestions.show = false;
			} else {
				data.suggestions.show = true;
			}
			if (data.suggestions.selected < 0) {
				data.suggestions.selected = 0;
			}
			if (itemRefs.length > 0) {
				itemRefs[data.suggestions.selected].scrollIntoView();
			}
			emit("update:modelValue", input.value.value);
		}

		function onTab() {
			data.suggestions.show = false;
		}

		function onEsc() {
			data.suggestions.show = false;
			input.value.blur();
		}

		function onClick(value) {
			data.suggestions.show = false;
			emit("update:modelValue", value);
			emit("enter");
		}

		function onClickAway() {
			if (!data.input.focused) {
				data.suggestions.show = false;
			}
		}

		function move(direction) {
			if (direction == -1 && data.suggestions.selected > 0) {
				data.suggestions.selected += direction;
			} else if (
				direction == 1 &&
				data.suggestions.selected < getSuggestions().length - 1
			) {
				data.suggestions.selected += direction;
			}
			if (itemRefs.length > 0) {
				itemRefs[data.suggestions.selected].scrollIntoView();
			}
		}

		function onEnter() {
			if (getSuggestions().length > 0) {
				input.value.value = getSuggestions()[data.suggestions.selected];
				data.suggestions.show = false;
				input.value.blur();
				emit("update:modelValue", input.value.value);
				emit("enter", input.value.value);
			}
		}

		function prevent(e) {
			if (props.regex) {
				if (!e.key.match(RegExp(props.regex))) {
					//&& !e.key.match(/\d/)) {
					e.preventDefault();
				}
			}
		}

		function focusInput() {
			input.value.focus();
		}

		return {
			data,
			input,
			itemRef,
			getSuggestions,
			onFocus,
			onBlur,
			onInput,
			onTab,
			onEnter,
			onEsc,
			onClick,
			onClickAway,
			move,
			prevent,
			focusInput,
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
		background: inherit;

		&:focus,
		&:hover {
			outline: 0;
			border-color: #2285ff;
		}
	}

	.dropdown {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 2rem;
		border: 1px solid #8c8c8c;
		border-left: none;

		&:hover {
			cursor: pointer;
			background: rgb(245, 245, 245);
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

		.selected {
			background: rgba(240, 240, 240, 0.9);
			border-radius: 0.3rem;
		}
	}
}
</style>