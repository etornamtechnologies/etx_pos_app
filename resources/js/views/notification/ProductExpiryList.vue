<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar color="cyan" dense dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Product Expiry List</v-toolbar-title>
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
                        :items="batches"
                        :headers="headers"
                        :rows-per-page-items="myRows">
                            <template v-slot:items="props">
                                <td class="text-xs-left">{{ props.item.product }}</td>
                                <td class="text-xs-left">{{ props.item.batch_no }}</td>
                                <td class="text-xs-left">{{ props.item.expiry_date }}</td>
                                <td class="text-xs-left">{{ props.item.quantity }}</td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetProductExpiryAlertList } from '../../utils/notification'
    export default {
        created() {
            this.fetchProductExpiryAlerts();
        },
        data() {
            return {
                search: '',
                batches: [],
                headers: [{text:'Product', value:'product'}, {text:'Batch number', value:'batch'}
                            ,{text: 'Expiry Date', value:'expiry_date'}, {text:'Quantity', value:'quantity'}],
                isLoading: false,
                myRows: [10,20,30]
            }
        },
        methods: {
            fetchProductExpiryAlerts: function() {
                this.isLoading = true;
                GetProductExpiryAlertList({})
                    .then(result=> {
                        this.isLoading = false;
                        this.batches = result.batches || []
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