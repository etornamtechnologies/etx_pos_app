<template>
    <div class="container fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-0">
                        <form @submit.prevent="fetchSales" class="py-0">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Receipt Number</label>
                                        <input type="text" class="form-control" v-model="search.ref_code">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>customer</label>
                                        <select v-model="search.customer_id" class="form-control">
                                            <option value="">All</option>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                {{ customer.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-success" 
                                    type="submit"
                                    style="margin-top:26px">
                                        filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card" style="position:relative; min-height:500px">
                    <div class="my-loader" v-if="isLoading"></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Reference code</th>
                                            <th>Sale rep</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Amount due</th>
                                            <th>Amount Paid</th>
                                            <th>Balance</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody style="position:relative">
                                        <tr v-for="(sale,index) in sales" :key="sale.id">
                                            <td>
                                                {{ sale.ref_code }}
                                            </td>
                                            <td>
                                                {{ (sale.user || {}).name }}
                                            </td>
                                            <td>
                                                {{ (sale.customer || {}).name }}
                                            </td>
                                            <td>
                                                {{ sale.created_at }}
                                            </td>
                                            <td>
                                                <div
                                                class="text-white py-0 px-1 text-center" style="border-radius:10px"
                                                :class="sale.status == 'active' ? 'bg-info' : 'bg-danger'">
                                                    {{ sale.status }}
                                                </div>
                                            </td>
                                            <td>
                                                {{ (saleEntryTotalCost(sale)/100).toFixed(2) }}
                                            </td>
                                            <td>
                                                {{ (sale.paid/100).toFixed(2) }}
                                            </td>
                                            <td class="text-xs-right">
                                                {{ (saleEntryBalance(sale)/100).toFixed(2) }}
                                            </td>
                                            <td style="min-width:180px">
                                                <button class="btn btn-secondary btn-sm"
                                                :disabled="saleEntryBalance(sale) >= 0 || sale.status == 'inactive'"
                                                @click.stop="openMakePaymentModal(sale, index)"
                                                >Pay</button>


                                                <button
                                                class="btn-sm btn btn-warning"
                                                :disabled="sale.status == 'cancelled' || filter_type!='all'"
                                                @click.stop="cancelSale(sale, index)">
                                                    cancel
                                                </button>

                                                <button
                                                class="btn btn-primary btn-sm"
                                                :disabled="isDetailLoading"
                                                @click.stop="getDetail(sale, index)">
                                                    <v-icon small color="white">info</v-icon>
                                                </button>
                                            </td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                                <el-pagination
                                background
                                :current-page.sync="currentPage"
                                :page-size="perPage"
                                :pager-count="21"
                                layout="prev, pager, next"
                                :total="totalRows">
                                </el-pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <el-dialog
            title="make payment"
            :visible.sync="showMakePaymentModal"
            width="30%"
            center>
            <form @submit.prevent="makePayment">
                <div class="form-group">
                    <label>amount:</label>
                    <input type="number" step="0.01" class="form-control" v-model="payment.count"
                    style="width:100% !important">
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <el-icon class="el-icon-circle-plus"></el-icon>
                    pay
                </button>
            </form>
        </el-dialog>

        <el-dialog
            title="sale entries" 
            :visible.sync="showEntryDetailModal">
            <div class="row py-3">
                <div class="col-md-8">
                    {{ (sale_detail.sale || {}).reference_number || "" }}
                </div>
                <div class="col-md-4">
                    {{ (sale_detail.sale || {}).status || "" }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Status</th>
                                <th>Quantity</th>
                                <th>Unit-Price</th>
                                <th>Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(entry,index) in sale_detail.entries" :key="index">
                                <td>{{ entry.product }}</td>
                                <td>{{ entry.status }}</td>
                                <td>
                                    {{entry.quantity}} {{ entry.stock_unit }}
                                </td>
                                <td>{{ ((entry.selling_price || 0)/100).toFixed(2) }}</td>
                                <td>{{ ((entry.sum || 0)/100).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <span> Payment history </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Paid By</th>
                                <th>Date</th>
                                <th>Amount (GHC)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="payment in sale_detail.payments" :key="payment.id">
                                <td>{{ (payment.user || {}).name }}</td>
                                <td>{{ payment.created_at }}</td>
                                <td>{{ (payment.count/100).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>            
        </el-dialog>
    </div>
</template>

<script>
    import {GetProduct} from '../../utils/product'
    import { GetSale, ShowSale, CreateInPayment, CancelSale } from '../../utils/pos'
    import { GetCustomer, } from '../../utils/inventory'
    import { Message } from 'element-ui';
    export default {
        mounted() {
            this.fetchCustomers();
            this.fetchSales();
        },
        watch: {
            filter_type: function() {
                this.fetchPurchases();
            },
            currentPage: function() {
                this.fetchSales();
            }
        },
        data() {
            return {
                filter: '',
                search: {ref_code:'', customer_id:'',paginate:true},
                sales: [],
                headers: [{value:'reference_number', text:'reference number'}, {value:'created_at', text:'date'}
                                , {value:'user', text:'sales rep'}, {value:'customer', text:'customer'}
                                ,{value:'status', text:'status'}, {value:'total_cost', text:'total cost (GHC)'}
                                , {value:'in_payments', text:'total payment'},{value:'balance', text:'balance'}
                                , {value:'actions', text:'actions'}],
                debts: [],
                customers: [],
                currentPage: 1,
                perPage: 2,
                totalRows: null,

                isLoading: false,
                isDetailLoading: false,
                payment: {},
                sale_detail: {sale: {}, entries:[], payments:[]},
                showMakePaymentModal: false,
                showEntryDetailModal: false,
                filter_type: 'all',
                filter_types: [{id:'all', label:'All Sales'}, {id:'debts', label:'Sales with debtors'}],
                paymentLoading: false,

            }
        },
        methods: {
            fetchSales: function(){
                this.isLoading = true;
                let saleData = this.search;
                saleData.page = this.currentPage;
                GetSale(saleData)
                    .then(result=> {
                        console.log(result.sales.data)
                        this.isLoading = false;
                        this.sales = result.sales.data || [];
                        this.perPage = result.sales.per_page;
                        this.currentPage = result.sales.current_page;
                        this.totalRows = result.sales.total;  
                    })
                    .catch((err)=> {
                        this.isLoading = false;
                    })
            },
            fetchCustomers: function() {
                GetCustomer({})
                    .then(result=> {
                        this.customers = result.customers || [];
                    })
                    .catch(err=> {

                    })
            },
            openMakePaymentModal: function(row, index){
                this.payment.sale_id = row.id;
                this.payment.count = 0;
                this.payment.index = index;
                this.showMakePaymentModal = true;
            },
            makePayment: function() {
                if(confirm('Are you sure you want to make payment')) {
                    this.paymentLoading = true;
                    CreateInPayment(this.payment)
                        .then(result=> {
                            this.paymentLoading = false;
                            location.reload(true)
                        })
                        .catch(err=> {
                            this.paymentLoading = false;
                        })
                }    
            },
            cancelSale: function(row) {
                let data = row;
                data.status = 'cancelled';
                if(confirm(`Are you sure you want to cancel sale quote (${row.ref_code})`)) {
                    CancelSale(data)
                    .then(result=> {
                        location.reload(true)
                    })
                }
            },
            getDetail: function(row) {
                this.isDetailLoading = true;
                let saleId = row.id;
                ShowSale(row)
                    .then(result=> {
                        this.sale_detail.entries = result.entries;
                            this.sale_detail.payments = result.payments;
                            this.sale_detail.sale = row;
                            this.isDetailLoading = false;
                            this.showEntryDetailModal = true;
                    })
                    .catch(err=> {
                        this.isDetailLoading = false;
                    })
            },
            saleEntryTotalCost: function(row) {
                let totalCost = row.total_cost || 0;
                return totalCost
            },
            saleEntryTotalPayment: function(row) {
                let total =0;
                let payments = row.in_payments || [];
                payments.forEach((entry)=> {
                    let count = entry.count || 0;
                    total += Number(count);
                })
                return total
            },
            saleEntryBalance: function(row){
                let paid = this.saleEntryTotalPayment(row);
                let cost = this.saleEntryTotalCost(row);
                let balance = paid-cost
                return (isNaN(balance) ? 0 : balance);
            },
        },
        computed: {
            getTableByType: function(){
                let list = [];
                if(this.filter_type == 'all') {
                    return this.sales;
                } else {
                    return this.debts;
                }
            }
        }
    }
</script>

<style scoped>

</style>
