<template>
	<div class="home--container">
		<h1 class="title">Willkommen bei Arktur!</h1>
		<h2>Was ist Arktur?</h2>
		<p class="textbox">
			Arktur ist ein Planungstool für Lehveranstaltungen an der Fakultät für Mathematik und Informatik. Mit diesem Tool können Sie als lehrende Person Ihre Veranstaltungen für die kommenden Semester ganz einfach dem Prüfungsamt mitteilen.
		</p>
		<h2>Wie funktioniert Arktur?</h2>
		<p class="textbox">
			Für Lehrende gibt es zwei Möglichkeiten Lehrveranstaltungen anzulegen:
		</p>
		<p class="textbox">
			1. Basierend auf einer vergangenen Veranstaltung:<br>
			Wenn Sie bereits eine ähnliche Lehrveranstaltung angeboten haben, können Sie ganz einfach in der Veranstaltungsübersicht nach dieser suchen. Anschließend können Sie in der Veranstaltungsübersicht über den Knopf "Neu" eine Veranstaltung mit bereits ausgefüllten Informationen, basierend auf Ihrer Alten, anlegen.
		</p>
		<p class="textbox">
			2. Eine neue Veranstaltung:<br>
			Um eine neue Lehrveranstaltung anzulegen, wechseln Sie ganz einfach in die Veranstaltungsübersicht und klciken auf den Knopf "Neu" links neben der Suchleiste. Anschließend können Sie eine vollkommen neue Veranstaltung anlegen. Vergessen Sie nicht sich selbst als Person hinzuzufügen, damit die Veranstaltung anschließend auf Ihrer Startseite angezeigt wird.
		</p>
		<p class="textbox">
			Auf Ihrer Startseite sehen Sie Ihre eigenen Veranstaltungen, sowohl Vergangene, als auch Kommende. Dort werden Ihnen ebenfalls zukünftige Veranstaltungen vorgeschlagen, basierend auf den vergangenen 4 Semestern. Diese sind ausgegraut und müssen mittles klick erst hinzugefügt werden, bevor diese als Veranstaltung angezeigt wird.<br>
			Dort können Sie Ihre kommenden Veranstaltungen über den Knopf "Ändern" auf der jeweiligen Veranstaltungsübersicht zu Bearbeiten. Vergangenen und laufende Veranstaltungen lassen sich nur noch vom Prüfungsamt ändern.
		</p>
		<h2>Woher kommt Arktur?</h2>
		<p class="textbox">
			Arktur ist im Auftrag der Fakultät von Studierenden im Umfang des Softwareentwicklungsprojektes gestartet worden. Anschließend wurde es von Marek Sommerfeld auf einen benutzbaren Stand gebracht.
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