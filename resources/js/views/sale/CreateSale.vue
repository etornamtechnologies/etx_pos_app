<template>
    <div class="container fluid" style="overflow-x:hidden">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-1">
                    Create Sale Quote
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <span><i class="shopping-cart"></i>  cart entries</span>
                        </div>
                        <div class="col-md-6" style="position:relative">
                            <form @submit.prevent="searchProduct">
                                <v-text-field
                                prepend-inner-icon="search"
                                autocomplete="off"
                                autofocus
                                id="search-input"
                                v-model="filter"></v-text-field>
                                <button style="display:none" type="submit"></button>
                            </form>
                            <div v-if="search_result.length > 0" class="search-result">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button
                                        class="btn btn-warning btn-sm btn-block mr-1"
                                        @click="search_result = []">x close</button>
                                    </div>
                                </div>    
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="search-list-menu">
                                            <li 
                                            v-for="item in search_result" 
                                            :key="item.id" 
                                            class="search-list"
                                            @click="addToCart(item)">
                                                {{ item.label }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="height:650px; overflow-y:auto">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>product</th>
                                        <th>quantity</th>
                                        <th>uom</th>
                                        <th>unit-price</th>
                                        <th>sum</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(entry, index) in cart" :key="entry.id">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ entry.data.label }}</td>
                                        <td style="width:170px">
                                            <input type="number" class="form-control" min="1" v-model="entry.quantity">
                                        </td>
                                        <td style="width:200px">
                                            <select 
                                            class="form-control"
                                            v-model="entry.selected_sku">
                                                <option v-for="sku in getEntryStockUnits(entry)" :value="sku.id" :key="sku.id">
                                                    {{ sku.label }}
                                                </option>
                                            </select>
                                        </td>
                                        <td>{{ (getEntrySellingPrice(entry)/100).toFixed(2) }}</td>
                                        <td>{{ (getEntrySum(entry)/100).toFixed(2) }}</td>
                                        <td>
                                            <el-button 
                                            type="danger"
                                            @click="removeCartEntry(index)">x</el-button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="height:100%">
                <div class="card-header py-1">
                    sale quote summary
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light py-1">
                                    <div class="row">
                                        <div class="col-md-3 item-centered" style="border-right:solid 1px #ffffff">
                                            <img class="form-icon" src="/img/icons/boy.svg" alt="">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="">
                                                    Customer 
                                                    <span @click="openCreateCustomerModal()" class="create-link ml-2">create customer</span>
                                                </label>
                                                <select class="form-control"
                                                v-model="summary.customer_id"
                                                >
                                                    <option value="">Anonymous</option>
                                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                        {{ customer.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="row">
                                        <div class="col-md-3 item-centered" style="border-right:solid 1px #ffffff">
                                            <img src="/img/icons/shopping-cart.svg" class="form-icon" alt="">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label>total cost</label>
                                                <input type="number" 
                                                class="form-control"
                                                style="width:100% !important"
                                                :value="(saleTotal/100).toFixed(2)"
                                                readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="row">
                                        <div class="col-md-3 item-centered" style="border-right:solid 1px #ffffff">
                                            <img src="/img/icons/debit-card.svg" class="form-icon" alt="">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="">total paid</label>
                                                <input type="number" 
                                                style="width:100% !important"
                                                class="form-control"
                                                v-model="summary.amount_paid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <div class="row">
                                        <div class="col-md-3 item-centered" style="border-right:solid 1px #ffffff">
                                            <img class="form-icon" src="/img/icons/change.svg" alt="">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <label for="">balance</label>
                                                <input type="text" 
                                                class="form-control"
                                                :value = "(saleBalance/100).toFixed(2)"
                                                readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-12">
                            <button 
                            class="btn btn-info btn-block" 
                            type="submit"
                            @click="createSale">SELL</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Receipt-->
        <div class="receipt">
            <div class="receipt-header">
                <div class="header-item">{{ (receipt_data.shop_info || {}).shop_name }}</div>
                <div class="header-item">{{ (receipt_data.shop_info || {}).shop_address }}</div>
                <div class="header-item">{{ (receipt_data.shop_info || {}).shop_phone }}</div>
                <div class="header-item">attendant: {{ (receipt_data.summary || {}).user}}</div>
            </div>
            <div class="receipt-invoice-wrapper">
                <span>invoice#: {{ (receipt_data.summary || {}).reference_number }}</span>
            </div>
            <div class="receipt-entries-wrapper">
                <table class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Sum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(entry, index) in receipt_data.entries" :key="index">
                            <td>{{ entry.product }}</td>
                            <td>{{ entry.quantity }} {{ entry.stock_unit }} (s)</td>
                            <td>{{ entry.unit_price }}</td>
                            <td>{{ entry.sum }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="receipt-summary">
                <div class="summary-item">TOTAL: {{ ((receipt_data.summary || {}).total_cost || 0).toFixed(2) }}</div>
                <div class="summary-item">PAID: {{ ((receipt_data.summary || {}).amount_paid || 0).toFixed(2)  }}</div>
                <div class="summary-item">CHANGE: {{ ((receipt_data.summary || {}).change || 0).toFixed(2) }}</div>
            </div>
            <div class="receipt-message text-center">
                <span><i>{{ ((receipt_data.summary || {}).shop_message) || "Thank you" }}</i></span>
            </div>
        </div>

        <el-dialog
            title="create customer"
            :visible.sync="showCreateCustomerModal"
            width="30%"
            center>
            <form @submit.prevent="createCustomer">
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" class="form-control" v-model="customer.name">
                </div>
                <div class="form-group">
                    <label>phone:</label>
                    <input type="text" class="form-control" v-model="customer.phone">
                </div>
                <div class="form-group">
                    <label>email:</label>
                    <input type="text" class="form-control" v-model="customer.email">
                </div>
                <div class="form-group">
                    <label>address:</label>
                    <input type="text" class="form-control" v-model="customer.address">
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <el-icon class="el-icon-circle-plus"></el-icon>
                    create customer
                </button>
            </form>
        </el-dialog>
    </div>
    </div>
</template>

<script>
    import {GetProduct} from '../../utils/product'
    import { CreateSale } from '../../utils/pos'
    import { Message } from 'element-ui';
    import { CreateCustomer, GetCustomer } from '../../utils/inventory'
    export default {
        mounted() {
            this.fetchCustomers()
        },
        data() {
            return {
                filter:"",
                search_result:[],
                cart:[],
                user: {},
                customers: [],
                customer: {label:"", phone:"", email:"", address:""},
                summary: {customer_id:"", total_cost:null, amount_paid:null},
                isLoading: false,
                showCreateCustomerModal: false,
                receipt_data: {}
            }
        },
        methods: {
            fetchCustomers: function() {
                GetCustomer({})
                    .then(result=> {
                        this.customers = result.customers || []
                    })

            },        
            searchProduct: function() {
                console.log('search');
                GetProduct({filter:this.filter})
                    .then(result=> {
                        let res = result;
                        if(res.code == 0) {
                            this.handleProductSearchResult(res.products);
                        }
                    })
            },
            openCreateCustomerModal: function(){
                this.customer.label = "";
                this.customer.phone = "";
                this.customer.email = "";
                this.customer.address = "";
                this.showCreateCustomerModal = true;
            },
            createCustomer: function() {
                CreateCustomer(this.customer)
                    .then(result=> {
                        this.customers.push(result.customer)
                        this.showCreateCustomerModal = false;
                    })
                    .catch(err=> {

                    })
            },
            handleProductSearchResult: function(products) {
                let productLen = products.length;
                if(productLen == 0) {
                    Message({
                        message: "Search does not match any product!",
                        type: 'info',
                        duration: 2 * 1000
                    })
                } else if(productLen == 1) {
                    let product = products[0];
                    this.search_result = [];
                    this.addToCart(product);
                } else {
                    this.search_result = products;
                }
            },
            addToCart: function(product) {
                let entry = {};
                entry.quantity = 1;
                entry.selected_sku = product.default_stock_unit || "";
                entry.data = Vue.util.extend({}, product);
                this.cart.unshift(entry);
                this.filter = "";
                document.querySelector('#search-input').focus();
            },
            removeCartEntry: function(index) {
                this.cart.splice(index, 1);
            },
            getEntryStockUnits: function(entry) {
                let product = entry.data || {} ;
                return product.stock_units; 
            },
            getEntrySelectedStockUnit: function(entry) {
                let product = entry.data || {};
                let skus = product.stock_units || [];
                let selectedStockUnitId = entry.selected_sku || null;
                return (skus.filter((su)=> {
                    return su.id == selectedStockUnitId;
                }) || []) [0];
            },
            getEntrySellingPrice: function(entry) {
                let stockUnit = this.getEntrySelectedStockUnit(entry);
                let price = ((stockUnit.pivot || {}).selling_price) || 0;
                return (stockUnit.pivot || {}).selling_price || 0;
            },
            getEntrySum: function(entry) {
                let qty = entry.quantity;
                let price = this.getEntrySellingPrice(entry);
                return qty*price;
            },
            createSale: function() {
                let entries = this.getCartEntries();
                if(entries.length < 1) {
                    return Message({
                        message: 'cart is empty',
                        type: 'info',
                        duration: 2 * 1000
                    })
                }
                let summaryData = this.summary;
                let data = {summary: summaryData, entries: entries};
                CreateSale(data)
                    .then(result=> {
                        if(result.code == 0) {
                            this.receipt_data = result.receipt_data;
                            console.log(this.receipt_data)
                            this.$nextTick(()=> {
                                window.print();
                                this.cart = [];
                                this.summary.customer_id = "";
                                this.summary.total_cost = "";
                                this.summary.amount_paid = "";
                                this.receipt_data = {};
                            })
                        } else {
                        }
                    })
            },
            getCartEntries: function() {
                let entries = [];
                (this.cart || []).forEach((entry)=> {
                    let d = {};
                    d.product_id = entry.data.id;
                    d.stock_unit_id = entry.selected_sku;
                    d.quantity = entry.quantity;
                    if(entry.quantity <= 0) {
                        return Notifier.warning("You cannot sell item with zero quantity!");
                    }
                    let stockUnits = (entry.data.stock_units || []);
                    let stockUnit = ((stockUnits.filter((sku)=> {
                        return sku.id == entry.selected_sku;
                    })||[])[0]) || {};
                    let sp = (stockUnit.pivot || {}).selling_price;
                    if(!sp || sp <= 0) {
                        return Notifier.info('Please you cannot sell non-priced item');
                    }
                    entries.push(d);
                })
                return entries;
            },
            resetSalePage: function(){
                this.cart = [];
                this.summary = {customer_id:"", total_cost:null, amount_paid:null};
            }
        },
        computed: {
            saleTotal: function() {
                let total = 0;
                (this.cart || []).forEach((entry)=> {
                    let sum = this.getEntrySum(entry);
                    total = total + Number(sum);
                })
                return total;
            },
            saleBalance: function() {
                let paid = this.summary.amount_paid || 0;
                return ((paid*100)-this.saleTotal);
            }
        }
    }
</script>

<style>
    .search-result {
        position: absolute;
        z-index: 99999999;
        top:0;
        margin-top: 50px;
        right: 0;
        width: 80%;
        background-color: #dcdee2;
    }
    .search-list-menu {
        width: 100%;
        height: 500px;
        overflow-y: auto;
        list-style: none;
        padding: 5px;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
    }
    .search-list {
        padding: 5px;
        margin-bottom: 3px;
        width: 100%;
        border-bottom: 1px solid #ffffff;
        cursor: pointer;
        text-align: center;
    }

    .search-list:hover {
        background-color: black;
        color:#ffffff;
    }

    .table {
        z-index: 5 !important;
    }

     .emphasize {
        font-size: 20px;
        font-weight: bold;
        color: gray;
    }
    .receipt {
        display: none;
    }


    @media print {
        @page{
            size: 0;
            margin: 0mm;
        }

        .left-side {
            display: none;
        }

        .nav-header {
            display: none !important;
        }

        .mainbar {
            display: none;
        }
        .pagebar {
            display: none;
        }
        .card {
            display: none;
        }
        .notification {
            display: none;
        }
        .receipt {
            display: flex;
            width: 400px;
            flex-direction: column;
            flex-wrap: nowrap;
            
        }
        .receipt-header {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            flex-wrap: wrap;
            justify-content: center;
            padding-top: 5px;
        }
        .header-item {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .receipt-invoice-wrapper {
            margin-top: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }
        .receipt-entries-wrapper {
            text-align: center;
        }
        .summary-item {
            text-align: center;
            font-weight: bold;
        }
    }
</style>
