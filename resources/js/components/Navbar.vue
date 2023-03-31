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
    nav ul li a{
        color: var(--clr-text-grey);
        text-decoration: none;
        font-size: 1.2rem;
        text-transform: uppercase;
        opacity: 0;
        transition: opacity 50ms ease-in-out;
    }
    nav ul li a:hover{
        color: var(--clr-bg-light-blue);
    }

    .nav-toggle:checked ~ nav{
        transform: scale(1, 1);
    }

    .nav-toggle:checked ~ nav ul li a {
        opacity: 1;
        transition-delay: 250ms;
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
            transform: scale(1.5);
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
        nav ul li a{
            opacity: 1;
            position: relative;
            transition: all 1s;
        }

        nav ul li a::before, 
        nav ul li a::after{
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

        nav ul li a::before{
            top: -.75rem;
            transform-origin: right;
        }
        nav ul li a::after{
            bottom: -.75rem;
            transform-origin: left;
        }
        nav ul li a:hover::before, 
        nav ul li a:hover::after{
            transform: scale(1, 1);
        }
    }
</style>

<template>
    <header>
        <div class="logo">
            <img src="/img/bbm_23_logo.jpg" alt="">
        </div>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <nav>
            <ul>
                <li v-for="page in pages" :key="page">
                    <a href="#">{{ page }}</a>
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
import store from './store/index.js'

export default defineComponent({
    name: "Navbar",
    data(){
        return {
            pages: ["Home", "About", "FAQ", "Videos", "Past Results", "Partners"],
            selectedPage: "Home"
        }
    },
    methods: {
    },
    computed: {
        ...mapState({
            species: state => state.species
        })
    }
})
</script>