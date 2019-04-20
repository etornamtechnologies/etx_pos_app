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
import PriceTemplate from './views/inventory/PriceTemplate'
import PriceTemplateCreate from './views/inventory/PriceTemplateCreate'
import ProductDetail from './views/inventory/ProductDetail'
import StockAdjustmentCreate from './views/inventory/StockAdjustmentCreate'
import CreateSale from './views/sale/CreateSale'
import ListSale from './views/sale/ListSale'
import CreatePurchase from './views/purchase/CreatePurchase'
import ListPurchase from './views/purchase/ListPurchase'
import ListSupplier from './views/client/ListSupplier'
import ListCustomer from './views/client/ListCustomer'
import ShopSetup from './views/config/ShopSetup'
import BackupAndRestore from './views/config/BackupAndRestore'
import UserList from './views/admin/UserList'
import User from './views/admin/User'
import SaleReport from './views/report/SaleReport'
import PurchaseReport from './views/report/PurchaseReport'
import FinanceReport from './views/report/FinanceReport'
import PermissionError from './views/PermissionError'
import StockAdjustmentHistory from './views/ledgers/StockAdjustmentHistory'
import SalesWithDebtors from './views/ledgers/SalesWithDebtors'
import PurchasesWithCreditors from './views/ledgers/PurchasesWithCreditors'
import BatchList from './views/ledgers/BatchList'
import ProductRestockList from './views/notification/ProductRestockList'
import ProductExpiryList from './views/notification/ProductExpiryList'
import store from './store';
import { GetUserInfo } from './utils/user'

Vue.use(Router)

const router = new Router({
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
                            component: Category,
                            meta: {requiresRoleCheck:true, roleList: ['admin','manager']}
                        },
                        {
                            path: 'stock-units',
                            component: StockUnit
                        },
                        {
                            path: 'products',
                            component: Product,
                            name: 'product_list'
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
                        },
                        {
                            path: 'price-templates',
                            component: PriceTemplate
                        },
                        {
                            path: 'price-templates/create',
                            component: PriceTemplateCreate,
                            name: 'create-price-template'
                        }
                    ]
                },
                {
                    path: 'sales',
                    component: ListSale,
                    name: 'sale_list'
                },
                {
                    path: 'sales/create',
                    component: CreateSale,
                    name: 'sale_create'
                },
                {
                    path: 'purchases',
                    component: ListPurchase,
                    name: 'purchase_list'
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
                    component: SaleReport,
                    name: 'sale_report'
                },
                {
                    path: 'reports/purchase',
                    component: PurchaseReport,
                    name: 'purchase_report'
                },
                {
                    path: 'reports/finance',
                    component: FinanceReport
                },
                {
                    path: 'clients/customers',
                    component: ListCustomer,
                    name: 'list-customer'
                },
                {
                    path: 'clients/suppliers',
                    component: ListSupplier,
                    name: 'list-supplier'
                },
                {
                    path: 'dashboard',
                    component: DashBoard,
                    name: 'dashboard'
                },
                {
                    path: '403',
                    component: PermissionError,
                    name: '403'
                },
                {
                    path: 'ledgers/stock-adjustment',
                    component: StockAdjustmentHistory,
                },
                {
                    path: 'ledgers/sales-with-debtors',
                    component: SalesWithDebtors,
                    name: 'sales-with-debtors'
                },
                {
                    path: 'ledgers/purchases-with-creditors',
                    component: PurchasesWithCreditors,
                    name: 'purchases-with-creditors'
                },
                {
                    path: 'ledgers/batches',
                    component: BatchList,
                    name: 'batch-list'
                },
                {
                    path: 'alerts/product-restock',
                    component: ProductRestockList,
                    name: 'product-restock-list'
                },
                {
                    path: 'alerts/product-expiry',
                    component: ProductExpiryList,
                    name: 'product-expiry-list'
                }
            ]
        }
  ]
})


export default router;
