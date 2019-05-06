<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-layout column>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs12 md6 sm12 style="padding:10px">
                                <div 
                                style="display:flex; flex-direction:row; text-align:center; font-size:20px">
                                    DASHBOARD <span style="margin-left:5px">
                                    <v-icon
                                    v-if="!isLoading"
                                    color="cyan"
                                    @click="fetchDashboard">refresh</v-icon></span>
                                    <img 
                                    v-if="isLoading"
                                    src="img/rolling.svg" alt="" width="23px" height="23px">
                                </div>
                            </v-flex>
                            <v-flex xs12 sm12 md6  
                            style="display:flex; flex-direction:row; justify-content:flex-end; flex-wrap:wrap">
                                <v-badge overlap color="red" style="margin-right:5px">
                                    <span slot="badge" v-if="dashboard.restock_count">
                                        {{ dashboard.restock_count || '' }}
                                    </span>
                                    <v-btn
                                    @click="goToProductStockAlertListPage"
                                    dark
                                    small
                                    color="success">
                                        stock alert
                                    </v-btn>
                                </v-badge>
                                <v-badge overlap color="cyan">
                                    <span slot="badge" v-if="dashboard.expiry_count">
                                        {{ dashboard.expiry_count || '' }}
                                    </span>
                                    <v-btn
                                    @click="goToProductExpiryAlertListPage"
                                    dark
                                    small
                                    color="warning">
                                        expiry alert
                                    </v-btn>
                                </v-badge>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md6 style="padding:10px">
                                <v-card>
                                    <v-card-title>
                                        <v-icon color="black" dark>shopping_cart</v-icon> <span style="font-weight:bold">TRANSACTIONS</span>
                                    </v-card-title>
                                    <v-card-text>
                                        <v-list>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Total Sale:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    GHC {{ getMoney(dashboard.total_sale_amount) }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">This Month Total Sale:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    GHC {{ getMoney(dashboard.this_month_total_sale) }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Yesterday Total Sale:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    GHC {{ getMoney(dashboard.yesterday_total_sale_amount) }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Today Total Sale:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    GHC {{ getMoney(dashboard.today_sale_amount) }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">This Month Total Purchase:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    GHC {{ getMoney(dashboard.this_month_purchase_total_amount) }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                        </v-list>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn 
                                        @click="goToSalesPage"
                                        dark
                                        color="cyan" small>Sales</v-btn>
                                        <v-btn 
                                        @click="goToPurchasesPage"
                                        color="light" small>Purchases</v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-flex>
                            <v-flex xs12 sm12 md6 style="padding:10px">
                                <v-card>
                                    <v-card-title>
                                        <v-icon color="black" dark>alarm</v-icon> <span style="font-weight:bold">ALERTS</span>
                                    </v-card-title>
                                    <v-card-text>
                                        <v-list>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Number Of Products:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    # {{ dashboard.product_count }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Number Of Users:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    # {{ dashboard.users_count }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Number Of Vendors:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    # {{ dashboard.suppliers_count }}
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Restock Alerts:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    <!-- TODO  expiry alerts from backend-->
                                                    # {{ dashboard.restock_count || 0 }} 
                                                </v-list-tile-content>
                                            </v-list-tile>
                                            <v-list-tile>
                                                <v-list-tile-action>
                                                    <span style="font-weight:bold; margin-right:5px">Expiry Alerts:</span>
                                                </v-list-tile-action>
                                                <v-list-tile-content>
                                                    <!-- TODO  expiry alerts from backend-->
                                                    # {{ dashboard.expiry_count || 0 }} 
                                                </v-list-tile-content>
                                            </v-list-tile>
                                        </v-list>
                                    </v-card-text>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn 
                                        color="primary"
                                        @click="goToProductListPage"
                                        small>Products</v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                    <v-flex xs12>
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12 style="padding:10px">
                                <v-card>
                                    <v-card-text>
                                        <div id="chart-container">
                                            <fusioncharts
                                            :type="type"
                                            :width="width"
                                            :height="height"
                                            :dataFormat="dataFormat"
                                            :dataSource="topCategorySaleChartData"
                                            ></fusioncharts>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetDashboard } from '../utils/dashboard'
    import { hasAnyRole } from '../utils/helpers'
    import FusionCharts from "fusioncharts";
    import Charts from "fusioncharts/fusioncharts.charts";
    import { FCComponent } from "vue-fusioncharts";

    // Resolves charts dependancy
    Charts(FusionCharts);
    export default {
        beforeRouteEnter (to, from, next) {
            hasAnyRole(['admin','manager','sales-reps'], (res)=> {
                if(res) {
                    next()
                } else {
                    next(from)
                }
            })
        },
        name: 'dashborad',
        created() {
            this.fetchDashboard();
        },
        data() {
            return {
                totalSale: "5",
                dashboard: {},
                type: "doughnut2d",
                width: "600",
                height: "500",
                dataFormat: "json",
                isLoading: false,
            }
        },
        methods: {
            fetchDashboard: function() {
                this.isLoading = true;
                GetDashboard({})
                    .then(result=> {
                        console.log(result);
                        this.isLoading = false;
                        this.dashboard = result.dashboard || {}
                    })      
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            getMoney: function(amt){
                let a = ((amt || 0)/100).toFixed(2)
                return a;
            },
            goToSalesPage: function(){
                this.$router.push({ name: 'sale_list' })
            },
            goToPurchasesPage: function(){
                this.$router.push({ name: 'purchase_list' })
            },
            goToProductListPage: function(){
                this.$router.push({ name: 'product_list' })
            },
            goToProductStockAlertListPage: function(){
                this.$router.push({ name: 'product-restock-list' })
            },
            goToProductExpiryAlertListPage: function() {
                this.$router.push({ name: 'product-expiry-list' });
            }
        },
        computed: {
            todaySaleCount: function(){
                return (this.dashboard || {}).today_sale_count
            },
            todaySaleAmount: function() {
                let amount = (this.dashboard || {}).today_sale_amount || 0
                return `GHC ${(amount/100).toFixed(2)}`
            },
            productCount: function(){
                return (this.dashboard || {}).product_count || 0
            },
            topCategorySaleChartData: function() {
                let categories = (this.dashboard || {}).top_category_sale || [];
                let catFilter = categories.filter(cat=> {
                    return cat.quantity > 0;
                }) || [];
                let chartInfo = {
                    caption: "Top Sale Categories",
                    subcaption: "For all time",
                    showpercentvalues: "1",
                    defaultcenterlabel: "Top 10",
                    aligncaptionwithcanvas: "0",
                    captionpadding: "0",
                    decimals: "1",
                    plottooltext:
                    "<b>$percentValue</b> of top 10 category sale <b>$label</b>",
                    centerlabel: "#$label: $value",
                    theme: "zune"
                };
                let dataValues = catFilter.map(category=> {
                    let d = {};
                    d.label = category.category || "";
                    d.value = category.quantity || 0;
                    return d;
                }) || [];
                let data = {};
                data.chart = chartInfo;
                data.data = dataValues;
                console.log('myData',data);
                return data;
            },
        },
    }
</script>

<style scoped>
    .my-card-label {
        width: 100%;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        border-bottom: solid #fff 1px;
        text-transform: uppercase;
    }
    .my-card-content {
        padding-top: 10px;
        width: 100%;
        height: 100%;
        font-weight: bold;
        font-size: 20px;
        text-align: center
    }
</style>
