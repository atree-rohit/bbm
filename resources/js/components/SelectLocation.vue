<style scoped>
    .location-select-wrapper{
        height: 100%;
        display: grid;
        grid-template-rows: 1fr 2fr;
        grid-template-columns: auto;
        justify-content: center;
    }
    .location-filters{
        display: grid;
        /* grid-template-rows: 1fr 1fr; */
        align-items: center;
        padding: 0rem;
        grid-gap: 0rem;
    }

    @media screen and (min-width: 800px) {
        .location-select-wrapper{
            border-radius: var(--border-radius);
            padding: .5rem;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: auto;
            justify-content: space-between;
        }
    }
</style>

<template>
    <div class="location-select-wrapper toggle-search-list">
        <div class="location-filters">
            <div class="toggle-btns">
                <button
                    class="toggle-btn"
                    v-for="filter in location_filters"
                    :key="filter"
                    v-text="filter"
                    :class="{selected: selected_location_filter == filter}"
                    @click="selected_location_filter = (selected_location_filter == filter) ? '' : filter"
                />
            </div>
        </div>
        <div class="list" >
            <div
                class="chip"
                v-for="location in filtered_locations"
                :key="location.id"
                :class="{ selected: (selected.location.state.indexOf(location.name) > -1 || selected.location.district.indexOf(location.name) > -1) }"
                @click="selectLocation(location)"
                :title="(location.type == 'Di') ? 'District' : 'State'"
                >
                <div class="type hidden" v-text="location.type"/>
                <div class="name" v-text="location.name" v-if="location.name" />
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from '../store'
export default defineComponent({
    name: "SelectDate",
    data(){
        return {
            location_filters: ["State", "District", "Bounding Box"],
            selected_location_filter: "",
            search_string: "",
        }
    },
    computed:{
        ...mapState([
            "observations", 
            "selected",
        ]),
        filtered_locations(): {type: string, name: string}[] {
            const op: {type: string, name: string}[] = []
            const data = Object.values(this.observations).flat()

            if(this.selected_location_filter == "District" || this.selected_location_filter == ""){
                const districts = [...new Set(data.map((o) => o.district))]
                districts.forEach((d) => {
                    op.push({
                        type: "Di",
                        name: d,
                    })
                })                
            }
            if(this.selected_location_filter == "State" || this.selected_location_filter == ""){
                const states = [...new Set(data.map((o) => o.state))]
                states.forEach((s) => {
                    op.push({
                        type: "St",
                        name: s,
                    })
                })
            }
            if(this.selected_location_filter == "Bounding Box"){
                op.push({
                    type: "BB",
                    name: "Bounding Box",
                })
            }

            return op.sort((a, b) => a.name.localeCompare(b.name))
        },
    },
    mounted(){
        console.log(this.filtered_locations)
    },
    methods:{
        selectLocation(location){
            if(location.type == "St"){
                store.dispatch("selectState", location.name)
            } else if(location.type == "Di"){
                store.dispatch("selectDistrict", location.name)
            }
        }
    }

})
</script>