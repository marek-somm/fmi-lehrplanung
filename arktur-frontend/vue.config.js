module.exports = {
	publicPath: '/',
	css: {
		loaderOptions: {
			sass: {
				additionalData: '@import "@/styles/base.scss";'
			}
		}
	}
 }