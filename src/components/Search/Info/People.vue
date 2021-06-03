<template>
	<h3><u>Personen:</u></h3>
	<div v-for="(roll, key) in people" :key="key">
		<h4>{{ key }}</h4>
		<div v-for="person in roll" :key="person.friedolinID">
			<p v-if="roll">
				{{ person.grad }} {{ person.vorname }} {{ person.nachname }} [<a
					:href="
						'https://friedolin.uni-jena.de/qisserver/rds?state=verpublish&moduleCall=webInfo&publishConfFile=webInfoPerson&publishSubDir=personal&personal.pid=' +
						person.friedolinID
					"
					target="_blank"
				>
					{{ person.friedolinID }} </a
				>]
			</p>
		</div>
	</div>
	<br />
</template>

<script>
export default {
	props: {
		people: {
			type: Object,
			required: true,
		},
	},
	setup() {
		return {};
	},
};
</script>

<style lang="scss" scoped>
p {
	margin: 0;
	margin: 0.2rem 0 0.2rem 0;
}
</style>