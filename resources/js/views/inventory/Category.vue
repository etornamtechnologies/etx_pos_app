<template>
   <v-layout row>
        <v-flex md12 xs12>
            <v-card>
                <v-toolbar color="cyan" dark dense flat>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>CATEGORIES</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon color="cyan" dark>
                        <v-icon
                        @click="createCategoryDialog = true">
                            add
                        </v-icon>
                    </v-btn>
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
                <v-data-table
                :headers="headers"
                :items="categories"
                :hide-actions=true
                >
                <template v-slot:items="props">
                    <td>{{ props.item.label }}</td>
                    <td class="text-xs-left">{{ props.item.created_at }}</td>
                    <td class="text-xs-left">{{ props.item.products_count }}</td>
                    <td class="text-xs-left">
                        <v-icon
                        @click="openEditCategoryDialog(props.item, props.index)">edit</v-icon>
                        <v-icon color="red"
                        @click="deleteCategory(props.item)">delete</v-icon>
                    </td>
                </template>
                <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                    Your search for "{{ search }}" found no results.
                </v-alert>
                </v-data-table>
            </v-card>
        </v-flex>

        <v-dialog v-model="createCategoryDialog" persistent width="500">
            <v-card>
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
            <v-card>
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
    export default {
        mounted() {
            this.fetchCategories();
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
            }
        },
        methods: {
            fetchCategories: function(){
                GetCategory({})
                    .then(result=> {
                        console.log('cats',result)
                        this.categories = result.categories || []
                    })
            },
            createCategory: function(){
                let data = Vue.util.extend({}, this.new_category);
                data.label = this.new_category.label.toUpperCase();
                CreateCategory(data)
                    .then(result=> {
                        this.categories.push(result.category || {});
                        this.createCategoryDialog = false;
                    })
                    .catch(err=> {

                    })
            },
            openEditCategoryDialog: function(row, index){
                this.edit_category = Vue.util.extend({},row);
                this.edit_category.index = index;
                this.isOpenEditCategoryDialog = true;
            },
            updateCategory: function(){
                UpdateCategory(this.edit_category)
                    .then(result=> {
                        this.categories[this.edit_category.index] = result.category;
                        this.isOpenEditCategoryDialog = false;
                        this.$forceUpdate();
                    })
            },
            deleteCategory: function(row) {
                if(confirm('Are you sure you want to delete? ')) {
                    DeleteCategory(row)
                        .then(result=> {
                            let index = this.categories.indexOf(row);
                            console.log(index);
                            this.categories.splice(index, 1);
                        })
                        .catch(err=> {
                            
                        })
                }
            }
        }
    }
</script>