<template>
   <v-layout row>
        <v-flex md12 xs12>
            <v-card>
                <v-toolbar color="cyan" dark dense flat>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>PRODUCTS</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-menu bottom left>
                        <template v-slot:activator="{ on }">
                            <v-btn
                            dark
                            icon
                            v-on="on"
                            >
                                <v-icon>more_vert</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-tile
                                key="create_product"
                                @click="openCreateProductModal"
                            >
                                <v-list-tile-title>
                                    <v-icon small>add</v-icon>
                                    create product
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile
                                key="create_product_entries"
                                @click="goCreateProductEntries"
                            >
                                <v-list-tile-title>
                                    <v-icon small>add</v-icon>
                                    create product(multiple)
                                </v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile
                            key="upload_product_csv"
                            @click="goProductCreateCsvPage">
                                <v-list-tile-title>
                                    <v-icon small>upload_file</v-icon>
                                    Upload product (CSV)
                                </v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-toolbar>
                <v-card-title>
                    <v-layout row wrap>
                        <v-flex xs12 sm6 md6>
                            <span style="margin-top:5px; margin-left:5px; font-size:17px">
                                Products
                            </span>
                        </v-flex>
                        <v-flex xs12 sm6 md6>
                            <v-form
                            style="width:100%"
                            @submit.prevent="fetchProducts">
                                <v-text-field
                                color="cyan"
                                v-model="search"
                                append-icon="search"
                                label="Search"
                                single-line
                                hide-details
                                ></v-text-field>
                            </v-form>
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text>
                    <v-layout column>
                        <v-flex xs12 style="position:relative; min-height:400px">
                            <div class="my-loader" v-if="isLoading"></div>
                            <v-data-table
                            :headers="headers"
                            :items="products"
                            hide-actions
                            >
                                <template v-slot:items="props">
                                    <td>{{ props.item.label }}</td>
                                    <td class="text-xs-left">{{ props.item.barcode }}</td>
                                    <td class="text-xs-left">{{ (props.item.category || {}).label }}</td>
                                    <td class="text-xs-left">{{ props.item.stock_quantity }} {{ (props.item.default_sku || {}).label }}</td>
                                    <td class="text-xs-left">{{ props.item.status }}</td>
                                    <td class="text-xs-right">
                                        <v-btn 
                                        color="cyan"
                                        icon small dark
                                         @click="productDetail(props.item)">
                                            <v-icon small>info</v-icon>
                                        </v-btn>
                                        <v-btn icon
                                        color="primary" small dark
                                        @click="openEditProductDialog(props.item, props.index)"
                                        >
                                            <v-icon small>edit</v-icon>
                                        </v-btn>
                                        <v-btn icon
                                        @click="deleteProduct(props.item)"
                                        color="error" dark small
                                        >
                                            <v-icon small>delete</v-icon>
                                        </v-btn>
                                    </td>
                                </template>
                                <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                                    Your search for "{{ search }}" found no results.
                                </v-alert>
                            </v-data-table>
                        </v-flex>
                        <v-flex xs12>
                            <el-pagination
                            background
                            :current-page.sync="currentPage"
                            :page-size="perPage"
                            :pager-count="21"
                            layout="prev, pager, next"
                            :total="totalRows">
                            </el-pagination>
                        </v-flex>
                    </v-layout>
                </v-card-text>
                <v-btn
                        color="pink"
                        dark
                        small
                        absolute
                        bottom
                        right
                        fab
                        @click="openCreateProductModal"
                >
                    <v-icon>add</v-icon>
                </v-btn>
            </v-card>
        </v-flex>

        <v-dialog v-model="isOpenCreateProductDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <v-form @submit.prevent="createProduct"
                ref="form"
                v-model="valid">
                <v-card-title class="headline">create product</v-card-title>
                <v-card-text>
                    <v-select
                    label="category *"
                    item-text="label"
                    item-value="id"
                    :rules="[v => !!v || 'category is required']"
                    v-model="product.category_id"
                    :items="categories"></v-select>
                    <v-text-field
                    label="label *"
                    required
                    :rules="[v => !!v || 'label is required']"
                    v-model="product.label"></v-text-field>
                    <v-text-field
                    label="barcode"
                    required
                    v-model="product.barcode"></v-text-field>
                    <v-select
                        label="default stock unit *"
                        item-text="label"
                        item-value="id"
                        :rules="[v => !!v || 'default sku is required']"
                        v-model="product.default_stock_unit"
                        :items="stock_units"></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isOpenCreateProductDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit"
                    :disabled="!valid">CREATE</v-btn>
                </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isOpenEditProductDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <v-form @submit.prevent="updateProduct">
                    <v-card-title class="headline">edit product</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="label *"
                        required
                        :rules="[v => !!v || 'label is required']"
                        v-model="edit_product.label"></v-text-field>
                        <v-text-field
                        label="label"
                        required
                        v-model="edit_product.barcode"></v-text-field>
                        <v-select
                        label="category *"
                        item-text="label"
                        item-value="id"
                        :rules="[v => !!v || 'category is required']"
                        v-model="edit_product.category_id"
                        :items="categories"></v-select>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isOpenEditProductDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">UPDATE</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
   </v-layout>
