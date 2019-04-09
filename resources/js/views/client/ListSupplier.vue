<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar flat color="cyan" dark dense>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>suppliers</v-toolbar-title>
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
                                    key="create_supplier"
                                    @click="openCreateSupplierDialog"
                                >
                                    <v-list-tile-title>+ create new supplier</v-list-tile-title>
                                </v-list-tile>
                            </v-list>
                        </v-menu>
                    </v-toolbar>
                    <v-card-title>
                        Suppliers
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
                    <v-data-table
                    :headers="supplier_headers"
                    :items="suppliers"
                    :search="filter"
                    >
                    <template v-slot:items="props">
                        <td>{{ props.item.name || '---' }}</td>
                        <td class="text-xs-left">{{ props.item.phone || '---' }}</td>
                        <td class="text-xs-left">{{ props.item.email || '---' }}</td>
                        <td class="text-xs-left">{{ props.item.address || '---' }}</td>
                        <td class="text-xs-left">{{ prettyDate(props.item.created_at || '') }}</td>
                        <td class="text-xs-left">
                            <v-icon
                            @click="openEditSupplierDialog(props.item, props.index)">edit</v-icon>
                            <v-icon color="red"
                            @click="deleteSupplier(props.item)">delete</v-icon>
                        </td>
                    </template>
                    <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                        Your search for "{{ search }}" found no results.
                    </v-alert>
                    </v-data-table>
                </v-card>
            </v-flex>
        </v-layout>
        <v-dialog v-model="isOpenCreateSupplierDialog" persistent width="500">
            <v-card>
                <v-form @submit.prevent="createSupplier">
                    <v-card-title class="headline">create customer</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="name *"
                        required
                        :rules="[v => !!v || 'label is required']"
                        v-model="new_supplier.name"></v-text-field>

                        <v-text-field
                        label="phone *"
                        required
                        v-model="new_supplier.phone"></v-text-field>

                        <v-text-field
                        label="email"
                        required
                        v-model="new_supplier.email"></v-text-field>

                        <v-text-field
                        label="address"
                        required
                        v-model="new_supplier.address"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isOpenCreateSupplierDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isOpenEditSupplierDialog" persistent width="500">
            <v-card>
                <v-form @submit.prevent="updateSupplier">
                    <v-card-title class="headline">edit supplier</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="name *"
                        required
                        :rules="[v => !!v || 'label is required']"
                        v-model="edit_supplier.name"></v-text-field>

                        <v-text-field
                        label="phone *"
                        required
                        v-model="edit_supplier.phone"></v-text-field>

                        <v-text-field
                        label="label"
                        required
                        v-model="edit_supplier.email"></v-text-field>

                        <v-text-field
                        label="label"
                        required
                        v-model="edit_supplier.address"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="isOpenEditSupplierDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">SUBMIT</v-btn>
                    </v-card-actions>
                </v-form>
            </v-card>
        </v-dialog>
    </v-container>
</template>
<script>
    import { GetSupplier, CreateSupplier, UpdateSupplier, DeleteSupplier } from '../../utils/inventory'
    import { formatDate } from '../../utils/helpers'
    export default {
        created() {
            this.fetchSuppliers();
        },
        data() {
            return {
                isOpenEditSupplierDialog: false,
                isOpenCreateSupplierDialog: false,
                new_supplier: {name:'', email:'', phone:'', address:''},
                edit_supplier: {},
                createLoading: false,
                updateLoading: false,
                deleteLoading: false,
                getLoading: false,
                suppliers: [],
                filter: '',
                supplier_headers: [{text:'name', value:'name', sortable:true}, {text: 'phone', value: 'phone'}
                                    , {text: 'email', value: 'email'}, {text: 'address', value: 'address'}
                                    , {text:'registered on', value: 'created_at'}, {text: 'Actions', value: 'buttons'}]
            }
        },
        methods: {
            fetchSuppliers: function() {
                this.getLoading = true;
                GetSupplier({})
                    .then(result=> {
                        this.getLoading = false;
                        this.suppliers = result.suppliers || [];
                    })
                    .catch(err=> {
                        this.getLoading = false;
                    })
            },
            openCreateSupplierDialog: function() {
                this.new_supplier = {name:'', email:'', phone:'', address:''};
                this.isOpenCreateSupplierDialog = true;
            },
            openEditSupplierDialog: function(entry) {
                this.edit_supplier = Vue.util.extend({}, entry);
                this.isOpenEditSupplierDialog = true
            },
            createSupplier: function () {
                this.createLoading = true;
                CreateSupplier(this.new_supplier)
                    .then(result=> {
                        this.createLoading = false;
                        this.suppliers.push(result.supplier || {});
                        this.isOpenCreateSupplierDialog = false;
                    })
                    .catch(err=> {
                        this.createLoading = false;
                    })
            },
            updateSupplier: function() {
                this.updateLoading = true;
                UpdateSupplier(this.edit_supplier)
                    .then(result=> {
                        this.updateLoading = false;
                        this.suppliers = result.suppliers || []
                        this.isOpenEditSupplierDialog = false;
                    })
                    .catch(err=> {
                        this.updateLoading = false;
                    })
            },
            deleteSupplier: function(entry) {
                this.deleteLoading = true;
                let index = this.suppliers.indexOf(entry);
                DeleteSupplier(entry)
                    .then(result=> {
                        this.deleteLoading = false;
                        if(index > -1) {
                            this.suppliers.splice(index, 1);
                        }
                    })
                    .catch(err=> {
                        this.deleteLoading = false;
                    })
            },
            prettyDate: function(date_str) {
                return formatDate(date_str) || ""
            }
        },
    }
</script>