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

	convertTurnusToNumber(value) {
		if (value == 'Keine Übernahme') {
			return 0;
		} else if (value == 'Jedes Semester') {
			return 1;
		} else if (value == 'Jedes 2. Semester') {
			return 2;
		}
		return -1;
	},

	addTurnus(semester, turnus) {
		if (turnus == 1) {
			return Number(semester % 10) == 1 ? Number(semester) + 9 : Number(semester) + 1;
		}
		if (turnus == 2) {
			return Number(semester) + 10;
		}
	},

	removeFromArray(array, element) {
		const index = array.indexOf(element);
		if (index > -1) {
			array.splice(index, 1);
		}
	},

	getCurrentSemester() {
		return 20210;
	},

	sortObj(obj) {
		var obj = Object.keys(obj).sort(function(a,b){ return +b - +a;}).reduce(function(t,k) {
			t.set(k,obj[k]);
			return t;
	  },new Map());
	  return obj
	}
};
