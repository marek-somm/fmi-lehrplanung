

<script>
import auth from "@/services/AuthService";

export default {
	setup() {
		auth.logout();
	},
};
</script>

<style lang="scss" scoped>
</style>