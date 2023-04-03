<style scoped>
    .date-select-wrapper{
        height: 100%;
        display: grid;
        grid-template-rows: 1fr 2fr;
        grid-template-columns: auto;
        justify-content: center;
    }
    .date-filters{
        display: grid;
        /* grid-template-rows: 1fr 1fr; */
        align-items: center;
        padding: 0rem;
        grid-gap: 0rem;
    }

    @media screen and (min-width: 800px) {
        .date-select-wrapper{
            border-radius: var(--border-radius);
            padding: .5rem;
            grid-template-columns: 1fr 3fr;
            grid-template-rows: auto;
            justify-content: space-between;
        }
    }
</style>

<template>
    <div class="date-select-wrapper toggle-search-list">
        <div class="date-filters">
            <div class="toggle-btns">
                <button
                    class="toggle-btn"
                    v-for="filter in date_filters"
                    :key="filter"
                    v-text="filter"
                    :class="{selected: selected_date_filter == filter}"
                    @click="selected_date_filter = (selected_date_filter == filter) ? '' : filter"
                />
            </div>
        </div>
        <div class="list" >
            <div
                class="chip"
                v-for="date in filtered_dates"
                :key="date.name"
                :title="date.type"
                :class="{ selected: (selected.date.year.indexOf(date.name) > -1 || selected.date.day.indexOf(date.name) > -1) }"
                @click="selectDate(date)"
            >
                <div class="type hidden" v-text="date.type"/>
                <div class="name" v-text="date.name" v-if="date.name" />
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
            date_filters: ["Year", "Weeks", "Day of the Week"],
            selected_date_filter: "",
        }
    },
    computed:{
        ...mapState([
            "observations",
            "selected"
        ]),
        filtered_dates(): {name: string, type: string} {
            let op: {name: string, type: string}[] = []
            const data = Object.values(this.observations).flat()

            switch (this.selected_date_filter) {
                case "Year":
                    const data1 = [...new Set(data.map((d) => d.date))]
                    let years = [...new Set(data1.map((d) => d.split("-")[2]))]
                    years.map((y) => {
                        op.push({
                            name: y,
                            type: "Year",
                        })
                    })
                    op.sort((a,b) => {
                        return parseInt(a.name) - parseInt(b.name)
                    })
                    break

                case "Day of the Week":
                    const days = [...new Set(data.map((d) => new Date(d.date).getDay()))]
                    const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
                    days.map((d) => {
                        if(d >= 0){
                            op.push({
                                name: daysOfWeek[d],
                                type: "Day of the Week",
                            })
                        }
                    })
                    op.sort((a,b) => {
                        const indexA = daysOfWeek.indexOf(a.name)
                        const indexB = daysOfWeek.indexOf(b.name)
                        return indexA - indexB
                    })
                    break
            }
            return op
        },
    },
    methods:{
        selectDate(date){
            if(date.type == "Year"){
                store.dispatch("selectDateYear", date.name)
            } else if(date.type == "Day of the Week"){
                store.dispatch("selectDateDay", date.name)
            }
        }
    }

})
</script>