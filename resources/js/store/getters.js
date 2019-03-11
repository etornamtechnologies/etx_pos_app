

const getters = {
    is_sidebar_closed: state => state.app.sidebar.closed ? true : false,
    user: state => state.user,
    token: state => state.user.token,
    user_name: state=> `${state.user.first_name} ${state.user.last_name}`,
    user_username: state=> state.user.username,
    user_roles: state=> state.user.roles,
}

export default getters;