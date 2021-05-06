<template>
  <div class="about--container">
    <div class="message" v-if="loggedIn == 'true'">You are now logged in!</div>
    <div class="message" v-if="loggedIn == 'false'">Wrong Username or Password!</div>
    <router-link class="link" :to="{ name: 'Home' }">Home</router-link>
    <router-link class="link" v-if="!(loggedIn == 'true')" :to="{ name: 'Login' }">Login</router-link>
  </div>
</template>

<script>
export default {
  props: {
    loggedIn : {
      type: String,
      default: 'false'
    }
  },
  setup () {
    
    return {}
  }
}
</script>

<style lang="scss" scoped>
.about--container {
  .message {
    font-size: 2rem;
    margin-bottom: 2rem;
  }

  .link {
    text-decoration: none;
    color: #2c3e50;
    margin: 1rem;

    &:hover {
      text-decoration: underline;
    }
  }
}
</style>