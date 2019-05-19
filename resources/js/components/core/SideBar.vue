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
                src = "/img/icons/online-shop.svg"
                ></v-img>
                <span style="margin-left:5px">ETX-POS</span>
            </div>
        </div>
        <el-menu-item index="/" class="menu-object">
            <img src="/img/icons/home.svg" class="el-icon" alt="">
            <span slot="title" class="menu-title">Home</span>
        </el-menu-item>
        <hr class="menu-divider">
        <el-menu-item index="/dashboard" class="menu-object" v-if="showDashboard">
            <img src="/img/icons/dashboard.svg" class="el-icon" alt="">
            <span slot="title" class="menu-title">Dashboard</span>
        </el-menu-item>
        <hr class="menu-divider" v-if="showDashboard">
        <el-submenu index="/sales" class="menu-object" v-if="showSalesModule">
            <template slot="title">
                <img src="/img/icons/shopping-cart.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Sales</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/sales/create">+ Create-Sale</el-menu-item>
                <el-menu-item index="/sales" v-if="showSalesList">Sales</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider" v-if="showSalesModule">
        <el-submenu index="/purchases" class="menu-object" v-if="showPurchaseModule">
            <template slot="title">
                <img src="/img/icons/shopping.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Purchases</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/purchases/create">+ Create-Purchase Invoice</el-menu-item>
                <el-menu-item index="/purchases">Purchase Invoices</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider" v-if="showPurchaseModule">
        <el-submenu index="/inventory" class="menu-object" v-if="showInventoryModule">
            <template slot="title">
                <img src="/img/icons/inventory.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">INVENTORY</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/inventory/products">Products</el-menu-item>
                <el-menu-item index="/inventory/products/create/entries">+ Create Products</el-menu-item>
                <el-menu-item index="/inventory/categories">Categories</el-menu-item>
                <el-menu-item index="/inventory/stock-units">Stock-Units</el-menu-item>
                <el-menu-item v-if="showPriceTemplate" index="/inventory/price-templates">Price Templates</el-menu-item>
                <el-menu-item index="/inventory/stock-adjustments/create">+ Create Stock-Adjustment</el-menu-item>
            </el-menu-item-group>
        </el-submenu>
        <hr class="menu-divider" v-if="showInventoryModule">
        <el-submenu index="/clients" class="menu-object" v-if="showClientsModule">
            <template slot="title">
                <img src="/img/icons/customer.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">clients</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/clients/customers">customers</el-menu-item>
                <el-menu-item index="/clients/suppliers">suppliers</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider" v-if="showClientsModule">
        <el-submenu index="/reports" class="menu-object" v-if="showReportModule">
            <template slot="title">
                <img src="/img/icons/statistics.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Reports</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/reports/sale">sale report</el-menu-item>
                <el-menu-item index="/reports/purchase">purchase report</el-menu-item>
                <el-menu-item index="/reports/finance" v-if="showFinancialReport">financial report</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider" v-if="showReportModule">
        <el-submenu index="/ledgers" class="menu-object" v-if="showHistoryModule">
            <template slot="title">
                <img src="/img/icons/ledger.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">History / Ledgers</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/ledgers/stock-adjustment">Stock-Adjustment History</el-menu-item>
                <el-menu-item index="/ledgers/sales-with-debtors">Sales With Debtors</el-menu-item>
                <el-menu-item index="/ledgers/purchases-with-creditors">Purchases With Creditors</el-menu-item>
                <el-menu-item index="/ledgers/batches">All Batch Numbers</el-menu-item>
            </el-menu-item-group>
        </el-submenu>
        <hr class="menu-divider" v-if="showInventoryModule">
        <el-submenu index="/admin" class="menu-object" v-if="showAdminModule">
            <template slot="title">
                <img src="/img/icons/admin.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">admin</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/admin/users">User Management</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider" v-if="showAdminModule">
        <el-submenu index="/settings" class="menu-object" v-if="showConfigModule">
            <template slot="title">
                <img src="/img/icons/settings.svg" class="el-icon" alt="">
                <span slot="title" class="menu-title">Configuration</span>
            </template>
            <el-menu-item-group>
                <el-menu-item index="/settings/shopsetup">Shop Set-Up</el-menu-item>
                <el-menu-item index="/settings/backup-and-restore">Backup and Restore</el-menu-item>
            </el-menu-item-group>
        </el-submenu> 
        <hr class="menu-divider" v-if="showConfigModule">                      
    </el-menu>
</div>
</template>

<script>
    import { HasAnyRole } from '../../utils/role'
    export default {
        watch: {
            $route(to, from) {
                this.activeLink = to.path;
            }
        },
        created() {
            
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
            },
            handleClose(key, keyPath) {
            },
            hasAnyRole: function(roles) {
                HasAnyRole(roles);
            }
        },
        computed: {
            isSideBarClosed: function() {
                return this.$store.getters.is_sidebar_closed;
            },
            showReportModule: function() {
                if(HasAnyRole(['admin', 'manager'])) {
                    return true;
                }
                return false;
            },
            showInventoryModule: function() {
                if(HasAnyRole(['supervisor', 'admin', 'manager'])) {
                    return true;
                }
                return false;
            },
            showPurchaseModule: function() {
                if(HasAnyRole(['admin', 'manager', 'supervisor'])) {
                    return true;
                }
                return false;
            },
            showAdminModule: function() {
                if(HasAnyRole(['admin'])) {
                    return true;
                }
                return false;
            },
            showConfigModule: function() {
                if(HasAnyRole(['admin', 'manager'])) {
                    return true;
                }
                return false;
            },
            showDashboard: function() {
                if(HasAnyRole(['admin', 'manager'])) {
                    return true;
                }
                return false;
            },
            showFinancialReport: function() {
                if(HasAnyRole(['admin'])) {
                    return true;
                }
                return false;
            },
            showSalesModule: function() {
                if(HasAnyRole(['admin', 'manager','sales-rep','supervisor'])) {
                    return true;
                }
                return false;
            },
            showSalesList: function() {
                if(HasAnyRole(['admin', 'manager', 'supervisor'])) {
                    return true;
                }
                return false;
            },
            showClientsModule: function() {
                if(HasAnyRole(['admin', 'manager', 'sales-rep'])) {
                    return true;
                }
                return false;
            },
            showHistoryModule: function() {
                if(HasAnyRole(['admin', 'manager'])) {
                    return true;
                }
                return false;
            },
            showPriceTemplate: function() {
                if(HasAnyRole(['admin','manager'])) {
                    return true
                }
                return false;
            }
        },
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