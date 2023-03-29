
<template>
    <species-page />
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from './store/index.js'
import SpeciesPage from './components/SpeciesPage.vue'
export default defineComponent({
    name: "App",
    components: {
        SpeciesPage
    },
    created(){
        store.dispatch('getAllData')
    },
    computed:{
        ...mapState([
            'taxa',
            'users',
            'observations'
        ]),
    }

})
</script>