import { getApiToken, setApiToken, removeApiToken } from '../../utils/auth'
import { loginUser, logOutUser, registerUser} from '../../api/login'

const user = {
    state: {
        first_name: "etornam",
        middle_name: 'kojo',
        last_name: 'anyidoho',
        username: "etoretornam",
        roles: ['admin', 'user'],
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
        SET_FIRST_NAME: (state, first_name)=> {
            state.first_name = first_name;
        },
        SET_MIDDLE_NAME: (state, middle_name)=> {
            state.middle_name = middle_name;
        },
        SET_LAST_NAME: (state, last_name)=> {
            state.last_name = last_name;
        },
        SET_ROLES: (state, roles)=> {
            state.roles = roles;
        },
        SET_PERMISSIONS: (state, permissions)=> {
            state.permissions = permissions;
        },
        SET_AVATAR: (state, avatar)=> {
            state.avatar = avatar;
        },
        SET_USERNAME: (state, username)=> {
            state.username = username;
        }
    },
    actions: {
        LoginUser({ commit }, userInfo) {
            const username = userInfo.username.trim()
            return new Promise((resolve, reject) => {
                loginUser(username, userInfo.password).then(response => {
                    const data = response || {}
                    const token = data.user.api_token;
                    commit('SET_TOKEN', token)
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
                    console.log(token);
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
                    console.log('logiut_data')
                    commit('SET_TOKEN', '')
                    commit('SET_ROLES', [])
                    removeApiToken()
                    console.log('token', getApiToken())
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
    }
}

export default user;