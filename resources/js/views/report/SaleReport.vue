<template>
    <div class="container fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-0">
                        <div class="row">
                            <div class="col-md-3 bg-light" style="border-right:solid 1px gray">
                                <div class="row mt-4" style="min-height:500px">
                                    <div class="col-md-12">
                                        <form @submit.prevent="generateReport">
                                            <el-select
                                            v-if="search.filter_by == 'transaction'"
                                            v-model="search.user_id" placeholder="sales-rep" class="my-input">
                                                <el-option
                                                value=""
                                                label="All Sales Reps"></el-option>
                                                <el-option
                                                v-for="user in users"
                                                :key="user.id"
                                                :value="user.id"
                                                :label="user.name"></el-option>
                                            </el-select>

                                            <el-select v-model="search.filter_by" placeholder="view by" class="my-input">
                                                <el-option
                                                @change="handleFilterByChange"
                                                v-for="filter in filterByList"
                                                :key="filter.id"
                                                :value="filter.id"
                                                :label="filter.name"></el-option>
                                            </el-select>
                                            

                                            <el-input
                                            class="my-input"
                                            v-if="search.filter_by == 'transaction'"
                                            v-model="search.invoice_number"
                                            placeholder="invoice number"></el-input>

                                            <el-input
                                            class="my-input"
                                            v-if="search.filter_by == 'product'"
                                            v-model="search.product"
                                            placeholder="product"></el-input>

                                            <el-checkbox
                                            class="my-input"
                                            @change="handleChangeRange"
                                            v-model="search.is_date_range">Date range</el-checkbox>

                                            <el-date-picker
                                            :clearable=false
                                            v-if="!search.is_date_range"
                                            class="my-input"
                                            v-model="date"
                                            type="date"
                                            placeholder="pick a date"></el-date-picker>

                                            <el-date-picker
                                            v-if="search.is_date_range"
                                            class="my-input"
                                            v-model="date_range"
                                            type="daterange"
                                            align="right"
                                            unlink-panels
                                            range-separator="-"
                                            start-placeholder="Start date"
                                            end-placeholder="End date"
                                            :picker-options="rangeOptions"></el-date-picker>
                                            <button class="btn btn-info btn-block" type="submit" style="margin-bottom:10px">
                                                GENERATE REPPORT
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9" v-if="search.filter_by == 'transaction'">
                                <div class="row bg-light py-1">
                                    <div class="col-md-12">
                                        <span>: {{ transaction_info }}</span>
                                    </div>
                                </div>
                                <div class="row" style="border-bottom: solid grey 1px">
                                    <div class="col-md-12">
                                        <span>TOTAL SALE: GHC{{ getMoney(total_amount) }} </span>
                                    </div>
                                </div>
                                <div class="row" style="height:500px; overflow-y:auto">
                                    <div class="col-md-12">
                                        <v-data-table
                                        :items="report_by_transaction"
                                        :headers="transaction_headers"
                                        :hide-actions="true">
                                            <template v-slot:items="props">
                                                <td>
                                                    {{ props.item.ref_code }}
                                                </td>
                                                <td>
                                                    {{ props.item.created_at }}
                                                </td>
                                                <td>
                                                    {{ (props.item.user || {}).name }}
                                                </td>
                                                <td>
                                                    {{ (props.item.customer || {}).name || 'Anonymous' }}
                                                </td>
                                                <td>
                                                    {{ getMoney(props.item.total_cost) }}
                                                </td>
                                            </template>
                                        </v-data-table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9" v-if="search.filter_by == 'product'">
                                <div class="row bg-light py-1">
                                    <div class="col-md-12">
                                        <span>: {{ product_report_info }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <span>{{ getMoney(product_total_amount) }}</span>
                                    </div>
                                </div>
                                <div class="row" style="height:500px; overflow-y:auto">
                                    <div class="col-md-12">
                                        <v-data-table
                                        :items="report_by_products"
                                        :headers="product_headers"
                                        :hide-actions="true">
                                            <template v-slot:items="props">
                                                <td>
                                                    {{ props.item.product_label }}
                                                </td>
                                                <td>
                                                    {{ props.item.created_at }}
                                                </td>
                                                <td>
                                                    {{ props.item.quantity }} {{ props.item.stock_unit_label }}
                                                </td>
                                                <td>
                                                    {{ getMoney(props.item.selling_price || 0) }}
                                                </td>
                                                <td>
                                                    {{ getMoney(props.item.amount || 0) }}
                                                </td>
                                            </template>
                                        </v-data-table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                search: {user_id:"",filter_by:'transaction', is_date_range: false
                            , from_date:'', to_date:'', product:'', invoice_number:''},
                report_by_transaction: [],
                transaction_headers: [],
                transaction_info: '',
                total_amount: "",
                report_by_products: [],
                product_headers: [],
                product_report_info: '',
                product_total_amount: '',
                users: [],
                date_range: [],
                date: new Date(),
                rangeOptions: {
                    shortcuts: [
                        {
                            text: 'Last week',
                            onClick(picker) {
                                const end = new Date();
                                const start = new Date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                                picker.$emit('pick', [start, end]);
                            }
                        },
                        {
                            text: 'Last month',
                            onClick(picker) {
                                const end = new Date();
                                const start = new date();
                                start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                                picker.$emit('pick', [start, end]);
                            }
                        }
                    ]
                }
            }
        },
        methods: {
            generateReport: function() {
                let startDate = null;
                let endDate = null;
                let myDate = null;
                if(this.date_range.length == 2) {
                    startDate = new Date(this.date_range[0]).toLocaleDateString();
                    endDate = new Date(this.date_range[1]).toLocaleDateString();
                }
                if(this.date) {
                    myDate = new Date(this.date).toLocaleDateString();
                }
                let data = Vue.util.extend({}, this.search);
                if(data.filter_by == 'product') {
                    data.user_id = null
                }
                data.date = myDate;
                data.from_date = startDate;
                data.to_date = endDate;
                if(data.filter_by == 'transaction') {
                    this.fetchByTransaction(data)
                } else {
                    this.fetchByProduct(data)
                }
            },
            fetchUsers: function() {
                GetUser({})
                    .then(result=> {
                        this.users = result.users || [];
                    })
            },
            fetchByTransaction: function(_data) {
                GetSaleReportByTransaction(_data)
                    .then(result=> {
                        console.log(result.reports)
                        this.report_by_transaction = result.reports;
                        this.transaction_headers = result.headers
                        this.transaction_info = result.info;
                        this.total_amount = result.total_amount;
                    })
                    .catch(err=> {

                    })
            },
            fetchByProduct: function(_data) {
                GetSaleReportByProduct(_data)
                    .then(result=> {
                        this.report_by_products = result.reports;
                        this.product_headers = result.header;
                        this.product_report_info = result.info
                        this.product_report_info = result.info
                        this.product_total_amount = result.total_amount || 0;
                        console.log('head', this.product_headers)
                    })
                    .catch(err=> {

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
                console.log('heye')
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
            
        },
    }
</script>

<style scoped>
    .my-input {
        width: 100% !important;
        margin-bottom: 30px;
    }

</style>
