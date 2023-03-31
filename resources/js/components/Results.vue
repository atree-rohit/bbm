<style scoped>
    .flex-wrapper{
        height: 95vh;
        display:flex;
        gap: 0.5rem;
        flex-direction: column;
        background: rgba(255, 0, 255, 0.25);
        padding: 0.5rem;
        padding-top:3.75rem;
    }

    .flex-wrapper .filters-wrapper,
    .flex-wrapper .modes-wrapper{
        display:flex;
        border-radius: 1rem;
        overflow: hidden;
    }

    .flex-wrapper .filters-wrapper > div, 
    .flex-wrapper .modes-wrapper > div{
        flex: 1 1 0;
        border: 1px solid rgba(100,100,100,.25);
        box-shadow: .125rem .125rem 0.25rem #bbb;
        transition: all 250ms ease;
        text-align:center;
        padding: .25rem;
        transition: all 500ms;
    }


    .flex-wrapper .data-wrapper{
        flex: 2 1 0;
        display:flex;
        flex-direction: column;
        border: 1.5px solid royalblue;
        border-radius: var(--border-radius);
        box-shadow: 2px 2px 5px #000;
    }

    .flex-wrapper .data-wrapper > div{
        transition: all 300ms cubic-bezier(.1,.6,1,.9);
        display: flex;
        margin: 0.25rem;
        border-radius: var(--border-radius);
        justify-content: center;
        align-items: center;
        box-shadow: 2px 2px 8px rgba(0,0,0,.5);
    }
    .flex-wrapper .data-wrapper .data{
        flex: 5 1 0;
        border: 1px solid red;
    }

    .flex-wrapper .data-wrapper .modes,
    .flex-wrapper .data-wrapper .filters{
        flex: 0 2 0;
    }

    .flex-wrapper .data-wrapper.selected-mode .modes,
    .flex-wrapper .data-wrapper.selected-filter .filters{
        flex: 1 1 0;
    }


    .flex-wrapper .filters-wrapper > div.selected-filter,
    .flex-wrapper .modes-wrapper > div.selected-mode{
        flex: 3 1 0;
        background: var(--clr-bg-green);
        /* color: white; */
    }

    .flex-wrapper .filters-wrapper > div:not(.selected-filter),
    .flex-wrapper .modes-wrapper > div:not(.selected-mode){
        flex: 1 3 0;
        background: var(--clr-bg-blue);
    }

    .flex-wrapper .filters-wrapper > div:hover,
    .flex-wrapper .modes-wrapper > div:hover{
        background:var(--clr-bg-yellow);
        cursor: pointer;
    }

    .flex-wrapper .filters-wrapper > div.selected-filter:hover,
    .flex-wrapper .modes-wrapper > div.selected-mode:hover{
        color:var(--clr-text-blue);
        font-weight: 700;
    }
</style>

<template>
    <div class="flex-wrapper" >
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
            <div class="filters">{{ capatilizeWord(selected.filter) }}</div>
            <div class="modes">{{ capatilizeWord(selected.mode) }}</div>
            <div class="data">Data</div>
        </div>
      </div>
  </template>
  
  <script>
  export default {
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
  };
  </script>
  