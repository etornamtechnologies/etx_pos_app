<template>
    <div class="container fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-0">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">
                                        Supplier
                                        <span @click="openCreateSupplierModal" class="ml-2 create-link">create supplier</span>
                                    </label>
                                    <select class="form-control"
                                    v-model="summary.supplier_id"
                                    >
                                        <option value="">Anonymous</option>
                                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                            {{ supplier.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">supplier invoice #</label>
                                    <input
                                    type="text"
                                    class="form-control"
                                    v-model="summary.supplier_invoice">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">invoice amount</label>
                                    <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    style="width:100%"
                                    v-model="summary.invoice_amount"/>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Amount Paid</label>
                                    <input
                                    type="number"
                                    class="form-control"
                                    style="width:100%"
                                    step="0.01"
                                    v-model="summary.amount_paid">
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                                <button 
                                style="margin-top:26px"
                                class="btn btn-info"
                                :disabled="cart.length < 1 || isLoading"
                                @click="createPurchase">CREATE INVOICE</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-6 offset-md-6" style="position:relative">
                                <form @submit.prevent="searchProduct">
                                    <v-text-field
                                    label="search"
                                    v-model="filter"
                                    id="search-input"
                                    autocomplete="off"
                                    autofocus></v-text-field>
                                    <button style="display:none" type="submit"></button>
                                </form>
                                <div v-if="search_result.length > 0" class="search-result">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button 
                                            class="btn btn-block btn-warning btn-sm mr-1"
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
                            <div class="col-md-12" style="height:450px; overflow-y:auto">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>product</th>
                                            <th>Batch #</th>
                                            <th>Expiry Date</th>
                                            <th>quantity</th>
                                            <th>uom</th>
                                            <th>unit cost</th>
                                            <th>sum</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(entry, index) in cart" :key="entry.id">
                                            <td>{{ index+1 }}</td>
                                            <td style="min-width:200px">{{ entry.data.label }}</td>
                                            <td>
                                                <input style="width:100px !important" 
                                                    type="text" class="form-control" v-model="entry.batch_number">
                                            </td>
                                            <td>
                                                <input
                                                placeholder="MM/YY" 
                                                style="width:100px !important" 
                                                type="text" class="form-control" 
                                                v-model="entry.expiry_date">
                                            </td>
                                            <td style="max-width:150px; min-width:150px">
                                                <input type="number"
                                                style="width:100%"
                                                class="form-control" min="1" v-model="entry.quantity">
                                            </td>
                                            <td style="min-width:170px">
                                                <select 
                                                class="form-control"
                                                style="width:100%"
                                                v-model="entry.selected_sku">
                                                    <option v-for="sku in getEntryStockUnits(entry)" :value="sku.id" :key="sku.id">
                                                        {{ sku.label }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td style="max-width:150px; min-width:150px">
                                                <input 
                                                style="width:100%"
                                                type="number" class="form-control" min="1" v-model="entry.cost_price">
                                            </td>
                                            <td>{{ (getEntrySum(entry)).toFixed(2) }}</td>
                                            <td>
                                                <el-button 
                                                type="danger"
                                                @click="removeCartEntry(index)">X</el-button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <el-dialog
                title="create supplier"
                :visible.sync="showCreateSupplierModal"
                width="30%"
                center>
                <form @submit.prevent="createSupplier">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" class="form-control" v-model="supplier.name">
                    </div>
                    <div class="form-group">
                        <label>phone:</label>
                        <input type="text" class="form-control" v-model="supplier.phone">
                    </div>
                    <div class="form-group">
                        <label>email:</label>
                        <input type="text" class="form-control" v-model="supplier.email">
                    </div>
                    <div class="form-group">
                        <label>address:</label>
                        <input type="text" class="form-control" v-model="supplier.address">
                    </div>
                    <button class="btn btn-success btn-block" type="submit">
                        <el-icon class="el-icon-circle-plus"></el-icon>
                        create supplier
                    </button>
                </form>
            </el-dialog>        
        </div>
    </div>
</template>

<script>
    import {GetProduct} from '../../utils/product'
    import { CreatePurchase } from '../../utils/pos'
    import { CreateSupplier, GetSupplier } from '../../utils/inventory';
    export default {
        mounted() {
            this.fetchSuppliers();
        },
        data() {
            return {
                filter:"",
                search_result:[],
                cart:[],
                user: {},
                suppliers: [],
                supplier: {label:"", phone:"", email:"", address:""},
                summary: {supplier_id:"", supplier_invoice:"", invoice_amount:null, amount_paid:null},
                isLoading: false,
                showCreateSupplierModal: false,
            }
        },
        methods: {
            fetchSuppliers: function() {
                GetSupplier({})
                    .then(result=> {
                        console.log('res', result)
                        this.suppliers = result.suppliers || []
                    })
                    .catch(err=> {

                    })
            },
            searchProduct: function() {
                GetProduct({filter:this.filter, search:true})
                    .then(res=> {
                        if(res.code == 0) {
                            this.handleProductSearchResult(res.products);
                        } else {
                            Notifier.error(res.message || "");
                        }
                    })
            },
            handleProductSearchResult: function(products) {
                let productLen = products.length;
                if(productLen == 0) {
                    Notifier.info("Search does not match any product!")
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
                entry.cost_price = "";
                entry.batch_number = "";
                entry.expiry_date = "";
                entry.selected_sku = product.default_stock_unit || "";
                entry.data = Vue.util.extend({}, product);
                this.cart.unshift(entry);
                this.filter = "";
                document.querySelector('#search-input').focus();
            },
            openCreateSupplierModal: function(){
                this.supplier.label = "";
                this.supplier.phone = "";
                this.supplier.email = "";
                this.supplier.address = "";
                this.showCreateSupplierModal = true;
            },
            createSupplier: function() {
                CreateSupplier(this.supplier)
                    .then(result=> {
                        this.suppliers.push(result.supplier || {});
                        this.summary.supplier_id = (result.supplier || {}).id;
                        this.showCreateSupplierModal = false;
                    })
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
                let price = entry.cost_price;
                return qty*price;
            },
            createPurchase: function(){
                this.isLoading = true;
                let data = {}
                data.summary = this.summary;
                let entries = [];
                this.cart.forEach((entry)=> {
                    let d = {};
                    d.quantity = entry.quantity || "";
                    d.stock_unit_id = entry.selected_sku || null;
                    d.product_id = (entry.data || {}).id || null;
                    d.batch_number = entry.batch_number || "";
                    d.expiry_date = entry.expiry_date || "";
                    d.cost_price = entry.cost_price || "";
                    entries.push(d);
                })
                data.entries = entries;
                CreatePurchase(data)
                    .then(result=> {
                        this.isLoading = false;
                        location.reload(true);
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            }
        },
        computed: {

        }
    }
</script>

<style scoped>
    .search-result {
        position: absolute;
        z-index: 999999;
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
</style>
