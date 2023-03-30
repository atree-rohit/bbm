import { createStore } from 'vuex'
import axois from 'axios'

export default createStore({
    state:{
        taxa: [],
        users: [],
        observations: {},
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
    },
    getters:{
    }
})