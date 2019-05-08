<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense color="cyan" dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Price template</v-toolbar-title>
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
                                    key="create_template"
                                    @click="goCreateTemplatePage"
                                >
                                    <v-list-tile-title>+ CREATE NEW TEMPLATE</v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </v-toolbar>
                    <v-card-title>
                        template
                        <v-spacer></v-spacer>
                        <v-text-field
                        color="cyan"
                        prepend-inner-icon="search"
                        v-model="filter"></v-text-field>
                    </v-card-title>
                    <v-card-text style="position:relative; min-height:450px">
                        <div class="my-loader" v-if="isLoading"></div>
                        <v-data-table
                        :items="templates"
                        :headers="template_headers"
                        :search="filter"
                        :rows-per-page-items="myRows"
                        >
                            <template v-slot:items="props">
                                <td>
                                    {{ props.item.label }}
                                </td>
                                <td>
                                    {{ props.item.description }}
                                </td>
                                <td>
                                    {{ props.item.apply_on }}
                                </td>
                                <td>
                                    {{ prettyDate(props.item.created_at || '') }}
                                </td>
                                <td>
                                    {{ templateFormula(props.item) }}
                                </td>
                                <td>
                                    {{ productCategory(props.item) }}
                                </td>
                                <td>
                                    {{ props.item.status }}
                                </td>
                                <td style="min-width:260px">
                                    <v-btn
                                    small
                                    color="success"
                                    @click="applyPriceTemplate(props.item)"
                                    >apply</v-btn>
                                    <v-btn
                                    color="error"
                                    small
                                    @click="deleteTemplate"
                                    >X</v-btn>
                                </td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetPriceTemplate, DeletePriceTemplate, ApplyPriceTemplate } from '../../utils/inventory'
    import { formatDate, hasAnyRole } from '../../utils/helpers'
    export default {
        beforeRouteEnter (to, from, next) {
            hasAnyRole(['admin','manager'], (res)=> {
                if(res) {
                    next()
                } else {
                    next(from)
                }
            })
        },
        created() {
            this.fetchTemplates();
        },
        data() {
            return {
                filter: '',
                templates: [],
                template_headers: [{text:'label', value:'label'}, {text:'description', value:'description'}
                                    ,{text:'add to', value:'apply_on'}, {text:'created on', value:'created_at'}
                                    , {text:'formula', value:'formula'},{text:'apply on category', value:'category'}
                                    ,{text:'status', value:'status'},{text:'actions', value:'buttons'}],
                isApplyLoading: false,
                isLoading: false,   
                myRows: [10,20,30],                 
            }
        },
        methods: {
            fetchTemplates: function() {
                this.isLoading = true;
                GetPriceTemplate({})
                    .then(result=> {
                        this.isLoading = false;
                        this.templates = result.templates
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            goCreateTemplatePage: function(){
                this.$router.push({ name: 'create-price-template' })
            },
            prettyDate: function(date_str) {
                return formatDate(date_str)
            },
            templateFormula: function(row) {
                let base_value = row.base_value || "";
                let percent_value = row.percent_value || "";
                let constant_value = row.constant_value || "";
                return `(${base_value} + ${percent_value}%) + ${constant_value}`;
            },
            productCategory: function(row) {
                let categoryId = row.category_id;
                if(categoryId) {
                    return `All products under ${(row.category || {}).label}`;
                } else {
                    return 'All products'
                }
            },
            templateStatus: function(){
                return 'active'
            },
            deleteTemplate: function(row) {
                let index = this.templates.indexOf(row);
                DeletePriceTemplate(row)
                    .then(result=> {
                        this.templates.splice(index, 1);
                    })
                    .catch(err=> {

                    })
            },
            applyPriceTemplate: function(row) {
                let id = row.id;
                this.isApplyLoading = true;
                ApplyPriceTemplate({template_id:id})
                    .then(result=> {
                        this.isApplyLoading = false
                    })
                    .catch(err=> {
                        this.isApplyLoading = false;
                    })
            }
        },
    }
</script>