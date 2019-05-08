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
                                <td class="text-xs-left">{{ props.item.date }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.total_cost || 0) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_paid) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_owed || 0) }}</td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetSaleWithDebt } from '../../utils/ledger'
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
                            , {text:'Amount Owed (GHC)', value:'amount_owed'}
                            ],
                isLoading: false,
                myRows: [10,20,30]
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
            }
        },
    }
</script>