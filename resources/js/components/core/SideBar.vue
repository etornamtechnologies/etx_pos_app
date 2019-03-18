<template>
<div class="bg-menu">
    <el-menu
    style="height:100vh !important"
    text-color="#000000"
    active-text-color="#01579B"
    class="el-menu-vertical-demo" 
    unique-opened
    :router=true
    :default-active="activeLink"
    @open="handleOpen" 
    @close="handleClose" 
    :collapse="isSideBarClosed">
        <div class="logo-menu" style="height:90px; border-bottom: solid 1px #000000; margin-bottom:30px">
            <div class="logo-div" v-if="!isSideBarClosed">
                <v-img
                max-height="50px"
                max-width="60px"
                src = "/img/icons/credit-card.png"
                ></v-img>
                <span>ETX-POS</span>
            </div>
        </div>
        <el-menu-item index="/" class="menu-object">
            <img src="/img/icons/home.svg" class="el-icon" alt="">
            <span slot="title" class="menu-title">Home</span>
        </el-menu-item>
        <hr class="menu-divider">
        <el-submenu index="/inventory" class="menu-object">
            <template slot="title">
                <img src="/img/icons/inventory.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">INVENTORY</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/inventory/products">Products</el-menu-item>
                <el-menu-item index="/inventory/categories">Categories</el-menu-item>
                <el-menu-item index="/inventory/stock-units">Stock-Units</el-menu-item>
                <el-menu-item index="/inventory/stock-adjustments">Stock-Adjustments</el-menu-item>
                <el-menu-item index="/inventory/stock-adjustments/create">+ Create Stock-Adjustment</el-menu-item>
            </el-menu-item-group>
        </el-submenu>
        <hr class="menu-divider">
        <el-submenu index="/sales" class="menu-object">
            <template slot="title">
                <img src="/img/icons/shopping-cart.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Sales</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/sales/create">+ Create-Sale</el-menu-item>
                <el-menu-item index="/sales">Sales</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider">
        <el-submenu index="/purchases" class="menu-object">
            <template slot="title">
                <img src="/img/icons/shopping.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Purchases</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/purchases/create">+ Create-Purchase Invoice</el-menu-item>
                <el-menu-item index="/purchases">Purchase Invoices</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider">
        <el-submenu index="/reports" class="menu-object">
            <template slot="title">
                <img src="/img/icons/statistics.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Reports</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/reports/sale">sale report</el-menu-item>
                <el-menu-item index="/reports/purchase">purchase report</el-menu-item>
                <el-menu-item index="/reports/adjustments">adjustment report</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider">
        <el-submenu index="/admin" class="menu-object">
            <template slot="title">
                <img src="/img/icons/admin.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">admin</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/admin/manage-users">User Management</el-menu-item>
                <el-menu-item index="/admin/invnetory-csv">Upload Inventory using csv</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider">
        <el-submenu index="/settings" class="menu-object">
            <template slot="title">
                <img src="/img/icons/settings.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Configuration</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/settings/shopsetup">Shop Set-Up</el-menu-item>
                <el-menu-item index="/settings/tax">Tax settings</el-menu-item>
                <el-menu-item index="/settings/backup-and-restore">Backup and Restore</el-menu-item>
            </el-menu-item-group>
        </el-submenu>                       
    </el-menu>
</div>
</template>

<script>
    export default {
        watch: {
            $route(to, from) {
                this.activeLink = to.path;
            }
        },
        mounted() {
            this.activeLink = this.$route.path;
        },
        data() {
            return {
                isCollapse: true,
                activeLink: null,
            };
        },
        methods: {
            handleOpen(key, keyPath) {
                console.log(key, keyPath);
            },
            handleClose(key, keyPath) {
                console.log(key, keyPath);
            }
        },
        computed: {
            isSideBarClosed: function() {
                return this.$store.getters.is_sidebar_closed;
            },
        }
    }
</script>

<style>
    .el-menu-vertical-demo:not(.el-menu--collapse) {
        width: 270px;
        height: 100%;
    }
    .el-menu--collapse{
        height: 100% !important;
        overflow-x: hidden;
    }
    .el-menu-vertical-demo {
        width: 80px;
        min-height: 100%;
    }
    .logo-div {
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: center;
        align-items: center;
    }
    .el-icon {
        width: 40px;
        width: 40px;
    }
    .menu-title {
        margin-left: 10px !important;
        font-size: 13px !important;
    }

    .el-menu {
        /* background-image: linear-gradient(to top, #13649d, #0087bc, #00abd4, #00cfe7, #48f3f3);
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: scroll;
        overflow-y: auto; */
        overflow-y: auto;
        background-color: transparent;
    }
    .bg-menu {
        /* background-image: linear-gradient(to top, #13649d, #0087bc, #00abd4, #00cfe7, #48f3f3); */
        background-color: #ffffff;
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: scroll;
        overflow-y: auto;
    }
    .el-menu-item:hover {
        background-color: #ffffff !important;
    }
    .menu-object {
        margin-bottom: 10px;
    }

    .el-menu::-webkit-scrollbar {
        width: 3px;
        background-color: #f5f5f5;
    }
    .el-menu::-webkit-scrollbar-thumb {
        background-color: #a0a2a5;
    }
    .el-menu::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        background-color: #f5f5f5;
    }

    .el-menu a {
        text-decoration: none;
        color: inherit;
    }

    .el-submenu {
        background-color: transparent !important;
    }
    .el-submenu .el-icon-arrow, .el-submenu__icon-arrow {
        color: #000 !important;
        font-weight: bold;
    }
    .el-submenu .el-submenu .el-icon-arrow-down {
        color: #000 !important;
        font-weight: bold;
    }

    .el-menu--vertical {
        /* background-color: green; */
    }
     .el-menu--vertical .el-menu--popup {
         font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: transparent !important;
    }

    .el-menu--vertical{
        background-color: #09c2e2 !important;
    }
    .el-menu-item:hover {
        
    }
    .el-menu-item:active {
        color: red;
    }
    is-active {
        background-color: red !important;
    }
    .menu-title, .el-menu-item {
        text-transform: uppercase;
    }
    .menu-divider {
        width: 80%;
        margin: auto;
    }
</style>