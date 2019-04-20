import { getApiToken, setApiToken, removeApiToken } from '../../utils/auth'
import { loginUser, logOutUser, registerUser} from '../../api/login'

const user = {
    state: {
        user: {name:''},
        name: '',
        username: "",
        roles: [],
        token: getApiToken(),
        roles: [],
        permissions: [],
        avatar: '',
        auth: false
    },
    mutations: {
        SET_TOKEN: (state, token)=> {
            state.token = token;
        },
        SET_USER: (state, user_data)=> {
            state.user = user_data;
        },
        SET_NAME: (state, name)=> {
            state.name = name;
        },
        SET_USERNAME: (state, name)=> {
            state.username = name;
        },
        SET_ROLES: (state, roles)=> {
            state.roles = roles;
        },
        SET_AVATAR: (state, avatar)=> {
            state.avatar = avatar;
        },
    },
    actions: {
        LoginUser({ commit }, userInfo) {
            const username = userInfo.username.trim()
            return new Promise((resolve, reject) => {
                loginUser(username, userInfo.password).then(response => {
                    const data = response || {}
                    const token = data.user.api_token;
                    commit('SET_TOKEN', token)
                    commit('SET_USER', data.user)
                    let userRoles = data.user.roles || []
                    let roles = userRoles.map((r)=> {
                        return r.id;
                    }) || [];
                    commit('SET_ROLES', roles);
                    setApiToken(token)
                    resolve(data)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        RegisterNewUser({ commit }, userInfo) {
            const username = userInfo.username.trim()
            return new Promise((resolve, reject) => {
                registerUser(userInfo.name, userInfo.username, userInfo.phone, userInfo.email,userInfo.password)
                .then(response => {
                    const data = response || {}
                    const userData = data.user || {};
                    const token = data.token;
                    commit('SET_TOKEN', token)
                    commit('SET_USERNAME', userData.username)
                    setApiToken(token)
                    resolve(data)
                }).catch(error => {
                    reject(error)
                })
            })
        },        
        //logout user
        LogOutUser({ commit, state }) {
            return new Promise((resolve, reject) => {
                logOutUser(state.token).then(() => {
                    commit('SET_TOKEN', '')
                    commit('SET_ROLES', [])
                    removeApiToken()
                    resolve()
                }).catch(error => {
                    reject(error)
                })
            })
        },
        FedLogOut({ commit }) {
            return new Promise(resolve => {
                commit('SET_TOKEN', '')
                removeApiToken()
                resolve()
                this.$router.psuh('account/login')
            })
        },
        setUser({commit}, userData) {
            return new Promise(resolve=> {
                commit('SET_NAME', userData.name)
                commit('SET_USERNAME', userData.username)
                commit('SET_USER', userData);
            })
        },
        setRoles({commit}, roles) {
            return new Promise(resolve=> {
                commit('SET_ROLES', roles);
            })
        }
    }
}

export default user;