import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/Home.vue'
import SignUp from './views/account/SignUp'
import SignIn from './views/account/SignIn'
import AccountLayout from './components/layouts/AccountLayout'
import AppLayout from './components/layouts/AppLayout'
import DashBoard from './views/DashBoard';
import StockUnit from './views/inventory/StockUnit';
import Product from './views/inventory/Product';
import Category from './views/inventory/Category';
import Inventory from './views/inventory/Inventory'
import InventoryIndex from './views/inventory/InventoryIndex'
import ProductCreateEntries from './views/inventory/ProductCreateEntries'
import ProductCreate from './views/inventory/ProductCreate'
import ProductDetail from './views/inventory/ProductDetail'
import StockAdjustmentCreate from './views/inventory/StockAdjustmentCreate'
import CreateSale from './views/sale/CreateSale'
import ListSale from './views/sale/ListSale'
import CreatePurchase from './views/purchase/CreatePurchase'
import ListPurchase from './views/purchase/ListPurchase'
import ShopSetup from './views/config/ShopSetup'
import BackupAndRestore from './views/config/BackupAndRestore'
import UserList from './views/admin/UserList'
import User from './views/admin/User'
import SaleReport from './views/report/SaleReport'
import store from './store';

Vue.use(Router)

export default new Router({
    mode: 'history',
    routes: [
        {
            path: '/account', component: AccountLayout,
            children: [
                {
                    path: '',
                    component: SignIn
                },
                {
                    path: "login",
                    component: SignIn,
                    alias: "/login"
                },
                {
                    path: "register",
                    component: SignUp
                }
            ]
        },  
        {
            path: '/', component: AppLayout,
            children: [
                {
                    path: '', 
                    component: Home
                },
                {
                    path: 'dashboard',
                    component: DashBoard
                },
                {
                    path: 'inventory', component: Inventory,
                    children: [
                        {
                            path: '',
                            component: InventoryIndex
                        },
                        {
                            path: 'categories',
                            component: Category
                        },
                        {
                            path: 'stock-units',
                            component: StockUnit
                        },
                        {
                            path: 'products',
                            component: Product
                        },
                        {
                            path: 'products/:id',
                            component: ProductDetail
                        },
                        {
                            path: 'products/create',
                            component: ProductCreate
                        },
                        {
                            path: 'products/create/entries',
                            name: 'create_product_entries',
                            component: ProductCreateEntries
                        },
                        {
                            path: 'stock-adjustments/create',
                            component: StockAdjustmentCreate
                        }
                    ]
                },
                {
                    path: 'sales',
                    component: ListSale
                },
                {
                    path: 'sales/create',
                    component: CreateSale
                },
                {
                    path: 'purchases',
                    component: ListPurchase
                },
                {
                    path: 'purchases/create',
                    component: CreatePurchase
                },
                {
                    path: 'settings/shopsetup',
                    component: ShopSetup
                },
                {
                    path: 'settings/backup-and-restore',
                    component: BackupAndRestore
                },
                {
                    path:'admin/users',
                    component: UserList
                },
                {
                    path:'admin/users/:id',
                    name: 'user_detail',
                    component: User
                },
                {
                    path: 'reports/sale',
                    component: SaleReport
                }
            ]
        }
  ]
})
