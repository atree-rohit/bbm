<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s;
    }

    .fade-enter, .fade-leave-to {
        opacity: 0;
    }
    main{
        position: relative;
        top: 3.7rem;
        height: calc(100vh - 4rem);
        overflow-y: auto;
        border: 1px solid yellowgreen;
    }

    main > div{
        border: 1px solid red;
        width: 100%;
    }
    @media screen and (min-width: 800px) {
        main{
            width: 100%;
            top: 4.05rem;
            height: calc(100vh - 4.75rem);
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
            let page = this.capitalizeWords(window.location.pathname.replace("/", "").replace("_", " "))
            if(page == "" || page == "/"){
                page = "Home"
            } else if (page == "Faq"){
                page = "FAQ"
            }
            store.dispatch("gotoPage", page)
        },
        capitalizeWords(str) {
            return str.toLowerCase().replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
        },
    }
})
</script>