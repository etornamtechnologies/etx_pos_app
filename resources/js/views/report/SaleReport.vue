<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense dark color="cyan">
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Sale Report</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <div class="row">
                            <div class="col-md-3 col-sm-12 col-xs-12">
                                <v-form
                                @submit.prevent="generateReport"
                                >
                                    <v-select
                                    label="User / Sales-Rep"
                                    :items="usersList"
                                    item-text="name"
                                    item-value="id"
                                    v-model="search.user_id"></v-select>
                                    <v-select
                                    label="View By"
                                    :items="filterByList"
                                    item-text="name"
                                    item-value="id"
                                    v-model="search.filter_by"></v-select>
                                    <v-text-field
                                    v-if="search.filter_by == 'transaction'"
                                    label="Invoice Number"
                                    v-model="search.invoice_number"></v-text-field>
                                    <v-text-field
                                    v-if="search.filter_by == 'product'"
                                    label="Product"
                                    v-model="search.product"></v-text-field>
                                    <v-checkbox
                                    label="Date Range"
                                    color="cyan"
                                    v-model="search.is_date_range"></v-checkbox>
                                    <v-menu
                                    v-if="!search.is_date_range"
                                    v-model="dateMenu"
                                    :close-on-content-click="false"
                                    full-width
                                    max-width="290"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                            :value="date"
                                            label="DATE"
                                            readonly
                                            v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="date"
                                            @change="dateMenu = false"
                                        ></v-date-picker>
                                    </v-menu>
                                    <div
                                    v-if="search.is_date_range">
                                        <v-menu
                                        v-model="fromDateMenu"
                                        :close-on-content-click="false"
                                        full-width
                                        max-width="290"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                :value="fromDate"
                                                label="From Date"
                                                readonly
                                                v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="fromDate"
                                                @change="fromDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                        <v-menu
                                        v-model="toDateMenu"
                                        :close-on-content-click="false"
                                        full-width
                                        max-width="290"
                                        >
                                            <template v-slot:activator="{ on }">
                                                <v-text-field
                                                :value="toDate"
                                                label="To Date"
                                                readonly
                                                v-on="on"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                v-model="toDate"
                                                @change="toDateMenu = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </div>
                                    <v-btn
                                    type="submit"
                                    color="cyan"
                                    dark>
                                        <v-icon>filter</v-icon> 
                                        <span>generate</span>
                                    </v-btn>
                                </v-form>
                            </div>
                            <div
                            v-if="search.filter_by == 'transaction'" 
                            class="col-md-9 col-sm-12 col-xs-12 py-2" 
                            style="background-color:#d8e6eb; min-height:500px; position:relative">
                                <div class="my-loader" v-if="isLoading"></div>
                                <div class="row px-3">
                                    <div class="col-md-12 bg-light">
                                        <span style="font-weight:bold">INFO: {{ info_transaction }} </span>
                                    </div>
                                </div>
                                <div class="row px-3 mb-2">
                                    <div class="col-md-12 bg-light">
                                        <span style="font-weight:bold">
                                            TOTAL AMOUNT SOLD: GHC{{ getMoney(total_amount_transaction) }} 
                                        </span>
                                        <span style="font-weight:bold; float:right">
                                            TOTAL PAID: GHC{{ getMoney(total_paid_transaction || 0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="height:550px; overflow-y:auto">
                                        <v-data-table
                                        :items="reports_transaction"
                                        :headers="headers_transaction"
                                        hide-actions>
                                            <template v-slot:items="props">
                                                <td>{{ props.item.ref_code }}</td>
                                                <td class="text-xs-left">{{ props.item.created_at }}</td>
                                                <td class="text-xs-left">{{ (props.item.user || {}).name || '--' }}</td>
                                                <td class="text-xs-left">{{ (props.item.customer || {}).name || '--' }}</td>
                                                <td class="text-xs-left">{{ getMoney(props.item.total_cost) || 0 }}</td>
                                                <td class="text-xs-left">{{ getMoney(props.item.paid) || 0 }}</td>
                                            </template>
                                            <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                                                Your search for "{{ search }}" found no results.
                                            </v-alert>
                                        </v-data-table>
                                    </div>
                                </div>
                            </div>

                            <div
                            v-if="search.filter_by == 'product'" 
                            class="col-md-9 col-sm-12 col-xs-12 py-2" 
                            style="background-color:#d8e6eb; min-height:500px">
                                <div class="my-loader" v-if="isLoading"></div>
                                <div class="row px-3">
                                    <div class="col-md-12 bg-light">
                                        <span style="font-weight:bold">INFO: {{ info_product }} </span>
                                    </div>
                                </div>
                                <div class="row px-3 mb-2">
                                    <div class="col-md-12 bg-light">
                                        <span style="font-weight:bold">
                                            TOTAL AMOUNT SOLD: GHC{{ getMoney(total_amount_product) }} 
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="height:550px; overflow-y:auto">
                                        <v-data-table
                                        :items="reports_product"
                                        :headers="headers_product"
                                        hide-actions>
                                            <template v-slot:items="props">
                                                <td>{{ (props.item.product || {}).label }}</td>
                                                <td class="text-xs-left">{{ props.item.created_at }}</td>
                                                <td class="text-xs-left">
                                                    {{ props.item.quantity }} {{ (props.item.stock_unit || {}).label }} (s)
                                                </td>
                                                <td class="text-xs-left">{{ getMoney(props.item.selling_price) || 0 }}</td>
                                                <td class="text-xs-left">{{ getMoney(props.item.amount) || 0 }}</td>
                                            </template>
                                            <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                                                Your search for "{{ search }}" found no results.
                                            </v-alert>
                                        </v-data-table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetUser } from '../../utils/user' 
    import { GetSaleReportByTransaction, GetSaleReportByProduct } from '../../utils/report'
    export default {
        created() {
            this.fetchUsers();
        },
        data(){
            return {
                search: {user_id:"",filter_by:'transaction', is_date_range: false, product:'', invoice_number:''},
                reports_transaction: [],
                headers_transaction: [],
                info_transaction: '',
                total_amount_transaction: "",
                total_paid_transaction: "",

                reports_product: [],
                headers_product: [],
                info_product: '',
                total_amount_product: "",
                users: [],
                date_range: [],

                date: new Date().toISOString().substring(0, 10),
                dateMenu: false,

                fromDate: new Date().toISOString().substring(0, 10),
                fromDateMenu: false,

                toDate: new Date().toISOString().substring(0, 10),
                toDateMenu: false,
                isLoading: false,
            }
        },
        methods: {
            generateReport: function() {
                let searchData = Vue.util.extend({}, this.search);
                searchData.from_date = "";
                searchData.to_date = "";
                searchData.date = "";
                if(searchData.is_date_range) {
                    searchData.from_date = this.fromDate;
                    searchData.to_date = this.toDate;
                } else {
                    searchData.date = this.date;
                }
                if(searchData.filter_by == 'product') {
                    searchData.user_id = null
                }
                if(searchData.filter_by == 'transaction') {
                    this.fetchByTransaction(searchData)
                } else {
                    this.fetchByProduct(searchData)
                }
            },
            fetchUsers: function() {
                GetUser({})
                    .then(result=> {
                        this.users = result.users || [];
                    })
            },
            fetchByTransaction: function(_data) {
                this.isLoading = true;
                GetSaleReportByTransaction(_data)
                    .then(result=> {
                        this.isLoading = false;
                        this.reports_transaction = result.reports;
                        this.headers_transaction = result.headers
                        this.info_transaction = result.info;
                        this.total_amount_transaction = result.total_amount;
                        this.total_paid_transaction = result.total_paid;
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })  
            },
            fetchByProduct: function(_data) {
                this.isLoading = true;
                GetSaleReportByProduct(_data)
                    .then(result=> {
                        this.isLoading = false;
                        this.reports_product = result.reports;
                        this.headers_product = result.header;
                        this.info_product = result.info
                        this.total_amount_product = result.total_amount || 0;
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            handleChangeRange: function(event){
                if(event == true) {
                    this.date = "";
                } else {
                    this.date_range = [];
                }
            },
            handleFilterByChange: function(event){
                if(event == 'transaction') {
                    this.report_by_products = [];
                    this.product_headers = [];
                    this.product_report_info = ""
                    this.product_total_amount = 0;
                } else {
                    this.report_by_transaction = [];
                    this.transaction_headers = []
                    this.transaction_info = "";
                    this.total_amount = 0;
                }    
            },
            getMoney: function(val) {
                let amt = val/100;
                return amt.toFixed(2)
            }

        },
        computed: {
            filterByList: function(){
                return [
                    {id:'product', name:'Product'},
                    {id:'transaction', name:'Transaction'}
                ];
            },
            usersList: function() {
                let entries = this.users || [];
                entries.push({id:'', name:'All Users'});
                return entries;
            },
            filterByList: function(){
                return [
                    {id: 'transaction', name:'Transaction'},
                    {id: 'product', name: 'Product'}
                ]
            }
            
        },
    }
</script>

<style scoped>
    .my-input {
        width: 100% !important;
        margin-bottom: 30px;
    }

</style>
