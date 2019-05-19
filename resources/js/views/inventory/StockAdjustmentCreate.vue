<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-1">
                    Create Purchase Invoice
                    <button class="btn btn-info float-right btn-sm"
                            @click="createAdjustment">
                                + create adjustment
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    Adjustment Reason 
                                    <span @click="openCreateReasonModal()" class="ml-2 create-link">+create reason</span>
                                </label>
                                <select 
                                class="form-control"
                                v-model="summary.reason_id"
                                required>
                                    <option 
                                    v-for="reason in reasons" 
                                    :value="reason.id"
                                    :key="reason.id">
                                        {{ reason.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" style="position:relative">
                            <form @submit.prevent="searchProduct">
                                <div class="form-group mb-0" horizontal label="Filter">
                                    <div class="form-group">
                                        <label for="">Filter</label>
                                        <input type="text" class="form-control" id="search-input" 
                                        v-model="filter"
                                        placeholder="Type to search"
                                        autocomplete="off"/>
                                    </div>
                                </div>
                                <button style="display:none" type="submit"></button>
                            </form>
                            <div v-if="search_result.length > 0" class="search-result">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button
                                        class="btn btn-warning btn-block btn-sm mr-1"
                                        @click="search_result = []">close</button>
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
                        <div class="col-md-12" style="height:500px; overflow-y:auto">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>product</th>
                                        <th>Batch</th>
                                        <th>Expiry-Date</th>
                                        <th>Old Quantity(default uom)</th>
                                        <th>quantity (difference)</th>
                                        <th>new quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(entry, index) in cart" :key="entry.id">
                                        <td>{{ index+1 }}</td>
                                        <td>{{ entry.data.label }}</td>
                                        <td>
                                            <input 
                                            style="width:130px" type="text"
                                            class="form-control"
                                            v-model="entry.batch_number">
                                        </td>
                                        <td>
                                            <input 
                                            style="width:130px" type="text" placeholder="MM/YY" 
                                            class="form-control"
                                            v-model="entry.expiry_date">
                                        </td>
                                        <td>{{ entry.data.stock_quantity }} {{ entryDefaultSku(entry).label }}(s)</td>
                                        <td style="width:180px">
                                            <input type="number" class="form-control" min="1" v-model="entry.difference">
                                        </td>
                                        <td style="width:170px">
                                            {{ entryNewQuantity(entry) }}
                                        </td>
                                        <td>
                                            <el-button 
                                            type="danger"
                                            @click="removeCartEntry(index)">remove</el-button>
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
            title="create adjustment reason"
            :visible.sync="showCreateReasonModal"
            width="30%"
            center>
            <form @submit.prevent="createReason">
                <div class="form-group">
                    <label>label:</label>
                    <input type="text" class="form-control" v-model="reason.label">
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <el-icon class="el-icon-circle-plus"></el-icon>
                    create reason
                </button>
            </form>
        </el-dialog>
    </div>
</template>

<script>
    import {GetProduct} from '../../utils/product'
    import { hasAnyRole } from '../../utils/helpers'
    import { CreateReason, GetReason, CreateStockAdjustment } from '../../utils/adjustment'
    export default {
        beforeRouteEnter (to, from, next) {
            hasAnyRole(['admin','manager', 'supervisor'], (res)=> {
                if(res) {
                    next()
                } else {
                    next(from)
                }
            })
        },
        mounted() {
            this.fetchReasons();
        },
        data() {
            return {
                filter:"",
                search_result:[],
                cart:[],
                user: {},
                suppliers: [],
                reasons: [],
                reason: {label:""},
                summary: {reason_id: null, },
                isLoading: false,
                showCreateReasonModal: false,
            }
        },
        methods: {
            searchProduct: function() {
                GetProduct({filter:this.filter, pos_search:true})
                    .then(result=> {
                        this.handleProductSearchResult(result.products);
                    })
            },
            fetchReasons: function() {
                GetReason()
                    .then(result=> {
                        this.reasons = result.reasons;
                    })
            },
            createReason: function() {
                CreateReason(this.reason)
                    .then(result=> {
                        console.log(result)
                        if(result.code == 0) {
                            this.reasons.push(result.reason);
                            this.showCreateReasonModal = false;
                        }
                    })
                    .catch(err=> {

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
            openCreateReasonModal: function(){
                this.summary.reason_id = null;
                this.showCreateReasonModal = true;
            },
            addToCart: function(product) {
                let entry = {};
                entry.data = Vue.util.extend({}, product);
                entry.batch_number = "";
                entry.expiry_date = "";
                entry.difference = 0;
                this.cart.unshift(entry);
                this.filter = "";
                document.querySelector('#search-input').focus();
            },
            removeCartEntry: function(index) {
                this.cart.splice(index, 1);
            },
            getEntryStockUnits: function(entry) {
                let product = entry.data || {} ;
                console.log('skus', product.stock_units)
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
            entryNewQuantity: function(row) {
                let diff = Number(row.difference) || 0;
                let oldQty = Number(row.data.stock_quantity) || 0;
                return diff + oldQty;
            },
            getEntries: function() {
                let entries = this.cart;
                let res = [];
                entries.forEach((entry)=> {
                    let d = {};
                    d.product_id = (entry.data.id) || null;
                    d.new_quantity = this.entryNewQuantity(entry) || null;
                    d.old_quantity = entry.data.stock_quantity;
                    d.difference = (entry.difference) || null;
                    d.batch_number = (entry.batch_number) || "";
                    d.expiry_date = entry.expiry_date || ""
                    if(!d.difference) {
                        return Notifier.error('difference field cannot be empty');
                    }
                    res.push(d)
                })
                return res;
            },
            createAdjustment: function() {
                let cartdEntries = this.getEntries();
                let data = {entries: cartdEntries, summary: this.summary};
                CreateStockAdjustment(data)
                    .then(result=> {
                        this.cart = [];
                        this.summary = {reason_id:null};
                    })
                    .catch(err=> {

                    })
            },
            entryDefaultSku: function(row) {
                let defaultSkuId = row.data.default_stock_unit;
                let defaultSku = ((row.data.stock_units || []).filter((sku)=> {
                    return sku.id == defaultSkuId;
                }) || [])[0];
                return defaultSku;
            }
        },
        computed: {
            getAdjustmentEntries: function() {
                let entries = [];
                (this.cart || []).forEach((entry)=> {
                    let d = {};
                    d.product = (d.data || {}).id || null;
                    d.new_quantity = entry.new_quantity || 0;
                    entries.push(d);
                })
                return entries;
            },
        }
    }
</script>

<style scoped>
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
    }

    .search-list:hover {
        background-color: black;
        color:#ffffff;
    }

    .table {
        z-index: 5 !important;
    }
</style>
