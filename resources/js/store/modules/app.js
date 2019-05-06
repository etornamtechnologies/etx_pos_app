const SIDEBAR_CLOSE = 'sidebar_close';

const app = {
    state: {
        sidebar: {
            closed: JSON.parse(localStorage.getItem(SIDEBAR_CLOSE) || false) || false,
        },
        app_search: "",
    },
    mutations: {
        TOGGLE_SIDEBAR: state => {
            state.sidebar.closed = !state.sidebar.closed;
        },
        OPEN_SIDEBAR: state => {
            state.sidebar.closed = false;
        },
        CLOSE_SIDEBAR: state => {
            state.sidebar.closed = true;
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
            localStorage.setItem(SIDEBAR_CLOSE, true);
            context.commit('CLOSE_SIDEBAR');
        },
        openSidebar(context) {
            localStorage.setItem(SIDEBAR_CLOSE, false);
            context.commit('OPEN_SIDEBAR');
        } 
    }
}

export default app;