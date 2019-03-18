<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-1">
                    purchases
                </div>
                <div class="card-body" style="position: relative">
                    <div class="page-loader" v-if="isLoading"></div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <form @submit.prevent = "fetchPurchases">
                                <b-form-group horizontal label="Filter" class="mb-0">
                                    <b-input-group>
                                        <b-form-input v-model="filter" placeholder="Type to Search" autocomplete="off"/>
                                        <b-input-group-append>
                                            <b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
                                        </b-input-group-append>
                                    </b-input-group>
                                    <button type="submit" style="display:none"></button>
                                </b-form-group>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3"> <span class="float-right">type</span></div>
                                <div class="col-md-9">
                                    <select v-model="filter_type" class="form-control">
                                        <option v-for="type in filter_types" :value="type.id" :key="type.id">
                                            {{type.label}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="filter_type == 'all'">
                        <div class="col-md-12">
                            <b-table
                                stacked="md"
                                :items="purchases"
                                :fields="purchases_header"
                            >
                                <template slot="invoice_total_cost" slot-scope="row">
                                    {{ (purchaseEntryTotal(row.item)/100).toFixed(2) }}
                                </template>
                                <template slot="out_payments" slot-scope="row">
                                    {{ (purchaseEntryTotalPayment(row.item)/100).toFixed(2) }}
                                </template>
                                <template slot="balance" slot-scope="row">
                                    {{ (purchaseEntryBalance(row.item)/100).toFixed(2) }}
                                </template>
                                <template slot="supplier" slot-scope="row">
                                    {{ ((row.item.supplier || {}).name) || "Anonymous" }}
                                </template>
                                <template slot="status" slot-scope="row">
                                    <div 
                                    class="text-white py-0 px-1 text-center" style="border-radius:10px"
                                    :class="row.item.status == 'active' ? 'bg-info' : 'bg-danger'">
                                        {{ row.item.status }}
                                    </div>
                                </template>
                                <template slot="actions" slot-scope="row" >
                                    <b-button size="sm"
                                        :disabled="purchaseEntryBalance(row.item) >= 0 || row.item.status == 'inactive'"
                                        @click.stop="openMakePaymentModal(row.item, row.index)"
                                        variant="">
                                        <i class="el-icon-edit"></i> Add payment
                                    </b-button>
                                    <b-button size="sm"
                                        :disabled="isDetailLoading"
                                        @click.stop="getDetail(row.item, row.index)"
                                        variant="primary">
                                        Ream more
                                    </b-button>
                                </template>
                            </b-table>
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
                                @current-change="fetchPurchases">
                            </el-pagination>
                        </div>
                    </div>
                    <div class="row" v-if="filter_type == 'creditors'">
                        <div class="col-md-12" style="height:700px; overflow-y:auto">
                            <b-table
                                stacked="md"
                                :items="credits"
                                :fields="credits_header"
                            >
                                <template slot="invoice_total_cost" slot-scope="row">
                                    {{ (row.item.invoice_total_cost/100).toFixed(2) }}
                                </template>
                                <template slot="total_payment" slot-scope="row">
                                    {{ (purchaseEntryTotalPayment(row.item)/100).toFixed(2) }}
                                </template>
                                <template slot="balance" slot-scope="row">
                                    {{ (purchaseEntryBalance(row.item)/100).toFixed(2) }}
                                </template> 
                                <template slot="actions" slot-scope="row" >
                                    <b-button size="sm"
                                        @click.stop="openMakePaymentModal(row.item, row.index)"
                                        variant="">
                                        <i class="el-icon-edit"></i> Add payment
                                    </b-button>
                                    <b-button size="sm"
                                        :disabled="isDetailLoading"
                                        @click.stop="getDetail(row.item, row.index)"
                                        variant="primary">
                                        Ream more
                                    </b-button>
                                </template>                               
                            </b-table>
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
            title="purchase entries" 
            :visible.sync="showEntryDetailModal">
            <div class="row py-3">
                <div class="col-md-8">
                    {{ (purchase_detail.sale || {}).reference_number || "" }}
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
    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetchPurchases();
        },
        data() {
            return {
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
                purchase_detail: {sale: {}, entries:[], paymenyts:[]},
                showMakePaymentModal: false,
                showEntryDetailModal: false,

                filter_type: 'all',
                filter_types: [{id:'all', label:'All'}, {id:'creditors', label:'Purchases On Credit'}]


            }
        },
        watch: {
            filter_type: function() {
                this.fetchPurchases();
            }
        },
        methods: {
            fetchPurchases: function(){
                this.isLoading = true;
                axios.get(`/purchases?paginate=${true}&page=${this.currentPage}&filter=${this.filter}&type=${this.filter_type}`)
                    .then((result)=> {
                        this.isLoading = false;
                        let res = result.data || {};
                        this.purchases = [];
                        this.credits = [];
                        if(res.code == 0) {
                            if(this.filter_type == 'all') {
                                this.purchases = res.purchases.data;
                                this.perPage = res.purchases.per_page;
                                this.currentPage = res.purchases.current_page;
                                this.totalRows = res.purchases.total;
                            } else {
                                this.credits = res.credits;
                                console.log('all', this.credits)
                            }
                        } else {
                            Notifier.error(res.message)
                        }
                    })
                    .catch((err)=> {
                        this.isLoading = false;
                        handleAjaxError(err);
                    })
            },
            openMakePaymentModal: function(row, index){
                this.payment.purchase_id = row.id;
                this.payment.count = 0;
                this.payment.index = index;
                this.showMakePaymentModal = true;
            },
            makePayment: function() {
                console.log('d', this.payment)
                if(confirm('Are you sure you want to make payment')) {
                    axios.post(`/out-payments`, this.payment)
                        .then((result)=> {
                            let res = result.data || {};
                            if(res.code == 0) {
                                location .reload(true);
                            } else {
                                Notifier.error(res.message);
                            }
                        })
                        .catch((err)=> {
                            handleAjaxError(err);
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
                axios.get(`/purchases/${saleId}`)
                    .then((result)=> {
                        let res = result.data || {};
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
                    .catch((err)=> {
                        this.isDetailLoading = false;
                        handleAjaError(err);
                    })
            },
            purchaseEntryTotal: function(row) {
                let totalCost = row.invoice_total_cost || 0;
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
        },
        computed: {

        }
    }
</script>

<style scoped>

</style>
