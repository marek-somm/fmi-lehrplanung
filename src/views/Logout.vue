<template>
  <div>
    <h1>Logout</h1>
  </div>
</template>

<script>
import store from '@/store'
import { useRouter } from "vue-router";

export default {
	setup() {
    const router = useRouter()
    
		store.dispatch('User/setUser', false)
        //window.location.href = '/'
        router.push({name: 'Home'});
    }
}
</script>

<style lang="scss" scoped>
</style>