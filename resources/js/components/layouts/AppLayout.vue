<template>
    <v-app>
        <div class="app">
            <div class="left-side">
                <sidebar></sidebar>
            </div>
            <div class="main-container">
                <header-component></header-component>
                <div class="main" style="overflow-y:auto; padding:5px 5px 5px 5px">
                    <v-container fluid fill-height>
                        <router-view></router-view>
                    </v-container>
                </div>
            </div>
        </div>
    </v-app>
</template>

<script>
    import Header from "../../components/core/Header";
    import Sidebar from "../../components/core/SideBar"
    import { GetUserInfo } from '../../utils/user'

    export default {
        name: "App",
        components: {
            sidebar: Sidebar,
            'header-component': Header,
        },
        created() {
            this.getUserInfo();
        },
        data() {
            return {
            //
            };
        },
        methods: {
            getUserInfo: function() {
                    GetUserInfo({})
                        .then(result=> {
                            if(result.code == 0) {
                                let user = result.user;
                                let token = user.api_token || "";
                                let userRoles = user.roles || [];
                                let roles = userRoles.map(r=> {
                                    return r.label;
                                }) || [];
                                this.$store.dispatch('setUser', user);
                                this.$store.dispatch('setRoles', roles);
                            }
                        })
                        .catch(err=> {

                        })
                }
        },
        computed: {
            sideBarOpened: function() {
                return this.$store.state.app.sidebar.opened
            }
        }
    };
</script>
<style scoped>
    .app {
        width: 100%;
        height: 100vh;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: flex-start;
    }
    .left-side {
        height: 100%;
        
    }
    .main-container {
        height: 100%;
        width: 100%;
        overflow-y: auto;
    }
</style>