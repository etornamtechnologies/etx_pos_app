<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar color="cyan" dense dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Sales With Debtors</v-toolbar-title>
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
                                <td class="text-xs-left">{{ props.item.user }}</td>
                                <td class="text-xs-left">{{ props.item.customer }}</td>
                                <td class="text-xs-left">{{ prettyDate(props.item.date || "") }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.total_cost || 0) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_paid) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_owed || 0) }}</td>
                                <td>
                                    <button class="btn btn-secondary btn-sm"
                                    @click.stop="openMakePaymentModal(props.item, props.index)"
                                    >Pay</button>
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
                <div class="form-group">
                    <label>amount:</label>
                    <input type="number" step="0.01" class="form-control" v-model="payment.count"
                    style="width:100% !important">
                </div>
                <button 
                :disabled="!validatePayment"
                class="btn btn-success btn-block" type="submit">
                    <el-icon class="el-icon-circle-plus"></el-icon>
                    pay
                </button>
            </form>
        </el-dialog>
    </v-container>
</template>
<script>
    import { GetSaleWithDebt } from '../../utils/ledger'
    import { formatDate } from '../../utils/helpers'
    import { GetSale, ShowSale, CreateInPayment, CancelSale } from '../../utils/pos'
    export default {
        created() {
            this.fetchSale();
        },
        data() {
            return {
                search: '',
                ledgers: [],
                headers: [{text:'Reference Number', value:'ref_code'}, {text:'Sales rep', value:'user'}
                             ,{text: 'Customer', value:'customer'}, {text:'Date', value:'date'}
                            , {text:'Total Invoice Amount (GHC)', value:'total_amount'}, {text:'Amount Paid (GHC)', value:'amount_paid'}
                            , {text:'Amount Owed (GHC)', value:'amount_owed'}, {text:'', value:'actions'}
                            ],
                isLoading: false,
                myRows: [10,20,30],
                sale_detail: {sale: {}, entries:[], payments:[]},
                showMakePaymentModal: false,
                payment: {sale_id:null, count:0},

            }
        },
        methods: {
            fetchSale: function() {
                this.isLoading = true;
                GetSaleWithDebt({})
                    .then(result=> {
                        this.isLoading = false;
                        this.ledgers = result.sales || []
                    })
                    .catch(err=> {
                        this.isLoading = false
                    })
            },
            getMoney: function(amount) {
                return (amount/100).toFixed(2)
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
            saleEntryTotalCost: function(row) {
                let totalCost = row.total_cost || 0;
                return totalCost
            },
            saleEntryTotalPayment: function(row) {
                return row.amount_paid;
            },
            saleEntryBalance: function(row){
                let paid = this.saleEntryTotalPayment(row);
                let cost = this.saleEntryTotalCost(row);
                let balance = paid - cost
                return (isNaN(balance) ? 0 : balance);
            },
            prettyDate: function(date_str) {
                return formatDate(date_str);
            }
        },
        computed: {
            validatePayment: function() {
                let paymentAmount = this.payment.count || 0;
                let saleId = this.payment.sale_id;
                let saleData = (this.ledgers.filter(sale=> {
                    return sale.id == saleId;
                }) || [])[0] || {};
                let balance = (this.saleEntryBalance(saleData) || 0)/100;
                return Math.abs(balance) >= paymentAmount;
            },
        },
    }
</script>