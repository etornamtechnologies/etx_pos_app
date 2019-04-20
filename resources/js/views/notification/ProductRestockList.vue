<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar color="cyan" dense dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Product Restock List</v-toolbar-title>
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
                        :items="products"
                        :headers="headers">
                            <template v-slot:items="props">
                                <td class="text-xs-left">{{ props.item.product }}</td>
                                <td class="text-xs-left">{{ props.item.barcode }}</td>
                                <td class="text-xs-left">
                                    {{ props.item.quantity }} {{ props.item.stock_unit }}
                                </td>
                                <td class="text-xs-left">{{ props.item.reorder_quantity }}</td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { GetProductWithLowQuantity } from '../../utils/notification'
    export default {
        created() {
            this.fetchProductsWithLowQuantity();
        },
        data() {
            return {
                search: '',
                products: [],
                headers: [{text:'Product', value:'product'}, {text:'barcode', value:'barcode'}
                             ,{text: 'Quantity', value:'quantity'}, {text:'Restock Quantity', value:'reorder_quantity'}],
                isLoading: false,
            }
        },
        methods: {
            fetchProductsWithLowQuantity: function() {
                this.isLoading = true;
                GetProductWithLowQuantity({})
                    .then(result=> {
                        this.isLoading = false;
                        this.products = result.products || []
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