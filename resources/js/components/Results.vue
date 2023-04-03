<style scoped>
    .main-wrapper{
        display:grid;
        grid-gap: 0.5rem;
        grid-template-rows: 2rem 2rem auto;
        height: 90vh;
        padding: 0.5rem;
    }

    .filters-wrapper,
    .modes-wrapper{
        display:flex;
        border-radius: 1rem;
        overflow: hidden;
    }

    .filters-wrapper > div, 
    .modes-wrapper > div{
        flex: 1 1 0;
        box-shadow: .25rem .25rem 0.5rem #bbb;
        transition: all 250ms ease;
        text-align:center;
        padding: .25rem;
		border: 1px solid var(--clr-bg-white);
		border-top-color: transparent;
		border-bottom-color: transparent;
        transition: all 500ms;
    }


    .data-wrapper{
        height: 100%;
        display:grid;
        grid-template-rows: 0rem auto;
        border-radius: var(--border-radius);
        box-shadow: 2px 2px 5px #000;
        transition: all 500ms;
    }

    .data-wrapper > div{
        transition: all 300ms cubic-bezier(.1,.6,1,.9);
		height:100%;
        margin: 0.25rem;
        border-radius: var(--border-radius);
        justify-content: center;
        align-items: center;
        box-shadow: 2px 2px 8px rgba(0,0,0,.5);
    }

    .data-wrapper.selected-filter, 
    .data-wrapper.selected-mode{
        grid-template-rows: 10rem auto;
    }

    .filters-wrapper > div.selected-filter,
    .modes-wrapper > div.selected-mode{
        background: var(--clr-bg-light-blue);
    }

    .filters-wrapper > div:not(.selected-filter),
    .modes-wrapper > div:not(.selected-mode){
        flex: 1 3 0;
        background: var(--clr-text-grey);
    }

    .filters-wrapper > div:hover,
    .modes-wrapper > div:hover{
        background:var(--clr-bg-green);
        cursor: pointer;
		color: var(--clr-text-white);
    }

    .filters-wrapper > div.selected-filter:hover,
    .modes-wrapper > div.selected-mode:hover{
		color: var(--clr-text-white);
    }

    @media screen and (min-width: 800px) {
        .data-wrapper.selected-filter, 
        .data-wrapper.selected-mode{
            grid-template-rows: 7.5rem auto;
        }
    }
</style>

<template>
    <div class="main-wrapper" >
        <div class="filters-wrapper">
            <div
                v-for="filter in filters"
                :key="filter"
                :class="filterClass(filter)"
                @click="select(filter, 'filter')"
                v-text="capatilizeWord(filter)"
            />
        </div>
        <div class="modes-wrapper">
            <div
                v-for="mode in modes"
                :key="mode"
                :class="modeClass(mode)"
                @click="select(mode, 'mode')"
                v-text="capatilizeWord(mode)"
        />
        </div>
        <div
            class="data-wrapper"
            :class="{'selected-filter': (selected_tab.filter || selected_tab.mode)}"
        > 
            <div class="filters-area">
				<SelectSpecies v-if="selected_tab.filter == 'species'"/>
				<SelectUser v-else-if="selected_tab.filter == 'user'"/>
				<SelectPortal v-else-if="selected_tab.filter == 'portal'"/>
				<span v-else-if="selected_tab.filter">{{ capatilizeWord(selected_tab.filter) }}</span>
				<span v-else>{{ capatilizeWord(selected_tab.mode) }}</span>
			</div>
            <div class="data">
                Data
                <pre>
                    {{ selected }}
                </pre>
            </div>
        </div>
      </div>
  </template>
  
  <script>
  import { defineComponent } from 'vue'
  import { mapState } from "vuex"
  import SelectSpecies from "./SelectSpecies.vue"
  import SelectUser from "./SelectUser.vue"
  import SelectPortal from "./SelectPortal.vue"

  export default defineComponent({
	name: "Results",
	components: {
        SelectSpecies,
		SelectUser,
		SelectPortal,
	},
    data() {
		return {
			filters: [ "species", "location",  "date", "user", "portal", ],
			modes: ["table", "map", "chart"],
			selected_tab: {
                filter: null,
                mode: null
			},
			selectedFilter: null,
			selectedMode: null,
		};
    },
    computed: {
        ...mapState(["selected"])
    },
    methods: {
		capatilizeWord(str){
			return str ? str.charAt(0).toUpperCase() + str.slice(1) : ""
		},
		filterClass(filter){
			return (this.selected_tab.filter == filter) ? filter + " selected-filter" : filter
		},
		modeClass(filter){
			return (this.selected_tab.mode == filter) ? filter + " selected-mode" : filter
		},
		select(val, type){
            const oppositeType = (type === "filter") ? "mode" : "filter";
            this.selected_tab[oppositeType] = null;
            this.selected_tab[type] = (this.selected_tab[type] === val) ? null : val;
		}
    }
  })
  </script>
  