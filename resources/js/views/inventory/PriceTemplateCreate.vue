<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense color="cyan" dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>CREATE NEW PRICE TEMPLATE</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form @submit.prevent="createPriceTemplate">
                            <v-layout column justify-center>
                                <v-flex md6>
                                    <v-text-field
                                    label="Label"
                                    v-model="template.label"></v-text-field>
                                </v-flex>
                                <v-flex md6>
                                    <v-text-field
                                    label="Description"
                                    v-model="template.description"></v-text-field>
                                </v-flex>
                                <v-flex>
                                    <v-select
                                    v-model="template.apply_on"
                                    label="add to"
                                    :items="add_to_list"
                                    item-text="label"
                                    item-value="id"
                                    ></v-select>
                                </v-flex>
                                <v-flex>
                                    (BASE * PERCENTAGE_VALUE%) + CONSTANT
                                </v-flex>
                                <v-flex>
                                    <v-layout row mt-5 mb-5 style="padding:10px; background-color:#e6e8ed">
                                        <v-flex xs3 mr-1>
                                            <v-select
                                            v-model="template.base_value"
                                            :items="base_list"
                                            item-text="label"
                                            item-value="id"
                                            label="BASE"></v-select>
                                        </v-flex>
                                        <v-flex md1 xs1 style="display:flex; justify-content:center; align-items:center">
                                            *
                                        </v-flex>
                                        <v-flex xs3>
                                            <v-text-field
                                            type="number"
                                            suffix="%"
                                            step="0.01"
                                            label="PERCENT VALUE"
                                            append-icon="percent"
                                            v-model="template.percent_value"></v-text-field>
                                        </v-flex>
                                        <v-flex md1 xs1 style="display:flex; justify-content:center; align-items:center">
                                            +
                                        </v-flex>
                                        <v-flex>
                                            <v-text-field
                                            type="number"
                                            label="CONSTANT"
                                            v-model="template.constant_value"
                                            style="0.01"></v-text-field>
                                        </v-flex>
                                    </v-layout>
                                </v-flex>
                                <v-flex>
                                    <v-select
                                    label="category of products"
                                    v-model="template.category_id"
                                    :items="categoryList"
                                    item-text="label"
                                    item-value="id"></v-select>
                                </v-flex>
                                <v-flex>
                                    <v-btn
                                    style="float:right"
                                    dark
                                    type="submit"
                                    color="cyan">CREATE TEMPLATE</v-btn>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetCategory } from '../../utils/category'
    import { GetPriceTemplate, CreatePriceTemplate } from '../../utils/inventory'
    export default {
        created() {
            this.fetchCategories();
        },
        data() {
            return {
                template: {label:'', category_id:'', description:'', constant_value:0, percent_value: ""
                            ,apply_on: 'cost_price', base_value:'cost_price'},
                categories: [],
                add_to_list: [{id:'cost_price', label:'apply on cost price'}
                            ,{id:'selling_price', label:'apply on selling price'}],
                base_list: [{id:'cost_price', label:'Cost-Price'}, {id:'selling_price', label:'Selling-Price'}]            
            }
        },
        methods: {
            createPriceTemplate: function(){
                console.log(this.template)
                CreatePriceTemplate(this.template)
                    .then(result=> {
                        this.$router.push('/inventory/price-templates')
                    })
                    .catch(err=> {

                    })
            },
            fetchCategories: function() {
                GetCategory({})
                    .then(result=> {
                        this.categories = result.categories
                    })
                    .catch(err=> {

                    })
            }
        },
        computed: {
            categoryList: function() {
                let d = {};
                d.id = '';
                d.label = 'all'
                let res = this.categories.map(cat=> {
                    let dd = {};
                    dd.id = cat.id;
                    dd.label = cat.label
                    return dd
                })
                res.unshift(d);
                return res;
            }
        },
    }
</script>