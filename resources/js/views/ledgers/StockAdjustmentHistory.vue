<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar color="cyan" dark dense>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Stock Adjustment History</v-toolbar-title>
                    </v-toolbar>
                    <v-card-title>
                        <v-spacer></v-spacer>
                        <v-text-field
                        color="cyan"
                        prepend-inner-icon="search"
                        v-model="search"></v-text-field>
                    </v-card-title>
                    <v-card-text style="min-height:400px; position:relative">
                        <div class="my-loader" v-if="isLoading"></div>
                        <v-data-table
                        :search="search"
                        :items="ledgers"
                        :headers="headers"
                        :rows-per-page-items="myRows">
                            <template v-slot:items="props">
                                <td class="text-xs-left">{{ props.item.product }}</td>
                                <td class="text-xs-left">{{ props.item.user }}</td>
                                <td class="text-xs-left">{{ props.item.created_at }}</td>
                                <td class="text-xs-left">{{ props.item.reason }}</td>
                                <td class="text-xs-left">{{ props.item.old_quantity || 0 }} {{ props.item.stock_unit }} (s)</td>
                                <td class="text-xs-left">{{ props.item.new_quantity }} {{ props.item.stock_unit }} (s)</td>
                                <td class="text-xs-left">{{ props.item.quantity_balance }} {{ props.item.stock_unit }} (s)</td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetStockAdjustment } from '../../utils/adjustment'
    import { formatDate } from '../../utils/helpers'
    export default {
        created() {
            this.fetchStockAdjustment()
        },
        data() {
            return {
                search: '',
                ledgers: [],
                headers: [{text:'Product', value:'product'}, {text:'User', value:'user'}, {text:'Date', value:'date'}
                            , {text: 'Reason', value: 'reason'}, {text:'Old-Quantity', value:'old_quantity'}
                            , {text:'New-Quantity', value:'new_quantity'}, {text:'Quantity Balance', value:'quantity_balance'}
                            ],
                isLoading: false,
                myRows: [10,20,30],
            }
        },
        methods: {
            fetchStockAdjustment: function(){
                this.isLoading = true
                GetStockAdjustment({})
                    .then(result=> {
                        this.isLoading = false
                        this.ledgers = (result || {}).stock_adjustments || [];
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            prettyDate: function(date_str){
                return formatDate(date_str);
            }
        },
        computed: {
            
        },
    }
</script>