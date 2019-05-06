<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>    
                <v-toolbar flat dense>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>CREATE NEW PRODUCTS</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon
                    @click="saveToDisk">
                        <v-icon>save</v-icon>
                    </v-btn>
                    <v-btn @click="createProductEntries"
                    color="cyan"
                    :dark="entryValid"
                    :disabled="!entryValid">
                        <v-icon>add</v-icon>
                        create products
                    </v-btn>
                </v-toolbar>
                <v-card-text>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Label</th>
                                <th>Barcode</th>
                                <th>Category</th>
                                <th>Default sku</th>
                                <th>Quantity</th>
                                <th>Batch #</th>
                                <th>Expiry date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(entry,index) in product_entries" :key="index">
                                <td>{{ index+1 }}</td>
                                <td>
                                    <input type="text" class="form-control" v-model="entry.label">
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="entry.barcode">
                                </td>
                                <td>
                                    <select class="form-control" v-model="entry.category_id">
                                        <option v-for="cat in categories" :value="cat.id" :key="cat.id">
                                            {{ cat.label }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" v-model="entry.default_sku_id">
                                        <option v-for="sku in stock_units" :value="sku.id" :key="sku.id">
                                            {{ sku.label }}
                                        </option>
                                    </select>
                                </td>
                                <td style="max-width:100px;">
                                    <input type="number" class="form-control" v-model="entry.quantity" style="width:100%">
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="entry.batch">
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="entry.expiry_date">
                                </td>
                                <td>
                                    <button class="btn btn-danger"
                                    v-if="product_entries.length > 1"
                                    @click="removeEntry(entry)">X</button>
                                    <button class="btn btn-info"    
                                    v-if="index==product_entries.length-1"
                                    @click="addEntry()">+</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { Message } from 'element-ui'
    import {GetCategory} from '../../utils/category'
    import {GetStockUnit} from '../../utils/stock-unit'
    import {CreateProductEntries} from '../../utils/product'
import Axios from 'axios';
    export default {
        mounted() {
            this.fetchCategories();
            this.fetchStockUnits();
            this.initPage();
        },
        data(){
            return {
                product_entries: [],
                headers: [{text:'label', value:'label'}, {text:'barcode', value:'barcode'}
                            ,{text:'category', value:'category_id'}, {text:'basic stock-unit', value:'default_sku_id'}
                            ,{text:'Batch #', value:'batch'}, {text: 'Expiry Date(mm/yyyy)', value: 'expiry_date'}
                            ,{text:'Actions', value:'btn', align:'right'}],
                categories: [],
                stock_units: [],            
            }
        },
        methods: {
            initPage: function() {
                let localEntries = localStorage.getItem('product_entries_data');
                if(localEntries) {
                    this.product_entries = JSON.parse(localEntries);
                } else {
                    this.addEntry();
                }
            },
            addEntry: function(){
                let entry = {label:'', barcode:'', category_id:"", default_sku_id:"", expiry_date:''
                                , batch:'', quantity:'', description:''};
                this.product_entries.push(Vue.util.extend({}, entry));
            },
            fetchCategories: function() {
                GetCategory({})
                    .then(result=> {
                        console.log('ressa', result)
                        this.categories = result.categories || [];
                    })
            },
            fetchStockUnits: function() {
                GetStockUnit({})
                    .then(result=> {
                        console.log('ressa', result)
                        this.stock_units = result.stock_units || [];
                    })
            },
            removeEntry: function(row) {
                let idx = this.product_entries.indexOf(row);
                if(idx > -1) {
                    this.product_entries.splice(idx, 1);
                }
            },
            saveToDisk: function() {
                localStorage.setItem('product_entries_data', JSON.stringify(this.product_entries));
            },
            createProductEntries: function() {
                let pros = [];
                this.product_entries.forEach(ent=> {
                    let d = ent;
                    d.label = (ent.label || "").toUpperCase();
                    pros.push(d);
                })
                CreateProductEntries({entries:pros})
                    .then(result=> {
                        if(result.code == 0) {
                            localStorage.removeItem('product_entries_data');
                            this.product_entries = [];
                            this.initPage();
                        }
                    })
                    .catch(err=> {

                    })
            },
            entriesDataValid: function() {
                let entries = this.product_entries || [];
                let errors = [];
                entries.forEach(entry=> {
                    if(!entry.label) {
                        errors.push("label field cannot be empty");
                    }
                    if(!entry.category_id) {
                        errors.push("category field cannot be empty");
                    }
                    if(!entry.default_sku_id) {
                        errors.push("default stock unit field cannot be empty");
                    }
                })
                if(errors.length > 0) {
                    Message({
                        message: res.message,
                        type: 'error',
                        duration: 2 * 1000
                    })
                }
            }
        },
        computed: {
            getCategories: function(){
                let list = this.categories || [];
                let res = list.map((cat)=> {
                    return {value:cat.id, text:cat.label};
                });
                return res || [];
            },
            getStockUnits: function(){
                let list = this.stock_units || [];
                let res = list.map((cat)=> {
                    return {value:cat.id, text:cat.label};
                });
                return res || [];
                console.log('catlist',res);
            },
            entryValid: function() {
                let list = this.product_entries || [];
                let errors = []
                list.forEach(entry=> {
                    if(!entry.label || !entry.default_sku_id || !entry.category_id) {
                        errors.push('err');
                    }
                })
                if(errors.length > 0) {
                    return false;
                }
                return true;
            }
        }
    }
</script>