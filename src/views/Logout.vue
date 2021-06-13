

<script>
import { request } from "@/scripts/request.js";

export default {
	setup() {
    const rq = new request();
    rq.logout();
    }
}
</script>

<style lang="scss" scoped>
</style>