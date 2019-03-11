const app = {
    state: {
        sidebar: {
            closed: false,
        },
        app_search: "",
    },
    mutations: {
        TOGGLE_SIDEBAR: state => {
            state.sidebar.closed = !state.sidebar.closed;
        },
        OPEN_SIDEBAR: state => {
            state.sidebar.closed = false;
            console.log(state.sidebar.closed);
        },
        CLOSE_SIDEBAR: state => {
            state.sidebar.closed = true;
            console.log(state.sidebar.closed);
        },
        SET_APP_SEARCH: (state, value) => {
            state.app_search = value;
        }
    },
    actions: {
        toggleSidebar(context) {
            context.commit('TOGGLE_SIDEBAR');
        },
        closeSidebar(context) {
            context.commit('CLOSE_SIDEBAR');
        },
        openSidebar(context) {
            context.commit('OPEN_SIDEBAR');
        } 
    }
}

export default app;