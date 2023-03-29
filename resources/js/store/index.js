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
                })
            axois.get('/api/users')
                .then(response => {
                    commit('SET_USERS', response.data)
                })
            axois.get('/api/observations')
                .then(response => {
                    console.log(response)
                    commit('SET_OBSERVATIONS', response.data)
                })
        },
    },
    getters:{
    }
})