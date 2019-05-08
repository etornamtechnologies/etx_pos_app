<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar flat color="cyan" dark dense>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>customers</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-menu bottom left>
                            <template v-slot:activator="{ on }">
                                <v-btn
                                    dark
                                    icon
                                    v-on="on"
                                >
                                    <v-icon>more_vert</v-icon>
                                </v-btn>
                            </template>

                            <v-list>
                                <v-list-tile
                                    key="create_customer"
                                    @click="openCreateCustomerDialog"
                                >
                                    <v-list-tile-title>+ create new customer</v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </v-toolbar>
                    <v-card-title>
                        Customers
                        <v-spacer></v-spacer>
                        <v-text-field
                        color="cyan"
                        v-model="filter"
                        append-icon="search"
                        label="Search"
                        single-line
                        hide-details
                        ></v-text-field>
                    </v-card-title>
                    <v-card-text style="position:relative; min-height:500px">
                        <div class="my-loader" v-if="getLoading"></div>
                        <v-layout column>
                            <v-flex xs12>
                                <v-data-table
                                :headers="customer_headers"
                                :items="customers"
                                :search="filter"
                                :rows-per-page-items="myRows"
                                >
                                    <template v-slot:items="props">
                                        <td>{{ props.item.name || '---' }}</td>
                                        <td class="text-xs-left">{{ props.item.phone || '---' }}</td>
                                        <td class="text-xs-left">{{ props.item.email || '---' }}</td>
                                        <td class="text-xs-left">{{ props.item.address || '---' }}</td>
                                        <td class="text-xs-left">{{ prettyDate(props.item.created_at || '') }}</td>
                                        <td class="text-xs-right">
                                            <v-btn icon color="primary" small dark
                                            @click="openEditCustomerDialog(props.item, props.index)">
                                                <v-icon small>edit</v-icon>
                                            </v-btn>
                                            <v-btn color="red" small dark icon
                                            @click="deleteCustomer(props.item)">
                                                <v-icon small>delete</v-icon>
                                            </v-btn>
                                        </td>
                                    </template>
                                    <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                                        Your search for "{{ search }}" found no results.
                                    </v-alert>
                                </v-data-table>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                    <v-btn
                            color="pink"
                            dark
                            small
                            absolute
                            bottom
                            right
                            fab
                            @click="openCreateCustomerDialog"
                    >
                        <v-icon>person_add</v-icon>
                    </v-btn>
                </v-card>
            </v-flex>
        </v-layout>
        <v-dialog v-model="isOpenCreateCustomerDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="createLoading"></div>
                <v-form @submit.prevent="createCustomer">
                    <v-card-title class="headline">create customer</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="name *"
                        required
                        :rules="[v => !!v || 'label is required']"
                        v-model="new_customer.name"></v-text-field>

                        <v-text-field
                        label="phone *"
                        required
                        v-model="new_customer.phone"></v-text-field>

                        <v-text-field
                        label="email"
                        required
                        v-model="new_customer.email"></v-text-field>

                        <v-text-field
                        label="address"
                        required
                        v-model="new_customer.address"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isOpenCreateCustomerDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isOpenEditCustomerDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="updateLoading"></div>
                <v-form @submit.prevent="updateCustomer">
                    <v-card-title class="headline">edit customer</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="name *"
                        required
                        :rules="[v => !!v || 'label is required']"
                        v-model="edit_customer.name"></v-text-field>

                        <v-text-field
                        label="phone *"
                        required
                        v-model="edit_customer.phone"></v-text-field>

                        <v-text-field
                        label="email"
                        required
                        v-model="edit_customer.email"></v-text-field>

                        <v-text-field
                        label="address"
                        required
                        v-model="edit_customer.address"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isOpenEditCustomerDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </v-container>
</template>
<script>
    import { GetCustomer, CreateCustomer, UpdateCustomer, DeleteCustomer } from '../../utils/inventory'
    import { formatDate } from '../../utils/helpers'
    export default {
        created() {
            this.fetchCustomers();
        },
        data() {
            return {
                isOpenEditCustomerDialog: false,
                isOpenCreateCustomerDialog: false,
                new_customer: {name:'', email:'', phone:'', address:''},
                edit_customer: {},
                createLoading: false,
                updateLoading: false,
                deleteLoading: false,
                getLoading: false,
                customers: [],
                filter: '',
                customer_headers: [{text:'name', value:'name', sortable:true}, {text: 'phone', value: 'phone'}
                                    , {text: 'email', value: 'email'}, {text: 'address', value: 'address'}
                                    , {text:'registered on', value: 'created_at'}, {text: '', value: 'buttons'}],
                myRows: [10,20,30],                    
            }
        },
        methods: {
            fetchCustomers: function() {
                this.getLoading = true;
                GetCustomer({})
                    .then(result=> {
                        this.getLoading = false;
                        this.customers = result.customers || [];
                    })
                    .catch(err=> {
                        this.getLoading = false;
                    })
            },
            openCreateCustomerDialog: function() {
                this.new_customer = {name:'', email:'', phone:'', address:''};
                this.isOpenCreateCustomerDialog = true;
            },
            openEditCustomerDialog: function(entry) {
                this.edit_customer = Vue.util.extend({}, entry);
                this.isOpenEditCustomerDialog = true
            },
            createCustomer: function () {
                this.createLoading = true;
                CreateCustomer(this.new_customer)
                    .then(result=> {
                        this.createLoading = false;
                        this.customers.push(result.customer || {});
                        this.isOpenCreateCustomerDialog = false;
                    })
                    .catch(err=> {
                        this.createLoading = false;
                    })
            },
            updateCustomer: function() {
                this.updateLoading = true;
                UpdateCustomer(this.edit_customer)
                    .then(result=> {
                        this.updateLoading = false;
                        this.customers = result.customers || [];
                        this.isOpenEditCustomerDialog = false;
                    })
                    .catch(err=> {
                        this.updateLoading = false;
                    })
            },
            deleteCustomer: function(entry) {
                this.deleteLoading = true;
                let index = this.customers.indexOf(entry);
                if(confirm('Are you sure you want to delete customer?')) {
                    DeleteCustomer(entry)
                    .then(result=> {
                        this.deleteLoading = false;
                        if(index > -1) {
                            this.customers.splice(index, 1);
                        }
                    })
                    .catch(err=> {
                        this.deleteLoading = false;
                    })
                }
            },
            prettyDate: function(date_str) {
                return formatDate(date_str) || '--';
            }
        },
        computed: {
            
        }
    }
</script>