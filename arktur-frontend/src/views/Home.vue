<template>
	<div class="home--container">
		<h2>Willkommen auf der Seite der Lehrplanungsplattform der Fakultät für Mathematik und Informatik der Universität Jena.</h2>
		<p class="textbox">
			Um die Lehrplannung transparenter zu machen und um den Studierenden eine Anlaufstelle zur Plannung ihre kommenden Semester zu ermöglichen wurde dieses Lehrplannungswerkzeugt entwickelt. Hier können Dozierende ihr kommende Lehre planen und so für Studierende der Fakultät sichtbar stellen.
		</p>
		<p class="textbox">
			Für Lehrende gibt es dabei zwei Möglichkeiten Lehrveranstaltungen anzulegen:
		</p>
		<p class="textbox">
			1. Basierend auf einer vergangenen Veranstaltung:<br>
			Auf Ihrer Startseite werden Ihnen Vorschläge für zukünftige Veranstaltungen gemacht. Diese können Sie mit einem klick auf die entsprechende Verasntaltung erstellen. Alternativ haben Sie die Möglichkeit, in der Veranstaltungsübersicht einer beliebigen Veranstaltung über den Knopf "Neu" eine Veranstaltung mit bereits ausgefüllten Informationen anzulegen. 
		</p>
		<p class="textbox">
			2. Eine neue Veranstaltung:<br>
			Um eine neue Lehrveranstaltung anzulegen, wechseln Sie zur Veranstaltungsübersicht und klicken auf den Knopf "Neu" links neben der Suchleiste. Anschließend können Sie eine vollkommen neue Veranstaltung anlegen. Vergessen Sie nicht sich selbst als Person hinzuzufügen, damit die Veranstaltung anschließend auf Ihrer Startseite angezeigt wird.
		</p>
		<p class="textbox">
			Auf Ihrer Startseite sehen Sie Ihre eigenen Veranstaltungen, sowohl Vergangene, als auch Kommende. Dort werden Ihnen ebenfalls zukünftige Veranstaltungen vorgeschlagen, basierend auf den vergangenen 4 Semestern. Diese sind ausgegraut und müssen mittles klick erst hinzugefügt werden, bevor diese als Veranstaltung angezeigt wird.<br>
			Dort können Sie Ihre kommenden Veranstaltungen über den Knopf "Ändern" auf der jeweiligen Veranstaltungsübersicht bearbeiten. Vergangene und laufende Veranstaltungen lassen sich nur noch vom Prüfungsamt ändern.
		</p>
		<h2>Woher kommt die Lehrplannungsplattform?</h2>
		<p class="textbox">
			Die Plattform ist im Auftrag der Fakultät von Studierenden im Umfang des Softwareentwicklungsprojektes gestartet worden. Anschließend wurde sie von Marek Sommerfeld auf einen benutzbaren Stand gebracht
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