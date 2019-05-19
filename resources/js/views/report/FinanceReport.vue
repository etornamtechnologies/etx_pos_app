<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense color="cyan" dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Financial Report</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-layout column style="padding:10px">
                            <v-flex xs12>
                                <span style="font-weight:bold; font-size:16px">
                                    TOTAL PRODUCTS VALUE: GHC{{ getMoney(total_value) }}
                                </span>
                            </v-flex>
                            <v-flex xs12>
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        <v-form
                                        style="padding:20px"
                                        @submit.prevent="generateReport">
                                            <v-layout row wrap>
                                                <v-flex xs12 sm12 md4>
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
                                                </v-flex>
                                                <v-flex xs12 sm12 md2></v-flex>
                                                <v-flex xs12 sm12 md4>
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
                                                </v-flex>
                                                <v-flex xs12 sm12 md2>
                                                    <v-btn
                                                    type="submit"
                                                    dark
                                                    color="cyan">Generate</v-btn>
                                                </v-flex>
                                            </v-layout>
                                        </v-form>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                            <v-flex xs12 style="padding:5px; background-color:#d8e6eb">
                                <span style="font-weight:bold">{{ info }}</span>
                            </v-flex>
                            <v-flex xs12 style="min-height:500px; position:relative">
                                <div class="my-loader" v-if="isLoading"></div>
                                <v-layout row wrap>
                                    <v-flex xs12 md6 sm12 style="padding:20px">
                                        <v-card style="margin-bottom:20px; background-color:#039be5" dark>
                                            <v-card-title>
                                                <v-icon color="#fff" dark>shopping_cart</v-icon>
                                                <span style="font-weight:bold">SALES</span>
                                            </v-card-title>
                                            <v-card-text style="background-color:#039be5">
                                                <v-list style="background-color:#039be5">
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Total Sale Amount:</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(total_sale.total_cost || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Total Sale Amount Paid:</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(total_sale.total_paid || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Sale Profit:</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(sale_profit || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Amount Owed:</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(total_sale.amount_owed || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                </v-list>
                                            </v-card-text>    
                                        </v-card>
                                    </v-flex>
                                    <v-flex xs12 sm12 md6 style="padding:20px">
                                        <v-card style="margin-bottom:20px; background-color:#ff6f00;" dark>
                                            <v-card-title>
                                                <v-icon color="#fff" dark>shopping_cart</v-icon>
                                                <span style="font-weight:bold">PURCHASES</span>
                                            </v-card-title>
                                            <v-card-text style="background-color:#ff6f00">
                                                <v-list style="background-color:#ff6f00">
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Total Purchase (GHC):</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(total_purchase.total_cost || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Total Purchase Amount Paid:</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(total_purchase.total_paid || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                    <v-list-tile>
                                                        <v-list-tile-action>
                                                            <span style="font-weight:bold; margin-right:5px">Amount Owed:</span>
                                                        </v-list-tile-action>
                                                        <v-list-tile-content>
                                                            GHC {{ getMoney(total_purchase.amount_owed || 0) }}
                                                        </v-list-tile-content>
                                                    </v-list-tile>
                                                </v-list>
                                            </v-card-text>    
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetFinacialReport } from '../../utils/report'
    export default {
        created() {
            this.generateReport();
        },
        data() {
            return {
                search_data: {},
                fromDate: new Date().toISOString().substring(0, 10),
                fromDateMenu: false,

                toDate: new Date().toISOString().substring(0, 10),
                toDateMenu: false,
                sale: {},
                total_sale: {},
                sale_profit: '',
                info: '',

                total_purchase: {},
                isLoading: false,
                total_value: 0,
            }
        },
        methods: {
            generateReport: function(){
                let data = {from_date:this.fromDate, to_date: this.toDate}
                this.isLoading = true;
                GetFinacialReport(data)
                    .then(result=> {
                        this.isLoading = false;
                        this.total_sale = result.total_sale
                        this.sale_profit = result.sale_profit
                        this.info = result.info || '';
                        this.total_value = (result || {}).total_value;
                        this.total_purchase = result.total_purchase;
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            getMoney: function(amt){
                let a = ((amt || 0)/100).toFixed(2)
                return a;
            },
        },
    }
</script>