</template>

<script>
    import {GetProduct, CreateProduct, UpdateProduct, DeleteProduct} from '../../utils/product'
    import {GetCategory} from '../../utils/category'
    import {GetStockUnit} from '../../utils/stock-unit'
    import { hasAnyRole } from '../../utils/helpers'
    export default {
        beforeRouteEnter (to, from, next) {
            hasAnyRole(['admin','manager','supervisor'], (res)=> {
                if(res) {
                    next()
                } else {
                    next(from)
                }
            })
        },
        mounted() {
            this.fetchProducts();
            this.fetchCategories();
            this.fetchStockUnits();
        },
        watch: {
            currentPage: function() {
                this.fetchProducts();
            }
        },
        data(){
            return {
                search:'',
                createProductDialog: false,
                isOpenEditProductDialog: false,
                isOpenCreateProductDialog: false,
                categories: [],
                stock_units: [],
                products: [],
                headers: [{text:'label', value:'label'}, {text:'barcode', value:'barcode'}
                            ,{text:'category', value:'category.label'}, {text:'stock_quantity', value:'quantity'}
                            ,{text:'status', value:'status'}, {text:'', value:'buttons'}],
                new_product: {label:''},
                edit_product: {id:null, label:''},
                product: {label:'', category_id:'', barcode:''},
                nameRules: [
                    v => !!v || 'Name is required',
                    v => (v && v.length <= 50) || 'Name must be less than 50 characters'
                ],
                valid: true,
                currentPage: 1,
                perPage: 2,
                totalRows: null,
                isLoading: false,
                isUpdating: false,
            }
        },
        methods: {
            fetchProducts: function(){
                let _data = {};
                _data.paginate = true;
                _data.filter = this.search;
                _data.page = this.currentPage;
                this.isLoading = true;
                GetProduct(_data)
                    .then(result=> {
                        this.isLoading = false;
                        this.products = (result.products || {}).data || [];
                        this.currentPage = (result.products || {}).current_page;
                        this.perPage = (result.products || {}).per_page;
                        this.totalRows = (result.products || {}).total
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            fetchCategories: function(){
                GetCategory({})
                    .then(result=> {
                        this.categories = result.categories || []
                    })
            },
            fetchStockUnits: function() {
                GetStockUnit({})
                    .then(result=> {
                        this.stock_units = result.stock_units || [];
                    })
            },
            createProduct: function(){
                let data = Vue.util.extend({}, this.product);
                this.isUpdating = true;
                CreateProduct(data)
                    .then(result=> {
                        this.isUpdating = false;
                        this.products.push(result.product || {});
                        this.isOpenCreateProductDialog = false;
                        this.product = {label:'', category_id:'', barcode:''};
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                    })
            },
            goProductCreateCsvPage: function(){
                this.$router.push({ name: 'product-create-csv' })
            },
            openEditProductDialog: function(row, index){
                this.edit_product = Vue.util.extend({},row);
                this.edit_product.index = index;
                this.isOpenEditProductDialog = true;
            },
            updateProduct: function(){
                this.isUpdating = true;
                UpdateProduct(this.edit_product)
                    .then(result=> {
                        this.isUpdating = false;
                        this.products[this.edit_product.index] = result.product;
                        this.isOpenEditProductDialog = false;
                        this.fetchProducts();
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                    })
            },
            deleteProduct: function(row) {
                if(confirm('Are you sure you want to delete? ')) {
                    DeleteProduct(row)
                        .then(result=> {
                            let index = this.products.indexOf(row);
                            this.products.splice(index, 1);
                        })
                        .catch(err=> {
                            
                        })
                }
            },
            productDetail: function(row){
                let productId = row.id || null;
                this.$router.push(`/inventory/products/${productId}`);
            },
            openCreateProductModal: function() {
                this.isOpenCreateProductDialog = true;
            },
            goCreateProductEntries: function(){
                this.$router.push({name:'create_product_entries'})
            }
        },
        computed: {
            categoryList: function(){
                let cats = this.categories || [];
                return cats.map(cat=> {
                    return {value:cat.id, text:cat.label}
                })
            }
        }
    }
</script>