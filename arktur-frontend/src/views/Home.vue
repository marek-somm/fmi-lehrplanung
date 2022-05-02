<template>
	<div class="home--container">
		<h2>Willkommen bei der Lehrplanungsplattform der Fakultät für Mathematik und Informatik der Universität Jena.</h2>
		<p class="textbox">
			Um die Lehrplanung transparenter zu machen und um den Studierenden eine Anlaufstelle zur Planung ihrer kommenden Semester zu ermöglichen, wurde dieses Lehrplanungswerkzeug entwickelt. Hier können Dozierende ihre kommenden Lehrveranstaltungen planen und für Studierende der Fakultät sichtbar stellen. 
		</p>
		<p class="textbox">
			Für Lehrende gibt es dabei zwei Möglichkeiten Lehrveranstaltungen anzulegen: 
		</p>
		<p class="textbox">
			<u>1. Basierend auf einer vergangenen Veranstaltung:</u> <br>
			Auf Ihrer Startseite werden Ihnen Vorschläge für zukünftige Veranstaltungen gemacht. Mit einem Klick auf die entsprechende Veranstaltung können Sie diese Veranstaltung für ein neues Semester erstellen. Alternativ haben Sie die Möglichkeit, in der Veranstaltungsübersicht einer beliebigen Veranstaltung, über den Knopf "Neu" eine Veranstaltung mit bereits ausgefüllten Informationen anzulegen. 
		</p>
		<p class="textbox">
			<u>2. Eine neue Veranstaltung:</u> <br>
			Um eine neue Lehrveranstaltung anzulegen, wechseln Sie zur Veranstaltungsübersicht und klicken auf den Knopf "Neu" links neben der Suchleiste. Anschließend können Sie eine vollkommen neue Veranstaltung anlegen. Vergessen Sie nicht sich selbst als Person hinzuzufügen, damit die Veranstaltung anschließend auf Ihrer Startseite angezeigt wird. 
		</p>
		<p class="textbox">
			Auf Ihrer Startseite sehen Sie Ihre eigenen Veranstaltungen, sowohl Vergangene, als auch Kommende. Dort werden Ihnen ebenfalls zukünftige Veranstaltungen vorgeschlagen, basierend auf den vergangenen 4 Semestern. Diese sind ausgegraut und müssen mittles Klick erst hinzugefügt werden, bevor sie als Veranstaltungen angezeigt werden. <br>
			Auf Ihrer Startseite können Sie Ihre kommenden Veranstaltungen über den Knopf "Ändern" auf der jeweiligen Veranstaltungsübersicht bearbeiten. Vergangene und laufende Veranstaltungen lassen sich nur noch vom Prüfungsamt ändern. 
		</p>

		<h2>Woher kommt die Lehrplanungsplattform?</h2>
		<p class="textbox">
			Die Plattform ist im Auftrag der Fakultät von Studierenden im Zuge des Softwareentwicklungsprojektes gestartet worden. Anschließend wurde sie von Marek Sommerfeld weiterentwickelt und auf einen benutzbaren Stand gebracht.
		</p>
		<br>
		<p class="textbox">
			<i>Bei Fragen oder Problemen wenden Sie sich bitte an das Prüfungsamt. Sollten technische Fehler auftreten, kontaktieren Sie bitte direkt den Seitenadministrator unter <a href="mailto:marek.sommerfeld@uni-jena.de">marek.sommerfeld@uni-jena.de</a>.</i>
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