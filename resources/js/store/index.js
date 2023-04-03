import { createStore } from 'vuex'
import axois from 'axios'

export default createStore({
    state:{
        pages: ["Home", "About", "FAQ", "Videos", "Past Results", "Partners"],
        selected_page: "Home",
        taxa: [],
        users: [],
        observations: {},
        filtered_observations:[],
        selected: {
            portals:["counts", "inat", "ibp", "ifb"],
            taxa: [],
            location: {
                state: [],
                district: [],
                bounding_box: []
            },
            date: {
                year: [],
                day: [],
            },
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
        },
        SET_SELECTED_STATE(state, state_name){
            const index = state.selected.location.state.indexOf(state_name)
            if(index > -1){
                state.selected.location.state.splice(index, 1)
            } else {
                state.selected.location.state.push(state_name)
            }
        },
        SET_SELECTED_DISTRICT(state, district_name){
            const index = state.selected.location.district.indexOf(district_name)
            if(index > -1){
                state.selected.location.district.splice(index, 1)
            } else {
                state.selected.location.district.push(district_name)
            }
        },
        SET_SELECTED_YEAR(state, year){
            const index = state.selected.date.year.indexOf(year)
            if(index > -1){
                state.selected.date.year.splice(index, 1)
            } else {
                state.selected.date.year.push(year)
            }
        },
        SET_SELECTED_DAY(state, day){
            const index = state.selected.date.day.indexOf(day)
            if(index > -1){
                state.selected.date.day.splice(index, 1)
            } else {
                state.selected.date.day.push(day)
            }
        },
        SET_FILTERED_OBSERVATIONS(state){
            state.filtered_observations = Object.values(state.observations).flat()
            if(state.selected.portals.length > 0 && state.selected.portals.length < 4){
                state.filtered_observations = []
                state.selected.portals.forEach((p) => {
                    state.filtered_observations = state.filtered_observations.concat(state.observations[p])
                })
            }
            if(state.selected.taxa.length > 0){
                state.filtered_observations = state.filtered_observations.filter(observation => state.selected.taxa.includes(observation.taxa_id))
            }
            if(state.selected.location.state.length > 0){
                state.filtered_observations = state.filtered_observations.filter(observation => state.selected.location.state.includes(observation.state))
            }
            if(state.selected.location.district.length > 0){
                state.filtered_observations = state.filtered_observations.filter(observation => state.selected.location.district.includes(observation.district))
            }
            if(state.selected.date.year.length > 0){
                state.filtered_observations = state.filtered_observations.filter((observation) => state.selected.date.year.includes(observation.date.split("-")[2]))
            }
            if(state.selected.date.day.length > 0){
                const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
                state.filtered_observations = state.filtered_observations.filter((observation) => {
                    const [day, month, year] = observation.date.split("-").map(Number)
                    let date = new Date(year, month-1, day).getDay()
                    return state.selected.date.day.includes(daysOfWeek[date])
                })
            }
            if(state.selected.users.length > 0){
                state.filtered_observations = state.filtered_observations.filter((observation) => state.selected.users.includes(observation.user_id))
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
                    commit('SET_FILTERED_OBSERVATIONS')
                })
        },
        gotoPage({commit}, payload){
            commit('SET_SELECTED_PAGE', payload)
        },
        selectTaxa({commit}, payload){
            commit('SET_SELECTED_TAXA', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
        selectState({commit}, payload){
            commit('SET_SELECTED_STATE', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
        selectDistrict({commit}, payload){
            commit('SET_SELECTED_DISTRICT', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
        selectDateYear({commit}, payload){
            commit('SET_SELECTED_YEAR', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
        selectDateDay({commit}, payload){
            commit('SET_SELECTED_DAY', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
        selectUser({commit}, payload){
            commit('SET_SELECTED_USER', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
        selectPortal({commit}, payload){
            commit('SET_SELECTED_PORTAL', payload)
            commit('SET_FILTERED_OBSERVATIONS')
        },
    },
    getters:{
    }
})