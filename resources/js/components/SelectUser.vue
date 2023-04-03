<style scoped>
    .users-select-wrapper{
        height: 100%;
        display: grid;
        grid-template-rows: 1fr 2fr;
        grid-template-columns: auto;
        justify-content: center;
    }
    .user-filters{
        display: grid;
        grid-template-rows: 1fr 1fr;
        align-items: center;
        padding: 0rem;
        grid-gap: 0rem;
        min-width: 90vw;
    }

    .portals{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: center;
        padding: 0rem;
    }
    .search input{
        width: 100%;
    padding: 0.25rem;
    border: 1px solid var(--clr-bg-green);
    border-radius: 0.25rem;
    }
    .users-list{
        border: 1px solid var(--clr-bg-grey);
        border-radius: var(--border-radius);
        overflow-y: auto;
        padding: .25rem .5rem;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.25rem 0.5rem;
        width: 100%;
    }

    .users-list .chip{
        border-radius: .5rem;
        display: flex;
        overflow: hidden;
        margin:auto;
        transition: all 150ms;
        border: 2px solid transparent;
    }
    .users-list .chip:hover{
        border-color: var(--clr-text-green);
        cursor: pointer;
    }

    .users-list .chip.selected:hover{
        border-color: red;
    }

    .users-list .chip .portal,
    .users-list .chip .name{
        padding: 0.125rem 0.25rem;
        display: flex;
        align-items: center;
    }
    .users-list .chip .portal{
        background: var(--clr-bg-blue);
        color: white;
        font-size: .8rem;
    }
    .users-list .chip .name{
        background: var(--clr-bg-grey);
        font-size: .75rem;
    }

    .users-list .chip.selected .portal{
        background: var(--clr-bg-green);
    }
    .users-list .chip.selected .name{
        color: var(--clr-text-green);
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
        .users-select-wrapper{
            border-radius: var(--border-radius);
            padding: .5rem;
            grid-template-columns: 1fr 4fr;
            grid-template-rows: auto;
            justify-content: space-between;
        }
        .user-filters{
            min-width: 19vw;
        }
    }

</style>

<template>
    <div class="users-select-wrapper">
        <div class="user-filters">
            <div class="portals">
                <button
                    class="btn"
                    v-for="portal in portals"
                    :key="portal.id"
                    :class="portalClass(portal.id)"
                    @click="selectPortal(portal)"
                    v-text="portal.id"
                />
            </div>
            <div class="search">
                <input type="text" v-model="search_string" placeholder="Enter User Name to filter the list...">
            </div>
        </div>
        <div class="users-list">
            <div
                class="chip"
                v-for="user in filtered_users"
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
            selected_portals: [],
            search_string: ""
        }
    },
    created(){
        this.selected_portals = this.portals.map((p) => p.id)
    },
    computed:{
        ...mapState([
            "users",
            "selected"
        ]),
        filtered_users(){
            return this.users.filter((user) => {
                const portal_index = this.selected_portals.indexOf(user.source)
                const search_index = user.name.toLowerCase().indexOf(this.search_string.toLowerCase())
                return (portal_index > -1 && search_index > -1)
            })
        }
    },
    methods:{
        portalClass(portal: string){
            return (this.selected_portals.indexOf(portal) > -1) ? 'selected' : ''
        },
        selectPortal(portal: any){
            if(this.selected_portals.indexOf(portal.id) > -1){
                this.selected_portals = this.selected_portals.filter((p) => p != portal.id)
            }else{
                this.selected_portals.push(portal.id)
            }
        },
        selectUser(user: any){
            store.dispatch("selectUser", user.id)
        }
    }
})
</script>