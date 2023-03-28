
<template>
    <species-page />
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import SpeciesPage from './components/SpeciesPage.vue'
export default defineComponent({
    name: "App",
    components: {
        SpeciesPage
    }
})
</script>