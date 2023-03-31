<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    main > div{
        border: 1px solid red;
        position: absolute;
        top: 4rem;
        width: 100%;
        padding: 1rem;
    }
    @media screen and (min-width: 800px) {
        main > div{
            top: 4.75rem;
        }
    }
</style>
 
<template>
    <div class="main-wrapper">
		<navbar />
        <transition name="fade">
            <main>
                <home v-if="selected_page == 'Home'"/>
                <about v-if="selected_page == 'About'"/>
                <f-a-q v-if="selected_page == 'FAQ'"/>
                <videos v-if="selected_page == 'Videos'"/>
                <results v-if="selected_page == 'Past Results'"/>
                <partners v-if="selected_page == 'Partners'"/>
            </main>
        </transition>
	</div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from './store/index.js'
import Navbar from './components/Navbar.vue'
import Home from './components/Home.vue'
import About from './components/About.vue'
import FAQ from './components/FAQ.vue'
import Videos from './components/Videos.vue'
import Results from './components/Results.vue'
import Partners from './components/Partners.vue'
import SpeciesPage from './components/SpeciesPage.vue'
export default defineComponent({
    name: "App",
    components: {
        Navbar,
        Home,
        About,
        FAQ,
        Videos,
        Results,
        Partners,
        SpeciesPage,
    },
    data(){
        return {
        }
    },
    created(){
        store.dispatch('getAllData')
        this.set_page()
    },
    computed:{
        ...mapState([
            "selected_page"
        ]),
    },
    methods:{
        set_page(){
            let page = window.location.hash.slice(1)
            if(page == ""){
                page = "Home"
            }
            store.dispatch("gotoPage", page)
        }
    }
})
</script>