<style scoped>
    .species-select-wrapper{
        height: 100%;
        display: grid;
        grid-template-rows: 1fr 2fr;
        grid-template-columns: auto;
        justify-content: center;
    }
    
    .species-filters{
        display: grid;
        grid-template-rows: 1fr 1fr;
        align-items: center;
        padding: 0rem;
        grid-gap: 0rem;
    }

    .species-filters .ranks{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        padding: 0rem ;
    }
    .species-list{
        height: 100%;
        overflow-y: auto;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.125rem 0.25rem;
        border: 2px solid var(--clr-bg-grey);
    }


    .species-list .search{
        flex: 1 3 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding:  0 0.25rem;
        gap: 1rem;
    }

    .species-filters .search input{
        width: 100%;
        padding: .25rem;
        border: 1px solid var(--clr-bg-green);
        border-radius: .25rem;
    }

    .species-list .chip{
        border-radius: .5rem;
        display: flex;
        overflow: hidden;
        margin:auto;
        transition: all 150ms;
        border: 2px solid transparent;
    }
    .species-list .chip:hover{
        border-color: var(--clr-text-green);
        cursor: pointer;
    }

    .species-list .chip.selected:hover{
        border-color: red;
    }

    .chip .name,
    .chip .common{
        padding: 0.125rem 0.25rem;
        display: flex;
        align-items: center;
    }
    .chip .name{
        background: var(--clr-bg-blue);
        color: white;
        font-size: .8rem;
    }
    .chip .common{
        background: var(--clr-bg-grey);
        font-size: .75rem;
        display: none;
    }

    .chip.selected .name{
        background: var(--clr-bg-green);
    }
    .chip.selected .common{
        color: var(--clr-text-green);
        display: block;
    }


    .btn{
        border: 1px solid var(--clr-bg-blue);
        color: var(--clr-bg-blue);
        padding: .5rem 0.25rem;
        font-size: 0.75rem;
        border-radius: .5rem;
    }


    .btn.selected{
        border: 1px solid transparent;
        color: var(--clr-text-white);
        background: var(--clr-bg-green);
        
    }

    .btn:hover{
        cursor: pointer;
        background: var(--clr-bg-light-blue);
    }

    @media screen and (min-width: 800px) {
        .species-select-wrapper{
            height: 100%;
            border-radius: var(--border-radius);
            padding: .5rem;
            display: grid;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: auto;
            justify-content: space-between;
            align-items: stretch;
        }
        .chip .common{
            display: block;
        }
    }

</style>

<template>
    <div class="species-select-wrapper">
        <div class="species-filters">
            <div class="ranks">
                <button
                    class="btn"
                    v-for="rank in ranks"
                    :key="rank"
                    :class="rankClass(rank)"
                    @click="selectRank(rank)"
                    v-text="rank"
                />
                    
            </div>
            <div class="search">
                <input type="text" v-model="search_string" placeholder="Enter scientific name or common name to filter the list...">
            </div>
        </div>
        <div class="species-list" >
            <div
                class="chip"
                v-for="taxon in filtered_taxa"
                :key="taxon.id"
                :class="{ selected: selected.taxa.indexOf(taxon.id) > -1 }"
                @click="selectTaxa(taxon)"
                :title="taxon.rank"
            >
                <div class="name" v-text="taxon.name"/>
                <div class="common" v-text="taxon.common_name" v-if="taxon.common_name" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from '../store'
export default defineComponent({
    name: "SelectSpecies",
    data(){
        return {
            ranks: [ "order", "superfamily", "family", "subfamily", "tribe", "genus", "species" ],
            selected_ranks: [ "order", "superfamily", "family", "subfamily", "tribe", "genus", "species" ],
            search_string: '',
        }
    },
    computed: {
        ...mapState([
            "taxa",
            "selected"
        ]),
        cleaned_taxa(){
            return this.taxa.filter(taxon => {
                return this.selected_ranks.indexOf(taxon.rank) > -1
            })
        },
        filtered_taxa(){
            return this.cleaned_taxa.filter(taxon => {
                if(this.selected_ranks.indexOf(taxon.rank) != -1){
                    const name_index = taxon.name.toLowerCase().indexOf(this.search_string.toLowerCase())
                    const common_index = taxon.common_name?.toLowerCase().indexOf(this.search_string.toLowerCase())
                    return name_index > -1 || common_index > -1
                }
            }).sort((a,b) => a.name.localeCompare(b.name))
        },
    },
    mounted(){
    },
    created(){
        // console.log(this.taxa)
    },
    methods:{
        rankClass(rank: string){
            return (this.selected_ranks.indexOf(rank) > -1) ? 'selected' : ''
        },
        selectRank(rank:string){
            const index = this.selected_ranks.indexOf(rank)
            if(index > -1){
                this.selected_ranks.splice(index, 1)
            } else {
                this.selected_ranks.push(rank)
            }
        },
        selectTaxa(taxon){
            store.dispatch("selectTaxa", taxon.id)
        },
    }    
})
</script>