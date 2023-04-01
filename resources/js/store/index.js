import { createStore } from 'vuex'
import axois from 'axios'

export default createStore({
    state:{
        pages: ["Home", "About", "FAQ", "Videos", "Past Results", "Partners"],
        selected_page: "Home",
        taxa: [],
        users: [],
        observations: {},
        selected: {
            portals:["count", "inat", "ibp", "ifb"],
            taxa: [],
            location: null,
            date: null,
            users: [],
        }
    },
    mutations:{
        SET_TAXA(state, payload){
            state.taxa = payload
        },
        SET_USERS(state, payload){
            state.users = payload
        },
        SET_OBSERVATIONS(state, payload){
            state.observations = payload
        },
        SET_SELECTED_PAGE(state, payload){
            state.selected_page = payload
        },
        SET_SELECTED_TAXA(state, taxon){
            const index = state.selected.taxa.indexOf(taxon)
            if(index > -1){
                state.selected.taxa.splice(index, 1)
            } else {
                state.selected.taxa.push(taxon)
            }
        },
        SET_SELECTED_PORTAL(state, portal){
            const index = state.selected.portals.indexOf(portal)
            if(index > -1){
                state.selected.portals.splice(index, 1)
            } else {
                state.selected.portals.push(portal)
            }
        },
        SET_SELECTED_USER(state, user){
            const index = state.selected.users.indexOf(user)
            if(index > -1){
                state.selected.users.splice(index, 1)
            } else {
                state.selected.users.push(user)
            }
        }
    },
    actions:{
        getAllData({commit}){
            axois.get('/api/taxa')
                .then(response => {
                    commit('SET_TAXA', response.data)
                    console.info("Taxa set")
                })
            axois.get('/api/users')
                .then(response => {
                    commit('SET_USERS', response.data)
                    console.info("Users set")
                })
            axois.get('/api/observations')
                .then(response => {
                    commit('SET_OBSERVATIONS', response.data)
                    console.info("Observations set")
                })
        },
        gotoPage({commit}, payload){
            commit('SET_SELECTED_PAGE', payload)
        },
        selectTaxa({commit}, payload){
            commit('SET_SELECTED_TAXA', payload)
        },
        selectUser({commit}, payload){
            commit('SET_SELECTED_USER', payload)
        },
        selectPortal({commit}, payload){
            commit('SET_SELECTED_PORTAL', payload)
        }
    },
    getters:{
    }
})