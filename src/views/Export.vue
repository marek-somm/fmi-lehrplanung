<template>
	<div class="results" v-if="input.data">
        <table class="item" v-for="(item, key, index) in input.data.data" :key="index">
            <tr>
            <th class="item" v-for="(item, key, index) in item[0]" :key="index">
                {{key}}
            </th></tr>
            <tr class="item" v-for="(item, key, index) in item" :key="index">
                <td class="item" v-for="(item, key, index) in item" :key="index">
                    {{item}}
                </td>
            </tr>
        </table>
    </div>
</template>

<script>
import { onMounted, reactive } from "vue";
import { request } from "@/scripts/request.js";
export default {
	setup() {
		const rq = new request();
		const input = reactive({
			data: {},
			});

		onMounted(() => {
			getVeranstaltungen();
		});

        async function getVeranstaltungen() {
            input.data = await rq.searchVeranstaltung()
        }
		return {
            input
        }
    }
}

</script>

<style lang="scss" scoped>
.results{
    padding: 1em 1em;
    font-family: helvetica, sans-serif;
    height: calc(100vh - 14.78rem);
    overflow-y: auto;
}
</style>