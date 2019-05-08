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
                                <td class="text-xs-left">{{ props.item.date }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.total_cost || 0) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_paid) }}</td>
                                <td class="text-xs-left">{{ getMoney(props.item.amount_owing || 0) }}</td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetPurchasesWithCredit } from '../../utils/ledger'
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
                            , {text:'Amount Owing (GHC)', value:'amount_owing'}
                            ],
                isLoading: false,
                myRows: [10,20,30],
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
            }
        },
    }
</script>