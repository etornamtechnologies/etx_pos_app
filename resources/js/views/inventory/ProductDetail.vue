<template>
    <div class="row">
        <div class="col-md-4">
            <v-card style="min-height:700px; padding-bottom:10px">
                <v-toolbar color="cyan" dark flat>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>PRODUCT DETAIL</v-toolbar-title>
                </v-toolbar>
                <v-list two-line>
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon color="indigo">menu</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Product Name</v-list-tile-title>
                            <v-list-tile-sub-title>{{ product.label }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon color="indigo">menu</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Product Barcode</v-list-tile-title>
                            <v-list-tile-sub-title>{{ product.barcode }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon color="indigo">menu</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Product Category</v-list-tile-title>
                            <v-list-tile-sub-title>{{ (product.category|| {}).label }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile>
                        <v-list-tile-action>
                            <v-icon color="indigo">menu</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Product Default Stock unit</v-list-tile-title>
                            <v-list-tile-sub-title>{{ (product.default_sku || {}).label }}</v-list-tile-sub-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
                <div class="row mb-3">
                    <div class="col-md-8 offset-md-2 bg-light py-2">
                        <div class="row">
                            <div class="col-md-12 justify-content-center">Description</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                {{ product.description }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        Product status
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8 offset-md-2">
                        <el-switch 
                        label="status"
                        v-model="status"
                        active-text="active"
                        inactive-text="inactive"></el-switch>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-8 offset-md-2">
                        <div class="form-group">
                            <label>default unit of measurement:</label>
                            <select class="form-control" v-model="product_data.default_stock_unit" required>
                                <option v-for="uom in getBasicStockUnits" :value="uom.id" :key="uom.id">
                                    {{ uom.label }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Restock Alert Quantity</label>
                            <input 
                            style="width:100%"
                            type="number" class="form-control" v-model="product_data.reorder_quantity"/>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-8 offset-md-2">
                        <button class="btn btn-block btn-info" @click="updateProduct">UPDATE</button>
                    </div>
                </div>
            </v-card>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-1">
                    <button class="btn btn-info btn-sm float-right" @click="openAddStockUnitModal">ADD STOCK_UNIT</button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>stock unit</th>
                                <th>metric scale</th>
                                <th>stock quantity</th>
                                <th>cost price (GHC)</th>
                                <th>selling price (GHC)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="sku in product.stock_units" :key="sku.id">
                                <td>{{ sku.label }}</td>
                                <td>{{ sku.pivot.metric_scale }}</td>
                                <td>{{ metricQuantity(sku) }}</td>
                                <td>
                                    {{ entryPrice(sku.pivot.cost_price || 0) }}
                                    <v-icon class="float-right"
                                    @click="openStockCostPriceUpdateDialog(sku)">edit</v-icon>
                                </td>
                                <td>
                                    {{ entryPrice(sku.pivot.selling_price || 0) }}
                                    <v-icon class="float-right"
                                    @click="openStockSellingPriceUpdateDialog(sku)">edit</v-icon>
                                </td>
                                <td>
                                    <v-btn icon color="red" dark @click="removeStockUnitFromProduct(sku)" small>
                                        <v-icon small>remove</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="supplier in suppliers" :key="supplier.id">
                               <td> {{ supplier.supplier_name || '--' }} </td>
                               <td> {{ supplier.supplier_phone || '--' }} </td> 
                               <td> {{ supplier.supplier_email || '--' }} </td>
                               <td> {{ supplier.supplier_address || '--' }} </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <v-dialog v-model="addStockUnitModal" persistent width="500">
            <v-card>
                <form @submit.prevent="addStockUnitToProduct">
                <v-card-title class="headline">ADD STOCK UNIT</v-card-title>
                <v-card-text>
                    <div class="form-group">
                        <label for="">Stock Unit</label>
                        <select class="form-control" v-model="add_stock_unit.stock_unit_id">
                            <option v-for="sku in stock_units" :value="sku.id" :key="sku.id">
                                {{ sku.label }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">metric scale</label>
                        <input type="text" class="form-control" v-model="add_stock_unit.metric_scale">
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="addStockUnitModal = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">ADD TO PRODUCT</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isUpdateCostPriceDialog" persistent width="500">
            <v-card>
                <form @submit.prevent="updateStockCostPrice">
                    <v-card-title class="headline">UPDATE STOCK COST PRICE</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="cost price"
                        v-model="stock_cost_price_data.cost_price"
                        ></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isUpdateCostPriceDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isUpdateSellingPriceDialog" persistent width="500">
            <v-card>
                <form @submit.prevent="updateStockSellingPrice">
                    <v-card-title class="headline">UPDATE STOCK SELLING PRICE</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="selling price"
                        v-model="stock_selling_price_data.selling_price"
                        ></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isUpdateSellingPriceDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
    </div>
</template>
<script>
    import {GetProduct, CreateProduct, UpdateProduct, DeleteProduct, GetProductDetail} from '../../utils/product'
    import {GetStockUnit} from '../../utils/stock-unit'
    import {AddStockUnitToProduct, RemoveStockUnitFromProduct, UpdateStockCostPrice, UpdateStockSellingPrice} from '../../utils/inventory'
    export default {
        mounted(){
            this.fetchStockUnits()
            this.fetchProduct();
            this.initPage();
        },
        data(){
            return {
                product: {},
                addStockUnitModal: false,
                add_stock_unit: {},
                stock_units: [],
                product_data: {},
                status: false,
                isUpdateCostPriceDialog: false,
                isUpdateSellingPriceDialog: false,
                stock_selling_price_data: {},
                stock_cost_price_data: {},
                isPricingLoading: false,
                suppliers: []
            }
        },
        methods: {
            fetchProduct: function() {
                let productId = this.$route.params.id;
                GetProductDetail(productId)
                    .then(result=> {
                        console.log(result.product)
                        this.product = result.product
                        this.suppliers = result.suppliers || []
                        this.initPage()
                    })
            },
            initPage: function() {
                this.product_data = Vue.util.extend({}, this.product);
                let status = this.product_data.status;
                if(status == 'active') {
                    this.status = true;
                } else {
                    this.status = false;
                }
            },
            fetchStockUnits: function() {
                GetStockUnit()
                    .then(result=> {
                        this.stock_units = result.stock_units || [];
                    })
            },
            metricQuantity: function(sku_row) {
                let stockQty = this.product.stock_quantity || 0;
                let skuMetricScale = sku_row.pivot.metric_scale || 1
                let metricQty = Number(stockQty) / Number(skuMetricScale);
                let fin = Math.floor(metricQty);
                return fin;
            },
            openAddStockUnitModal: function(){
                this.add_stock_unit = {product_id: this.product.id, stock_unit_id: '', metric_scale:''};
                this.addStockUnitModal = true;
            },
            addStockUnitToProduct: function() {
                console.log(this.add_stock_unit);
                AddStockUnitToProduct(this.add_stock_unit)
                    .then(result=> {
                        this.product = result.product
                        this.addStockUnitModal = false;
                    })
            },
            removeStockUnitFromProduct: function(row) {
                let data = {product_id:this.product.id, stock_unit_id: row.id}
                RemoveStockUnitFromProduct(data)
                    .then(result=> {
                        this.product = result.product
                    })
            }, 
            updateProduct: function(){
                let data = Vue.util.extend({},this.product_data);
                if(this.status == true) {
                    data.status = 'active'
                } else {
                    data.status = 'inactive';
                }
                UpdateProduct(data)
                    .then(result=> {
                        location.reload();
                    })
            },
            entryPrice: function(price) {
                let s = (price/100)
                return s.toFixed(2);
            },
            openStockCostPriceUpdateDialog: function(entry) {
                let pivot = Vue.util.extend({}, entry.pivot || {})
                this.stock_cost_price_data.product_id = pivot.product_id || null;
                this.stock_cost_price_data.stock_unit_id = pivot.stock_unit_id || null
                this.stock_cost_price_data.cost_price = "";
                this.isUpdateCostPriceDialog = true;
            },
            openStockSellingPriceUpdateDialog: function(entry) {
                let pivot = Vue.util.extend({}, entry.pivot || {})
                this.stock_selling_price_data.product_id = pivot.product_id || null;
                this.stock_selling_price_data.stock_unit_id = pivot.stock_unit_id || null
                this.stock_selling_price_data.selling_price = "";
                this.isUpdateSellingPriceDialog = true;
            },
            updateStockCostPrice: function() {
                this.isPricingLoading = true;
                UpdateStockCostPrice(this.stock_cost_price_data)
                    .then(result=> {
                        this.isPricingLoading = false;
                        this.product = result.product || {};
                        this.isUpdateCostPriceDialog = false;
                    })
                    .catch(err=> {
                        this.isPricingLoading = false;
                    })
            },
            updateStockSellingPrice: function() {
                this.isPricingLoading = true;
                UpdateStockSellingPrice(this.stock_selling_price_data)
                    .then(result=> {
                        this.isPricingLoading = false;
                        this.product = result.product || {};
                        this.isUpdateSellingPriceDialog = false;
                    })
                    .catch(err=> {
                        this.isPricingLoading = false;
                    })
            },
        },
        computed:  {
            getBasicStockUnits: function() {
                let skus = (this.product.stock_units) || [];
                return skus.filter((sku)=> {
                    return (sku.pivot || {}).metric_scale == 1
                })
            }  
        }
    }
</script>