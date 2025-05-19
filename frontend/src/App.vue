
<script setup>
import AppHeader from "./components/homePage/AppHeader.vue";
import AppFooter from "./components/homePage/AppFooter.vue";
import { RouterView } from "vue-router";
</script>
<template>
  <div>
    <AppHeader />
    <router-view />
    <AppFooter />
  </div>
</template>
