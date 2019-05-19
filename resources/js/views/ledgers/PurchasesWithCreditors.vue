<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar color="cyan" dense dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Purchases With Creditors</v-toolbar-title>
                    </v-toolbar>
                    <v-card-title>
                        <v-spacer></v-spacer>
                        <v-text-field
                        v-model="search"
                        color="cyan"
                        prepend-inner-icon="search"></v-text-field>
                    </v-card-title>
                    <v-card-text style="min-height:400px; position:relative">
                        <div class="my-loader" v-if="isLoading"></div>
                        <v-data-table
                        :search="search"
                        :items="ledgers"
                        :headers="headers"
                        :rows-per-page-items="myRows">
                            <template v-slot:items="props">
                                <td class="text-xs-left">{{ props.item.ref_code }}</td>
                                <td class="text-xs-left">{{ props.item.invoice_number }}</td>
                                <td class="text-xs-left">{{ props.item.user }}</td>
                                <td class="text-xs-left">{{ props.item.supplier }}</td>
                                <td class="text-xs-left">{{ prettyDate(props.item.date || "") }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.total_cost || 0) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_paid) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_owing || 0) }}</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm"
                                    @click.stop="openMakePaymentModal(props.item, props.index)"
                                    variant="">
                                        <i class="el-icon-plus"></i> pay
                                    </button>
                                </td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

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
    </v-container>
</template>
<script>
    import { GetPurchasesWithCredit } from '../../utils/ledger'
    import { GetPurchase, ShowPurchase, CreateOutPayment } from '../../utils/pos'
    import { formatDate } from '../../utils/helpers';
    export default {
        created() {
            this.fetchPurchase();
        },
        data() {
            return {
                search: '',
                ledgers: [],
                headers: [{text:'Reference Number', value:'ref_code'}, 
                             , {text:'vendor Invoice Number', value:''}, {text:'Entered By', value:'user'}
                             ,{text: 'Supplier', value:'supplier'}, {text:'Date', value:'date'}
                            , {text:'Total Invoice Amount (GHC)', value:'total_amount'}, {text:'Amount Paid (GHC)', value:'amount_paid'}
                            , {text:'Amount Owing (GHC)', value:'amount_owing'}, {text:'', value:'actions'}
                            ],
                isLoading: false,
                myRows: [10,20,30],
                showMakePaymentModal: false,
                payment: {},
                paymentLoading: false,
            }
        },
        methods: {
            fetchPurchase: function() {
                this.isLoading = true;
                GetPurchasesWithCredit({})
                    .then(result=> {
                        this.isLoading = false;
                        this.ledgers = result.purchases || []
                    })
                    .catch(err=> {
                        this.isLoading = false
                    })
            },
            getMoney: function(amount) {
                return (amount/100).toFixed(2)
            },
            openMakePaymentModal: function(row, index){
                console.log('row', row)
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
            purchaseEntryBalance: function(row){
                let paid = this.purchaseEntryTotalPayment(row);
                let cost = this.purchaseEntryTotal(row);
                let balance = paid-cost
                console.log('balance',balance);
                return (isNaN(balance) ? 0 : balance);
            },
            purchaseEntryTotal: function(row) {
                let totalCost = row.total || 0;
                return totalCost
            },
            purchaseEntryTotalPayment: function(row) {
                console.log('row',row);
                let total = 0;
                let payments = row.out_payments || [];
                payments.forEach((entry)=> {
                    let count = entry.count || 0;
                    total += Number(count);
                })
                return total
            },
            prettyDate: function(date) {
                return formatDate(date);
            }
        },
    }
</script>