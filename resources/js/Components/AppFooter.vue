<template>
  <footer class="bg-white border-t">
    <div class="max-w-7xl mx-auto px-4 py-3 text-center text-sm text-gray-500">
      © {{ year }} — Gestión Ciudadana
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
const year = new Date().getFullYear()
</script>

<style scoped>
</style>
