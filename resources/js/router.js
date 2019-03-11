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
                            component: ProductCreateEntries
                        },
                        {
                            path: 'stock-adjustments/create',
                            component: StockAdjustmentCreate
                        }
                    ]
                },
            ]
        }
  ]
})
