<template>
    <div class="container fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-0">
                        <form @submit.prevent="fetchPurchases" class="py-0">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>reference code</label>
                                        <input type="text" class="form-control" v-model="search.ref_code">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>supplier</label>
                                        <select v-model="search.supplier_id" class="form-control">
                                            <option value="">All</option>
                                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                                {{ supplier.name }}
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

        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Reference code</th>
                                            <th>User</th>
                                            <th>Supplier</th>
                                            <th>Date</th>
                                            <th>Invoice Total Cost</th>
                                            <th>Amount Paid</th>
                                            <th>Amount balance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(purchase,index) in purchases" :key="purchase.id">
                                            <td>
                                                {{ purchase.ref_code }}
                                            </td>
                                            <td>
                                                {{ (purchase.user || {}).name }}
                                            </td>
                                            <td>
                                                {{ (purchase.supplier || {}).name }}
                                            </td>
                                            <td>
                                                {{ purchase.created_at }}
                                            </td>
                                            <td>
                                                {{ (purchase.total/100).toFixed(2) }}
                                            </td>
                                            <td>
                                                {{ (purchaseEntryTotalPayment(purchase)/100).toFixed(2) }}
                                            </td>
                                            <td>
                                                {{ (purchaseEntryBalance(purchase)/100).toFixed(2) }}
                                            </td>
                                            <td style="min-width:150px">
                                                <button class="btn btn-secondary btn-sm"
                                                :disabled="(purchaseEntryBalance(purchase)/100)>=0"
                                                @click.stop="openMakePaymentModal(purchase, index)"
                                                variant="">
                                                    <i class="el-icon-plus"></i> pay
                                                </button>
                                                <button class="btn btn-sm btn-primary"
                                                :disabled="isDetailLoading"
                                                @click.stop="getDetail(purchase, index)"
                                                >
                                                    more
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <el-pagination
                                @current-change="changePage"
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

            <el-dialog
                title="purchase entries" 
                :visible.sync="showEntryDetailModal">
                <div class="row py-3">
                    <div class="col-md-8">
                        {{ (purchase_detail.sale || {}).ref_code || "" }}
                    </div>
                    <div class="col-md-4">
                        {{ (purchase_detail.sale || {}).status || "" }}
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
                                <tr v-for="(entry,index) in purchase_detail.entries" :key="index">
                                    <td>{{ entry.product }}</td>
                                    <td>
                                        {{entry.quantity}} {{ entry.stock_unit }}
                                    </td>
                                    <td>{{ ((entry.cost_price || 0)/100).toFixed(2) }}</td>
                                    <td>{{ ((entry.sum || 0)/100).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
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
                                <tr v-for="payment in purchase_detail.payments" :key="payment.id">
                                    <td>{{ (payment.user || {}).name }}</td>
                                    <td>{{ payment.created_at }}</td>
                                    <td>{{ (payment.count/100).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </el-dialog>

            <el-dialog
                title="make payment"
                :visible.sync="showMakePaymentModal"
                width="30%"
                center>
                <form @submit.prevent="makePayment">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>amount:</label>
                                <input 
                                required
                                type="number" step="0.01" class="form-control" v-model="payment.count"
                                style="width:100%">
                            </div>
                            <button class="btn btn-success btn-block" type="submit"
                            :disabled="paymentLoading">
                                <el-icon class="el-icon-circle-plus"></el-icon>
                                pay
                            </button>
                        </div>
                    </div>
                </form>
            </el-dialog>
        </div>
    </div>
</template>

<script>
    import { GetPurchase, ShowPurchase, CreateOutPayment } from '../../utils/pos'
    import { GetSupplier } from '../../utils/inventory'
    export default {
        mounted() {
            this.fetchPurchases();
            this.fetchSuppliers();
        },
        data() {
            return {
                search: {paginate:true, supplier_id:''},
                filter: '',
                purchases: [],
                purchases_header: [{key:'reference_number', label:'reference number'}, {key:'created_at', label:'date'}
                                , {key:'user.name', label:'creator'}, {key:'supplier', label:'supplier'}
                                ,{key:'status', label:'status'}, {key:'invoice_total_cost', label:'invoice total cost(GHC)'}
                                , {key:'out_payments', label:'total payment'},{key:'balance', label:'balance'}
                                , {key:'actions', label:'actions'}],
                credits_header: [{key:'reference_number', label:'reference'}, {key:'user.name', label:'user'}
                                ,{key:'supplier.name', label:'supplier'}, {key:'invoice_total_cost', label:'invoice amount'}
                                ,{key:'total_payment', label:'total payment'}, {key:'balance', label:'balance'}
                                ,{key:'actions', label:'actions'}],                
                credits: [],
                currentPage: 1,
                perPage: 2,
                totalRows: null,

                isLoading: false,
                isDetailLoading: false,
                payment: {},
                purchase_detail: {sale: {}, entries:[], payments:[]},
                showMakePaymentModal: false,
                showEntryDetailModal: false,

                filter_type: 'all',
                filter_types: [{id:'all', label:'All'}, {id:'creditors', label:'Purchases On Credit'}],
                paymentLoading: false,
                suppliers: []
            }
        },
        watch: {
            filter_type: function() {
                this.fetchPurchases();
            },
            currentPage: function() {
                this.fetchPurchases();
            }
        },
        methods: {
            fetchPurchases: function(){
                this.isLoading = true;
                let searchData = this.search;
                searchData.page = this.currentPage;
                GetPurchase(searchData)
                    .then(result=> {
                        console.log('res', result.purchases)
                        this.isLoading = false;
                        this.purchases = result.purchases.data || [];
                        this.perPage = result.purchases.per_page;
                        this.currentPage = result.purchases.current_page;
                        this.totalRows = result.purchases.total;    
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            fetchSuppliers: function() {
                GetSupplier({})
                    .then(result=> {
                        this.suppliers = result.suppliers || []
                    })
                    .catch(err=> {
                        
                    })
            },
            openMakePaymentModal: function(row, index){
                this.payment.purchase_id = row.id;
                this.payment.count = 0;
                this.payment.index = index;
                this.showMakePaymentModal = true;
            },
            makePayment: function() {
                if(confirm('Are you sure you want to make payment')) {
                    this.paymentLoading = true;
                    CreateOutPayment(this.payment)
                        .then(result=> {
                            let res = result || {};
                            this.paymentLoading = false;
                            if(res.code == 0) {
                                location.reload(true);
                            }
                        })
                        .catch(err=> {
                            this.paymentLoading = false;
                        })
                }
            },
            cancelPurchase: function(row) {
                axios.put(`/purchases/${row.id}/cancel`, {status:'inactive'})
                    .then((result)=> {
                        let res = result.data || {};
                        if(res.code == 0) {
                            location.reload(true);
                        } else {
                            Notifier.error(res.message || "");
                        }
                    })
                    .catch((err)=> {
                        handleAjaError(err);
                    })
            },
            getDetail: function(row) {
                this.isDetailLoading = true;
                let saleId = row.id;
                ShowPurchase({id:saleId})
                    .then(result=> {
                        let res = result;
                        console.log('res', res)
                        if(res.code == 0) {
                            this.purchase_detail.entries = res.entries;
                            this.purchase_detail.payments = res.payments;
                            this.purchase_detail.sale = row;
                            this.isDetailLoading = false;
                            this.showEntryDetailModal = true;
                        } else {
                            Notifier.error(res.message)
                        }
                    })
                    .catch(err=> {
                        this.isDetailLoading = false;
                    })
            },
            purchaseEntryTotal: function(row) {
                let totalCost = row.total || 0;
                return totalCost
            },
            purchaseEntryTotalPayment: function(row) {
                let total = 0;
                let payments = row.out_payments || [];
                payments.forEach((entry)=> {
                    let count = entry.count || 0;
                    total += Number(count);
                })
                return total
            },
            purchaseEntryBalance: function(row){
                let paid = this.purchaseEntryTotalPayment(row);
                let cost = this.purchaseEntryTotal(row);
                let balance = paid-cost
                return (isNaN(balance) ? 0 : balance);
            },
            changePage: function() {
                console.log('change', this.currentPage)
            }
        },
        computed: {

        }
    }
</script>

<style scoped>

</style>
