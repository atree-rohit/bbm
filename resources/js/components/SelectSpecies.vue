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

    @media screen and (min-width: 800px) {
        .species-select-wrapper{
            border-radius: var(--border-radius);
            padding: .5rem;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: auto;
            justify-content: space-between;
        }
    }

</style>

<template>
    <div class="species-select-wrapper toggle-search-list">
        <div class="species-filters">
            <div class="toggle-btns">
                <button
                    class="toggle-btn"
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
        <div class="list" >
            <div
                class="chip"
                v-for="taxon in filtered_taxa"
                :key="taxon.id"
                :class="{ selected: selected.taxa.indexOf(taxon.id) > -1 }"
                @click="selectTaxa(taxon)"
                :title="taxon.rank"
            >
                <div class="name" v-text="taxon.name"/>
                <div class="common hidden" v-text="taxon.common_name" v-if="taxon.common_name" />
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