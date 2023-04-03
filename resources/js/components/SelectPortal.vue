<style scoped>
.portals-wrapper{
    height: 100%;
    display:flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 0 1rem;
}

.portals-wrapper .btn{
    border: 1px solid red;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem 1rem;
    transition: all 250ms;
}

.portals-wrapper .btn.selected{
    border: 1px solid var(--clr-bg-green);
    background: var(--clr-bg-green);
    color: var(--clr-bg-grey);
    border-radius: 1rem 0.5rem;
}

.portals-wrapper .btn:hover{
    cursor:pointer;
    border-radius: 0.5rem 0.5rem;
}

</style>

<template>
    <div class="portals-wrapper">
        <button
            class="btn"
            v-for="portal in portals"
            :key="portal.id"
            v-text="portal.name"
            :class="{selected: selected.portals.indexOf(portal.id) > -1}"
            @click="selectPortal(portal)"
        />
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from '../store'

export default defineComponent({
    name: "SelectPortal",
    data(){
        return{
            portals: [
                {
                    id: "counts",
                    name: "Big Butterfly Month Counts"
                },
                {
                    id: "inat",
                    name: "iNaturalist.org"
                },
                {
                    id: "ibp",
                    name: "India Biodiversity Portal"
                },
                {
                    id: "ifb",
                    name: "iFoundButterflies"
                }
            ]
        }
    },
    computed:{
        ...mapState(["selected"])
    },
    methods:{
        selectPortal(p){
            store.dispatch("selectPortal", p.id)
        }
    }
})
</script>