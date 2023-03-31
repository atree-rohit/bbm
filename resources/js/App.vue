<style scoped>
</style>
 
<template>
    <div class="main-wrapper">
		<navbar />
		<results />
	</div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from './store/index.js'
import Navbar from './components/Navbar.vue'
import Results from './components/Results.vue'
import SpeciesPage from './components/SpeciesPage.vue'
export default defineComponent({
    name: "App",
    components: {
      Navbar,
      Results,
      SpeciesPage,
    },
    data(){
        return {
            
        }
    },
    created(){
        store.dispatch('getAllData')
    },
    computed:{
        ...mapState([
        ]),
    },
    methods:{
    }

})
</script>