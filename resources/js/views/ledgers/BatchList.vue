<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar color="cyan" dense dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>All Batches With Expiry Dates</v-toolbar-title>
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
                                <td class="text-xs-left">{{ props.item.product }}</td>
                                <td class="text-xs-left">{{ props.item.batch }}</td>
                                <td class="text-xs-left">{{ props.item.expiry_date }}</td>
                                <td class="text-xs-left">
                                    {{ props.item.quantity }} {{ props.item.stock_unit }} (s)
                                </td>
                                <td>
                                    <v-btn icon small dark color="cyan"
                                    @click="openEditDialog(props.item)">
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <v-btn icon small dark color="error"
                                    @click="deleteEntry(props.item)">
                                        <v-icon small>delete</v-icon>
                                    </v-btn>
                                </td>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <v-dialog v-model="isEditDialogOpen" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <form @submit.prevent="updateEntry">
                <v-card-title class="headline">edit batch entry</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="quantity"
                    required
                    v-model="edit_batch.quantity"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isEditDialogOpen = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">UPDATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
    </v-container>
</template>
<script>
    import { GetAllBatches } from '../../utils/ledger'
    import { UpdateBatch, DeleteBatch } from '../../utils/batch'
    export default {
        created() {
            this.fetchBatches();
        },
        data() {
            return {
                search: '',
                ledgers: [],
                headers: [{text:'Product', value:'product'}, {text:'batch #', value:'label'}
                             ,{text: 'Expiry Date', value:'expiry_date'}, {text:'Remaining quantity', value:'quantity'}
                             ,{text:'', value:'actions'}],
                isLoading: false,
                isEditDialogOpen: false,
                isUpdating: false,
                edit_batch: {quantity:''},
                myRows: [10,20,30],
            }
        },
        methods: {
            fetchBatches: function() {
                this.isLoading = true;
                GetAllBatches({})
                    .then(result=> {
                        this.isLoading = false;
                        this.ledgers = result.batches || []
                    })
                    .catch(err=> {
                        this.isLoading = false
                    })
            },
            getMoney: function(amount) {
                return (amount/100).toFixed(2)
            },
            openEditDialog: function(row) {
                this.edit_batch = Vue.util.extend({}, row)
                this.isEditDialogOpen = true;
            },
            updateEntry: function() {
                this.isUpdating = true;
                UpdateBatch(this.edit_batch)
                    .then(result=> {
                        this.isUpdating = false;
                        location.reload(true)
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                    })
            },
            deleteEntry: function(row) {
                this.isUpdating = true;
                let index = this.ledgers.indexOf(row);
                DeleteBatch(row)
                    .then(result=> {
                        this.isUpdating = false;
                        this.ledgers.splice(index, 1);
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                    })
            },
        },
    }
</script>