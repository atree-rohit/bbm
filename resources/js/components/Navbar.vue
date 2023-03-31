<style scoped>
    header{
        background: var(--clr-bg-blue);
        text-align: center;
        position: fixed;
        width: 100%;
        z-index: 100;
        padding: 0.5rem .25rem;
    }
    .logo{
        color: var(--clr-text-white);
        transform: scale(1.5);
    }
    .logo img{
        /* position: relative; */
        /* top: -0.5rem; */
        max-height: 2.5rem;
    }

    .nav-toggle {
        display: none;
    }

    .nav-toggle-label{
        position: absolute;
        top: 0;
        left: 0;
        margin-left: 1rem;
        height:100%;
        display: flex;
        align-items:center;
    }

    .nav-toggle-label span,
    .nav-toggle-label span::before,
    .nav-toggle-label span::after{
        display: block;
        background: white;
        height: 2px;
        width: 1.5rem;
        border-radius: 2px;
        position: relative;
        transition: all 200ms;
    }

    .nav-toggle-label span::before,
    .nav-toggle-label span::after{
        content: "";
        position: absolute;
    }

    .nav-toggle-label span::before{
        bottom: 7px;
    }
    .nav-toggle-label span::after{
        top: 7px;
    }

    .nav-toggle:checked ~ .nav-toggle-label span,
    .nav-toggle:checked ~ .nav-toggle-label span::before, 
    .nav-toggle:checked ~ .nav-toggle-label span::after{
        background: transparent;
    }

    .nav-toggle:checked ~ .nav-toggle-label span::before{
        /* content: "\274c"; */
        content: "ⓧ";
        font-size: 2rem;
        margin-bottom: 1rem;
        color:white;
    }

    .nav-toggle-label:hover{
        cursor: pointer;
        
    }

    .nav-toggle-label:hover span,
    .nav-toggle-label:hover span::before,
    .nav-toggle-label:hover span::after{
        background: var(--clr-bg-light-blue);
    }

    nav{
        position: absolute;
        text-align: left;
        top: 100%;
        left: 0;
        background: var(--clr-bg-blue);
        width: 100%;
        transform: scale(1, 0);
        transform-origin: top;
        transition: transform 400ms ease-in-out;
    }

    nav ul{
        list-style: none;
    }

    nav ul li {
        margin-bottom: 1rem;
        margin-left: 1rem;
    }
    nav ul li span{
        color: var(--clr-text-grey);
        text-decoration: none;
        font-size: 1.2rem;
        text-transform: uppercase;
        opacity: 0;
        transition: opacity 50ms ease-in-out;
    }
    nav ul li span:hover{
        cursor: pointer;
        color: var(--clr-bg-light-blue);
    }
    nav ul li.active span {
        color: goldenrod;
    }

    .nav-toggle:checked ~ nav{
        transform: scale(1, 1);
    }

    .nav-toggle:checked ~ nav ul li span{
        opacity: 1;
        transition-delay: 250ms;
    }

    nav ul li.active span{
        position:relative;
    }

    nav ul li.active span::before,
    nav ul li.active span::after{
        content: "";
        position: absolute;
        top: -.25rem;
        width: 2px;
        height: 125%;
        background: var(--clr-bg-light-blue);
        border-radius: 0.5rem;
    }

    nav ul li.active span::before{
        left: -0.5rem;
    }

    nav ul li.active span::after{
        right: -.5rem;
    }

    @media screen and (min-width: 800px) {
        .nav-toggle-label{
            display: none;
        }

        header {
            display: grid;
            grid-template-columns: 1fr auto minmax(10rem, 15fr) 1fr;
        }
        .logo{
            grid-column: 2 / span 1;
            display: flex;
            justify-content: baseline;
            transform: scale(1.65);
            border: 4px solid var(--clr-bg-blue);
            border-radius: .5rem;
            border-top: 0.4rem solid transparent;
            border-bottom-width: 4px;
        }
        nav{
            all: unset;
            grid-column: 3 / 4;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        nav ul {
            display: flex;
        }
        nav ul li{
            margin-left: 1.75rem;
            margin-bottom: 0;
        }
        nav ul li span{
            opacity: 1;
            position: relative;
            transition: all 1s;
        }

        nav ul li span::before, 
        nav ul li span::after{
            content: "";
            display: block;
            height: .1rem;
            background: var(--clr-bg-light-blue);
            border-radius: 5rem;
            position: absolute;
            left: 0;
            right: 0;
            transform: scale(0, 0);
            transition: transform ease-in-out 500ms;
        }

        nav ul li span::before{
            top: -.75rem;
            transform-origin: right;
        }
        nav ul li span::after{
            bottom: -.75rem;
            transform-origin: left;
        }
        nav ul li span:hover::before, 
        nav ul li span:hover::after{
            transform: scale(1, 1);
        }
    }
</style>

<template>
    <header>
        <div class="logo">
            <img src="/img/bbm_23_logo.jpg" alt="">
        </div>
        <input type="checkbox" id="nav-toggle" class="nav-toggle" v-model="nav_toggle">
        <nav>
            <ul>
                <li
                    v-for="page in pages"
                    :key="page"
                    :class="{ 'active': page === selected_page }"
                    @click.stop="selectPage(page)"
                >
                    <span>{{ page }}</span>
                </li>
            </ul>
        </nav>
        <label for="nav-toggle" class="nav-toggle-label">
            <span></span>
        </label>
    </header>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from '../store/index.js'

export default defineComponent({
    name: "Navbar",
    data(){
        return {
            nav_toggle: false
        }
    },
    computed: {
        ...mapState(["pages", "selected_page"])
    },
    methods: {
        selectPage(page){
            store.dispatch("gotoPage", page)
            history.pushState(
                null,
                null,
                this.URLSlug(page)
            )
            this.nav_toggle = false
        },
        URLSlug(page){
            return page.toLowerCase().replace(" ", "_")
        }
    },
})
</script>