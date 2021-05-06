<template>
  <div>
    <h1>Logout</h1>
  </div>
</template>

<script>
import store from '@/store'
export default {
	setup() {
		store.dispatch('User/setUser', false)
        window.location.href = '/'
        // this.$router.push({path: 'About'});
    }
}
</script>

<style lang="scss" scoped>
</style>