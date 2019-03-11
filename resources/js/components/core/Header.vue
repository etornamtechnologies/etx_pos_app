<template>
    <div class="nav-header">
        <div class="navheader-left">
            <v-icon @click.stop="openSideBar" v-if="isSidebarClosed">menu</v-icon>
            <v-icon @click.stop="closeSideBar" v-if="!isSidebarClosed">chevron_left</v-icon>
        </div>
        <div class="navheader-middle">
            <div style="width:250px">
            </div>
        </div>
        <div class="navheader-right">
            <div class="navbar-item" style="display:flex; flex-direction:row; align-items:center; justify-content:center">
                <span style="margin-right:2px">{{ username }}</span>
                <v-avatar
                size="20"
                >
                    <img src="/img/icons/man.svg" alt="image">
                </v-avatar>
            </div>
            <v-badge overlap color="info" class="navbar-item">
                <span slot="badge">3</span>
                <v-avatar size="20"
                    color="purple red--after"
                >
                    <v-icon 
                    @click="openNotificationMenu" 
                    dark>notifications</v-icon>
                </v-avatar>
            </v-badge>  
            <v-img
            src="/img/icons/settings.svg"
            class="my-3 navbar-item"
            color="primary"
            style="cursor:pointer"
            contain
            height="20"
            max-width="20"
            ></v-img>   
            <v-img
            src="/img/icons/logout.svg"
            height="20"
            style="cursor:pointer"
            max-width="20"
            class="navbar-item"
            @click="handleLogOut"></v-img>
        </div>
    </div>
</template>

<script>
export default{
    data() {
        return {
            
        }
    },
    mounted() {
        
    }, 
    methods: {
        openSettings: function(){
            console.log('settings')
        },
        openNotificationMenu: function() {
            console.log('notification');
        },
        logoutUser: function(){
            console.log('logout')
        },
        toggleSideBar: function() {
            this.$store.dispatch('toggleSidebar').app;
        },
        closeSideBar: function() {
            this.$store.dispatch('closeSidebar').app;
        },
        openSideBar: function() {
            this.$store.dispatch('openSidebar').app;
        },
        openNotificationMenu: function() {
            console.log('notification menu')
        },
        handleLogOut: function(){
            this.$store.dispatch('LogOutUser')
                .then((result)=> {
                    this.$router.push({ path: '/account/login' })
                })
                .catch((err)=> {
                    console.log(err)
                })
        },
    },
    computed: {
        userName: function() {
            let user = this.$store.state.user.user || {};
            return user.username;
        },
        isSidebarClosed: function() {
            return this.$store.getters.is_sidebar_closed
        },
        username: function() {
            return this.$store.getters.user_username;
        },
    }
}
</script>

<style scoped>
    .nav-header {
        height: 60px;
        background-color:#ffffff;
        -webkit-box-shadow: 0px 5px 22px -3px rgba(103,103,103,0.74); 
        box-shadow: 0px 5px 22px -3px rgba(103,103,103,0.74);
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        padding: 5px;
    }
    .navheader-left {
        height: 100%;
        flex-grow: 1;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: flex-start;
        align-items: center;
    }
    .navheader-middle {
        height: 100%;
        flex-grow: 4;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;        
    }
    .navheader-right  {
        height: 100%;
        flex-grow: 5;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: flex-end;
        align-items: center;
        padding-right: 10px;        
    }
    .navbar-item {
        margin-left: 15px;
    }
    .nav-header-right {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: flex-end;
        align-items: center;

    }
    .nav-header-right .item {
        margin-left: 15px
    }

    .nav-header-right .item .avatar {
        width: 25px;
        height: 25px;
    }
</style>
