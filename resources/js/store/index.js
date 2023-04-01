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
            source: null,
            taxa: [],
            location: null,
            date: null,
            user: null,
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
        }
    },
    getters:{
    }
})