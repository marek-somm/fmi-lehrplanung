console.log('Coppying /backend')

var ncp = require('ncp').ncp
ncp.limit = 16

// ncp(source, destination, callback)
ncp('.\\backend', '.\\dist', function(err) {
	if (err) {
		return console.error(err)
	}
	console.log('Copied /backend to /dist')
})
