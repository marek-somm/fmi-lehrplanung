export default {
	convertSemester(value) {
		if (value % 10 == 0) {
			return 'SoSe ' + parseInt(value / 10);
		} else {
			return 'WiSe ' + parseInt(value / 10);
		}
	},

	convertTurnus(value) {
		if (value == 0) {
			return 'Keine Übernahme';
		} else if (value == 1) {
			return 'Jedes Semester';
		} else if (value == 2) {
			return 'Jedes 2. Semester';
		}
		return 'Nicht angegeben';
	},

	removeFromArray(array, element) {
		const index = array.indexOf(element);
		if (index > -1) {
			array.splice(index, 1);
		}
	},
};
