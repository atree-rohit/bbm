<style scoped>
    .users-wrapper{
        height: 100%;
        display: grid;
        grid-template-columns: 1fr 3fr;
        gap: 1rem;
    }
    .users-wrapper .filters{
        border: 1px solid var(--clr-bg-grey);
        border-radius: var(--border-radius);
        padding: .5rem;
        display: flex;
        flex-direction: column;
    }

    .filters .portals, .filters .search{
        flex: 1 1 0;

    }
    .users-wrapper .users{
        border: 1px solid var(--clr-bg-grey);
        border-radius: var(--border-radius);
        overflow-y: auto;
        padding: .25rem .5rem;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.25rem 0.5rem;
    }

    .users .chip{
        border-radius: .5rem;
        display: flex;
        overflow: hidden;
        margin:auto;
        transition: all 150ms;
        border: 2px solid transparent;
    }
    .users .chip:hover{
        border-color: var(--clr-text-green);
        cursor: pointer;
    }

    .users .chip.selected:hover{
        border-color: red;
    }

    .users .chip .portal,
    .users .chip .name{
        padding: 0.125rem 0.25rem;
        display: flex;
        align-items: center;
    }
    .users .chip .portal{
        background: var(--clr-bg-blue);
        color: white;
        font-size: .8rem;
    }
    .users .chip .name{
        background: var(--clr-bg-grey);
        font-size: .75rem;
    }

    .users .chip.selected .portal{
        background: var(--clr-bg-green);
    }
    .users .chip.selected .name{
        color: var(--clr-text-green);
    }

</style>

<template>
    <div class="users-wrapper">
        <div class="filters">
            <div class="portals">Portals</div>
            <div class="search">
                <input type="text" v-model="search_string">
            </div>
        </div>
        <div class="users">
            <div
                class="chip"
                v-for="user in users"
                :key="user.id"
                :class="{ selected: selected.users.indexOf(user.id) > -1 }"
                @click="selectUser(user)"
                :title="user.rank"
            >
                <div class="portal" v-text="user.source"/>
                <div class="name" v-text="user.name"/>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { mapState } from 'vuex'
import store from '../store'

export default defineComponent({
    name: "SelectUser",
    data(){
        return{
            portals: [
                {
                    id: "count",
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
            ],
            selected_portals: ["count", "inat", "ibp", "ifb"]
        }
    },
    computed:{
        ...mapState([
            "users",
            "selected"
        ])
    },
    methods:{
        selectUser(user: any){
            store.dispatch("selectUser", user.id)
        }
    }
})
</script>