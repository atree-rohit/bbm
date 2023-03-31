<style scoped>
    .species-select-wrapper{
        background: salmon;
        width:100%;
        height: 100%;
        border-radius: var(--border-radius);
        padding: .5rem;
        display: flex;
        justify-content: space-between;
        align-items: stretch;
    }
    .species-select-wrapper .ranks{
        display: flex;
        align-items: center;
        white-space: nowrap;
        padding: 0 1rem ;
        gap: .125rem;
    }
    
    .species-select-wrapper .ranks,
    .species-select-wrapper .search,
    .species-select-wrapper .list{
        border: 1px solid red;
    }

    .species-select-wrapper .search{
        flex: 1 3 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding:  0 0.25rem;
        gap: 1rem;
    }
    .species-select-wrapper .list{
        flex: 3 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        padding: .25rem .5rem;
        height: 2rem;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.25rem 0.5rem;
    }

    .species-select-wrapper .list .chip{
        border-radius: .5rem;
        display: flex;
        overflow: hidden;
        transition: all 150ms;
        border: 2px solid transparent;
    }
    .species-select-wrapper .list .chip:hover{
        border-color: var(--clr-text-green);
        cursor: pointer;
    }

    .species-select-wrapper .list .chip.selected:hover{
        border-color: red;
    }

    .species-select-wrapper .list .chip .name,
    .species-select-wrapper .list .chip .common{
        padding: 0.125rem 0.25rem;
        display: flex;
        align-items: center;
    }
    .species-select-wrapper .list .chip .name{
        background: var(--clr-bg-blue);
        color: white;
        font-size: .8rem;
    }
    .species-select-wrapper .list .chip .common{
        background: var(--clr-bg-grey);
        font-size: .75rem;
    }

    .species-select-wrapper .list .chip.selected .name{
        background: var(--clr-bg-green);
    }
    .species-select-wrapper .list .chip.selected .common{
        color: var(--clr-text-green);
    }

    .species-select-wrapper .search input{
        width: 100%;
        padding: .25rem;
        border: 1px solid var(--clr-bg-green);
        border-radius: .25rem;
    }

    .btn{
        border: 1px solid var(--clr-bg-green);
        padding: .5rem 0.25rem;
        border-radius: .25rem;
    }


    .btn.selected{
        border: 1px solid var(--clr-text-green);
        background: var(--clr-bg-green);
        
    }

    .btn:hover{
        cursor: pointer;
        background: var(--clr-bg-light-blue);
    }

</style>

<template>
    <div class="species-select-wrapper">
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
            {{ search_string }}
            <input type="text" v-model="search_string">
        </div>
        <div class="list" :style="{ height: listHeight + 'px' }">
            <div
                class="chip"
                v-for="taxon in filtered_taxa"
                :key="taxon.id"
                :class="{ selected: selected_taxa.indexOf(taxon) > -1 }"
                @click="selectTaxa(taxon)"
                :title="taxon.rank"
            >
                <div class="name" v-text="taxon.name"/>
                <div class="common" v-text="taxon.common_name"/>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
export default defineComponent({
    name: "SelectSpecies",
    data(){
        return {
            ranks: [ "order", "superfamily", "family", "subfamily", "tribe", "genus", "species" ],
            selected_ranks: [ "order", "superfamily", "family", "subfamily", "tribe", "genus", "species" ],
            search_string: '',
            selected_taxa: [],
        }
    },
    computed: {
        ...mapState([
            "taxa"
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
            })
        },
        listHeight(){
            const searchHeight = document.querySelector('.search')
            console.log(searchHeight)
            return searchHeight.offsetHeight + 'px';
        }
    },
    mounted(){
        console.log(this.listHeight)
    },
    created(){
        console.log(this.taxa)
        // console.log(this.listHeight)
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
            const index = this.selected_taxa.indexOf(taxon)
            if(index > -1){
                this.selected_taxa.splice(index, 1)
            } else {
                this.selected_taxa.push(taxon)
            }
        },
    }    
})
</script>