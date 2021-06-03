<template>
	<h3><u>Prüfungen:</u></h3>
	<div class="block" v-for="(semester, key) in exams" :key="semester">
		<h3 class="title">{{ key }}</h3>
		<div
			class="box"
			v-for="(exam, index) in semester"
			:key="index"
			@click="view(exam)"
		>
			<p>
				<b>{{ exam.titel }}</b>
			</p>
			<br />
			<p v-for="(elem, key) in exam" :key="key" v-show="key != 'titel' && key != 'semester'">
				{{ key }}: {{ elem }}
			</p>
		</div>
		<br />
	</div>
</template>

<script>
export default {
	props: {
		exams: {
			type: Object,
			required: true,
		},
	},
	setup(props, { emit }) {
		function view(exam) {
			emit("exam", exam);
		}

		return {
			view,
		};
	},
};
</script>

<style lang="scss" scoped>
.block {
	margin-bottom: 1.5rem;

	.title {
		text-align: center;
	}

	.box {
		border: 1px gray solid;
		padding: 0.5rem;
		margin-bottom: 0.5rem;
		transition: all 0.1s ease;

		&:hover {
			cursor: pointer;
			background-color: rgb(230, 230, 230);
			transform: scale(1.01);
		}
	}
}

p {
	margin: 0;
	margin: 0.2rem 0 0.2rem 0;
}
</style>