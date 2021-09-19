<template>
	<div class="home--container">
		<h1 class="title">Willkommen bei Arktur!</h1>
		<h2>Was ist Arktur?</h2>
		<p class="textbox">
			Arktur ist ein Planungstool für Lehveranstaltungen an der Fakultät für Mathematik und Informatik. Mit diesem Tool soll es für Lehrende möglich sein, ihre Veranstaltungen ganz einfach im vorraus dem Prüfungsamt zu übermitteln.
		</p>
		<h2>Wie funktioniert Arktur?</h2>
		<p class="textbox">
			Für Lehrende gibt es zwei Möglichkeiten Lehrveranstaltungen anzulegen:
		</p>
		<p class="textbox">
			1. Eine neue Lehrveranstaltung anlegen<br>
			Um eine neue Lehrveranstaltung anzulegen, muss zunächst in der Veranstaltungsübersicht nach einer ähnlichen oder alten Veranstaltung gesucht werden. Nachdem diese Ausgewählt wurde, kann mit dem Button "New" eine Neue Veranstaltung angelegt werden. Die Daten werden dabei automatisch von der alten Vorlesung übernommen wenn diese nicht verändert werden. 
		</p>
		<p class="textbox">
			2. Eine alte Lehrveranstaltung editieren<br>
			Um eine alte oder bereits angelegte Lehrveranstaltung zu editieren, wird zunächst in der Veranstaltungsübersicht nach der entsprechenden Veranstaltung gesucht. Nachdem diese Ausgewählt wurde, kann diese dem Button "Edit" editiert werden. Alle Daten die nicht geändert werden bleiben erhalten.
		</p>
		<h2>Wer kann Arktur verwenden?</h2>
		<p class="textbox">
			Arktur ist in erster Linie ein Werkzeug um die Kommunikation zwischen Lehrenden und Prüfungsamt zu erleichtern, daher haben gegenwärtig nur diese beiden Personengruppen Zugriff auf das Arktur System.
		</p>
		<h2>Woher kommt Arktur?</h2>
		<p class="textbox">
			Arktur wurde im Auftrag der Fakultät von Studierenden im Umfang des Softwareentwicklungsprojektes geschrieben.
		</p>
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
#boxes {
	display: flex;
	justify-content: center;
}

.home--container {
	width: 40%;
	margin: auto;
	text-align: left;

	.title {
		text-align: center;
	}

	.textbox {
		text-align: justify;
	}
}
</style>