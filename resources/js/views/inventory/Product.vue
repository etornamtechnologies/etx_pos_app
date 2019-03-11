<template>
   <v-layout row>
        <v-flex md12 xs12>
            <v-card>
                <v-toolbar color="cyan" dark dense flat>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>PRODUCTS</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon color="cyan" dark>
                        <v-icon
                        @click="createProductDialog = true">
                            add
                        </v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-title>
                    Products
                    <v-spacer></v-spacer>
                    <v-text-field
                    color="cyan"
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                :headers="headers"
                :items="products"
                :search="search"
                >
                <template v-slot:items="props">
                    <td>{{ props.item.label }}</td>
                    <td class="text-xs-left">{{ props.item.barcode }}</td>
                    <td class="text-xs-left">{{ props.item.category.label }}</td>
                    <td class="text-xs-left">{{ props.item.stock_quantity }} {{ props.item.default_sku.label }}</td>
                    <td class="text-xs-left">{{ props.item.status }}</td>
                    <td class="text-xs-left">
                        <v-icon
                        @click="openEditProductDialog(props.item, props.index)">edit</v-icon>
                        <v-icon color="red"
                        @click="deleteProduct(props.item)">delete</v-icon>
                        <v-icon
                        @click="productDetail(props.item)">more</v-icon>
                    </td>
                </template>
                <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                    Your search for "{{ search }}" found no results.
                </v-alert>
                </v-data-table>
            </v-card>
        </v-flex>

        <v-dialog v-model="createProductDialog" persistent width="500">
            <v-card>
                <form @submit.prevent="createProduct">
                <v-card-title class="headline">create category</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="label"
                    required
                    v-model="new_product.label"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="createProductDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">CREATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isOpenEditProductDialog" persistent width="500">
            <v-card>
                <form @submit.prevent="updateProduct">
                <v-card-title class="headline">edit product</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="label"
                    required
                    v-model="edit_product.label"></v-text-field>
                    <v-text-field
                    label="label"
                    required
                    v-model="edit_product.barcode"></v-text-field>
                    <v-select
                    label="category"
                    item-text="label"
                    item-value="id"
                    v-model="edit_product.category_id"
                    :items="categories"></v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isOpenEditProductDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">UPDATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
   </v-layout>
</template>

<script>
    import {GetProduct, CreateProduct, UpdateProduct, DeleteProduct} from '../../utils/product'
    import {GetCategory} from '../../utils/category'
    export default {
        mounted() {
            this.fetchProducts();
            this.fetchCategories();
        },
        data(){
            return {
                search:'',
                createProductDialog: false,
                isOpenEditProductDialog: false,
                categories: [],
                products: [],
                headers: [{text:'label', value:'label'}, {text:'barcode', value:'barcode'}
                            ,{text:'category', value:'category.label'}, {text:'stock_quantity', value:'quantity'}
                            ,{text:'status', value:'status'}, {text:'', value:'buttons'}],
                new_product: {label:''},
                edit_product: {id:null, label:''},
            }
        },
        methods: {
            fetchProducts: function(){
                GetProduct({})
                    .then(result=> {
                        console.log('cats',result)
                        this.products = result.products || []
                    })
            },
            fetchCategories: function(){
                GetCategory({})
                    .then(result=> {
                        this.categories = result.categories || []
                    })
            },
            createProduct: function(){
                let data = Vue.util.extend({}, this.new_product);
                data.label = this.new_product.label.toUpperCase();
                CreateProduct(data)
                    .then(result=> {
                        this.categories.push(result.product || {});
                        this.createProductDialog = false;
                    })
                    .catch(err=> {

                    })
            },
            openEditProductDialog: function(row, index){
                this.edit_product = Vue.util.extend({},row);
                this.edit_product.index = index;
                this.isOpenEditProductDialog = true;
            },
            updateProduct: function(){
                UpdateProduct(this.edit_product)
                    .then(result=> {
                        this.products[this.edit_product.index] = result.product;
                        this.isOpenEditProductDialog = false;
                        this.fetchProducts();
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