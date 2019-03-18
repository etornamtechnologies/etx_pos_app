<template>
    <div class="container fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-1">
                        Sales
                    </div>
                    <div class="card-body" style="position: relative">
                        <div class="page-loader" v-if="isLoading"></div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <form @submit.prevent = "fetchSales">
                                    <v-text-field
                                    v-model="filter"
                                    prepend-inner-icon="search"></v-text-field>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3"><span class="float-right">sale type</span></div>
                                    <div class="col-md-9">
                                        <select v-model="filter_type" class="form-control form-control-sm">
                                            <option v-for="type in filter_types" :value="type.id" :key="type.id">
                                                {{ type.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="max-height:730px; overflow-y:auto">
                                <v-data-table
                                :headers="headers"
                                :items="getTableByType"
                                :search="filter"
                                >
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
                                            {{ (props.item.customer || {}).name }}
                                        </td>
                                        <td>
                                            <div
                                            class="text-white py-0 px-1 text-center" style="border-radius:10px"
                                            :class="props.item.status == 'active' ? 'bg-info' : 'bg-danger'">
                                                {{ props.item.status }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ (saleEntryTotalCost(props.item)/100).toFixed(2) }}
                                        </td>
                                        <td class="text-xs-right">
                                            {{ (props.item.paid/100) }}
                                        </td>
                                        <td class="text-xs-right">
                                            {{ (saleEntryBalance(props.item)/100).toFixed(2) }}
                                        </td>
                                        <td class="text-xs-right">
                                            <v-btn
                                            :disabled="saleEntryBalance(props.item) >= 0 || props.item.status == 'inactive'"
                                            @click.stop="openMakePaymentModal(props.item, props.index)">
                                                pay
                                            </v-btn>

                                            <v-btn
                                            icon
                                            color="warning"
                                            :disabled="props.item.status == 'inactive' || filter_type!='all'"
                                            @click.stop="cancelSale(props.item, props.index)">
                                                <v-icon>close</v-icon>
                                            </v-btn>

                                            <v-btn
                                            icon
                                            :disabled="isDetailLoading"
                                            color="primary"
                                            @click.stop="getDetail(props.item, props.index)">
                                                <v-icon>chevron_right</v-icon>
                                            </v-btn>
                                        </td>
                                    </template>
                                    <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                                        Your search for "{{ search }}" found no results.
                                    </v-alert>
                                </v-data-table>
                            </div>
                        </div>
                        <div class="row" v-if="filter_type == 'all'">
                            <div class="col-md-12 block justify-content-center">
                                <el-pagination
                                    layout="prev, pager, next"
                                    background
                                    :page-size="perPage"
                                    :total="totalRows"
                                    :current-page.sync="currentPage"
                                    :pager-count="11"
                                    @current-change="fetchSales">
                                </el-pagination>
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
                        <input type="number" step="0.01" class="form-control" v-model="payment.count">
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
                                    <th>Quantity</th>
                                    <th>Unit-Price</th>
                                    <th>Sum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(entry,index) in sale_detail.entries" :key="index">
                                    <td>{{ entry.product }}</td>
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
    </div>
</template>

<script>
    import {GetProduct} from '../../utils/product'
    import { GetSale, ShowSale } from '../../utils/pos'
    import { Message } from 'element-ui';
    export default {
        mounted() {
            this.fetchSales();
        },
        watch: {
            filter_type: function() {
                this.fetchSales();
            }
        },
        data() {
            return {
                filter: '',
                sales: [],
                headers: [{value:'reference_number', text:'reference number'}, {value:'created_at', text:'date'}
                                , {value:'user', text:'sales rep'}, {value:'customer', text:'customer'}
                                ,{value:'status', text:'status'}, {value:'total_cost', text:'total cost (GHC)'}
                                , {value:'in_payments', text:'total payment'},{value:'balance', text:'balance'}
                                , {value:'actions', text:'actions'}],
                debts: [],
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

            }
        },
        methods: {
            fetchSales: function(){
                this.isLoading = true;
                GetSale({filter:this.filter, type: this.filter_type})
                    .then(result=> {
                        this.sales = [];
                        this.bebts = [];
                        console.log(result.sales);
                        if(this.filter_type == 'all') {
                            this.sales = result.sales || []
                        } else {
                            this.debts = result.debts || [];
                        }
                    })
                    .catch((err)=> {
                        this.isLoading = false;
                    })
            },
            openMakePaymentModal: function(row, index){
                this.payment.sale_id = row.id;
                this.payment.count = 0;
                this.payment.index = index;
                this.showMakePaymentModal = true;
            },
            makePayment: function() {
                axios.post(`/in-payments`, this.payment)
                    .then((result)=> {
                        let res = result.data || {};
                        if(res.code == 0) {
                            location .reload(true);
                        } else {
                            Notifier.error(res.message);
                        }
                    })
                    .catch((err)=> {
                        handleAjaError(err);
                    })
            },
            cancelSale: function(row) {
                axios.put(`/sales/${row.id}/cancel`, {status:'inactive'})
                    .then((result)=> {
                        let res = result.data || {};
                        if(res.code == 0) {
                            location.reload(true);
                        } else {
                            Notifier.error(res.message || "");
                        }
                    })
                    .catch((err)=> {
                        handleAjaxError(err);
                    })
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
