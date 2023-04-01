<style scoped>
    .main-wrapper{
        height: 100%;
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
        grid-template-rows: 0rem 0rem auto;
        border-radius: var(--border-radius);
        box-shadow: 2px 2px 5px #000;
    }

    .data-wrapper > div{
        transition: all 300ms cubic-bezier(.1,.6,1,.9);
		height:100%;
        /* display: flex; */
        margin: 0.25rem;
        border-radius: var(--border-radius);
        justify-content: center;
        align-items: center;
        box-shadow: 2px 2px 8px rgba(0,0,0,.5);
    }

    .data-wrapper.selected-filter{
        grid-template-rows: 7.5rem 0rem auto;
    }
    .data-wrapper.selected-mode{
        grid-template-rows: 0rem 7.5rem auto;
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
            :class="{'selected-mode': selected.mode, 'selected-filter': selected.filter}"
        > 
            <div class="filters">
				<SelectSpecies v-if="selected.filter == 'species'"/>
				<span v-else>{{ capatilizeWord(selected.filter) }}</span>
			</div>
            <div class="modes">{{ capatilizeWord(selected.mode) }}</div>
            <div class="data">Data</div>
        </div>
      </div>
  </template>
  
  <script>
  import { defineComponent } from 'vue'
  import SelectSpecies from "./SelectSpecies.vue";
  export default defineComponent({
	name: "Results",
	components: {
		SelectSpecies
	},
    data() {
		return {
			filters: ["species", "location",  "date", "user"],
			modes: ["table", "map", "chart"],
			selected: {
			filter: null,
			mode: null
			},
			selectedFilter: null,
			selectedMode: null,
		};
    },
    methods: {
		capatilizeWord(str){
			if(!str){
				return ""
			}
			return str.charAt(0).toUpperCase() + str.slice(1)
		},
		filterClass(filter){
			let op = filter
			if(this.selected.filter == filter){
				op += " selected-filter"
			} 
			return op
		},
		modeClass(filter){
			let op = filter
			if(this.selected.mode == filter){
				op += " selected-mode"
			} 
			return op
		},
		select(val, type){
			if(type == "filter"){
				this.selected.mode = null
				this.selected.filter = (this.selected.filter == val) ? null : val
			} else if(type == "mode"){
				this.selected.filter = null
				this.selected.mode = (this.selected.mode == val) ? null : val
			}
		}
    }
  })
  </script>
  