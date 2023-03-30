
<template>
    <!-- <species-page /> -->
    Stats
    <pre>
        {{ sizes }}
    </pre>
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
        sizes(){
            return {
                taxa: JSON.stringify(this.taxa).length,
                users: JSON.stringify(this.users).length,
                observations: JSON.stringify(this.observations).length
            }
        }
    }

})
</script>