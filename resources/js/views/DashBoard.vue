<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense color="orange" dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>DASHBOARD</v-toolbar-title>
                    </v-toolbar>  
                    <v-card-text>
                        <v-layout mt-5 row wrap pl-3 pr-3 justify-space-around>
                            <v-flex xs12 md3 py-2>
                                <v-card height="120px" color="cyan" dark>
                                    <v-card-title style="align-text:center; padding:0">
                                        <span class="my-card-label">TODAY SALE COUNT</span>
                                    </v-card-title>
                                    <v-card-text>
                                        <div class="my-card-content">
                                            {{ todaySaleCount }}
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                            <v-flex xs12 md3 pt-2>
                                <v-card height="120px" color="orange" dark>
                                    <v-card-title style="align-text:center; padding:0">
                                        <span class="my-card-label">today sale amount</span>
                                    </v-card-title>
                                    <v-card-text>
                                        <div class="my-card-content">
                                            {{ todaySaleAmount }}
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                            <v-flex xs12 md3 pt-2>
                                <v-card height="120px" color="success" dark>
                                    <v-card-title style="align-text:center; padding:0">
                                        <span class="my-card-label">products</span>
                                    </v-card-title>
                                    <v-card-text>
                                        <div class="my-card-content">
                                            {{ productCount }}
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetDashboard } from '../utils/dashboard'
    export default {
        name: 'dashborad',
        created() {
            this.fetchDashboard();
        },
        data() {
            return {
                totalSale: "5",
                dashboard: {}
            }
        },
        methods: {
            fetchDashboard: function() {
                GetDashboard({})
                    .then(result=> {
                        console.log(result);
                        this.dashboard = result.dashboard || {}
                    })      
                    .catch(err=> {

                    })
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
            }
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
