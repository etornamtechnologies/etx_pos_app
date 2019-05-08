<template>
   <v-layout row>
        <v-flex md12 xs12>
            <v-card>
                <v-toolbar color="cyan" dark dense flat>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>CATEGORIES</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-card-title>
                    Categories
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
                <v-card-text style="position:relative; min-height:400px">
                    <div class="my-loader" v-if="isLoading"></div>
                    <v-layout column>
                        <v-flex xs12>
                            <v-data-table
                            :headers="headers"
                            :items="categories"
                            :search="search"
                            :rows-per-page-items="myRows"
                            >
                            <template v-slot:items="props">
                                <td>{{ props.item.label }}</td>
                                <td class="text-xs-left">{{ props.item.created_at }}</td>
                                <td class="text-xs-left">{{ props.item.products_count }}</td>
                                <td class="text-xs-right">
                                    <v-btn icon small dark color="primary"
                                    @click="openEditCategoryDialog(props.item, props.index)"
                                    >
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <v-btn icon small dark color="error"
                                    @click="deleteCategory(props.item)">
                                        <v-icon small>delete</v-icon>
                                    </v-btn>
                                </td>
                            </template>
                            <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                                Your search for "{{ search }}" found no results.
                            </v-alert>
                            </v-data-table>
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
                        @click="createCategoryDialog = true"
                >
                    <v-icon>add</v-icon>
                </v-btn>
            </v-card>
        </v-flex>

        <v-dialog v-model="createCategoryDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <form @submit.prevent="createCategory">
                <v-card-title class="headline">create category</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="label"
                    required
                    v-model="new_category.label"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="createCategoryDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">CREATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isOpenEditCategoryDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <form @submit.prevent="updateCategory">
                <v-card-title class="headline">edit category</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="label"
                    required
                    v-model="edit_category.label"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isOpenEditCategoryDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">UPDATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
   </v-layout>
</template>

<script>
    import {GetCategory, CreateCategory, UpdateCategory, DeleteCategory} from '../../utils/category'
    import { hasAnyRole } from '../../utils/helpers'
    import '../../store'
    export default {
        mounted() {
            this.fetchCategories();
        },
        beforeRouteEnter (to, from, next) {
            hasAnyRole(['admin','manager','supervisor'], (res)=> {
                if(res) {
                    next()
                } else {
                    next(from)
                }
            })
        },
        data(){
            return {
                search:'',
                createCategoryDialog: false,
                isOpenEditCategoryDialog: false,
                categories: [],
                headers: [{text:'label', value:'label'}, {text:'created on', value:'created_at'}
                            ,{text:'product_count', value:'product_count'}, {text:'', value:'buttons'}],
                new_category: {label:''},
                edit_category: {id:null, label:''},
                isLoading: false,
                isUpdating: false,
                myRows: [10,20,30],
            }
        },
        methods: {
            fetchCategories: function(){
                this.isLoading = true;
                GetCategory({})
                    .then(result=> {
                        this.isLoading = false;
                        this.categories = result.categories || []
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            createCategory: function(){
                let data = Vue.util.extend({}, this.new_category);
                data.label = this.new_category.label.toUpperCase();
                this.isUpdating = true;
                CreateCategory(data)
                    .then(result=> {
                        this.isUpdating = false;
                        this.categories.push(result.category || {});
                        this.createCategoryDialog = false;
                        this.new_category = {label:''}
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                        this.new_category = {label:''}
                    })
            },
            openEditCategoryDialog: function(row, index){
                this.edit_category = Vue.util.extend({},row);
                this.edit_category.index = index;
                this.isOpenEditCategoryDialog = true;
            },
            updateCategory: function(){
                this.isUpdating = true;
                UpdateCategory(this.edit_category)
                    .then(result=> {
                        this.isUpdating = false;
                        this.categories= result.categories;
                        this.isOpenEditCategoryDialog = false;
                        this.$forceUpdate();
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                    })
            },
            deleteCategory: function(row) {
                if(confirm('Are you sure you want to delete? ')) {
                    DeleteCategory(row)
                        .then(result=> {
                            let index = this.categories.indexOf(row);
                            this.categories.splice(index, 1);
                        })
                        .catch(err=> {
                            
                        })
                }
            },
        },
        computed: {

        }
    }
</script>