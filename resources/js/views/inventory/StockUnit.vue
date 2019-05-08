<template>
   <v-layout row>
        <v-flex md12 xs12>
            <v-card>
                <v-toolbar color="cyan" dark dense flat>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>UNITS OF MEASUREMENT</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon color="cyan" dark>
                        <v-icon
                        @click="isOpenCreateStockUnitDialog = true">
                            add
                        </v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-title>
                    Units of Measurement
                    <v-spacer></v-spacer>
                    <v-text-field
                    color="cyan"
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                    ></v-text-field>
                </v-card-title>
                <v-card-text style="position:relative; min-height:400px">
                    <div class="my-loader" v-if="isLoading"></div>
                    <v-layout column>
                        <v-flex xs12>
                            <v-data-table
                            :headers="headers"
                            :items="stock_units"
                            :search="search"
                            :rows-per-page-items="myRows"
                            >
                            <template v-slot:items="props">
                                <td>{{ props.item.label }}</td>
                                <td class="text-xs-left">{{ props.item.created_at }}</td>
                                <td class="text-xs-right">
                                    <v-btn icon small dark color="primary"
                                    @click="openEditStockUnitDialog(props.item, props.index)">
                                        <v-icon small>edit</v-icon>
                                    </v-btn>
                                    <v-btn icon color="error" small dark
                                    @click="deleteStockUnit(props.item)">
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
                @click="isOpenCreateStockUnitDialog = true"
                >
                    <v-icon>add</v-icon>
                </v-btn>
            </v-card>
        </v-flex>

        <v-dialog v-model="isOpenCreateStockUnitDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <form @submit.prevent="createStockUnit">
                <v-card-title class="headline">create stock unit</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="label"
                    required
                    v-model="new_stock_unit.label"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isOpenCreateStockUnitDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">CREATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isOpenEditStockUnitDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <form @submit.prevent="updateStockUnit">
                <v-card-title class="headline">edit unit of measurement</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="label"
                    required
                    v-model="edit_stock_unit.label"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isOpenEditStockUnitDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">UPDATE</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
   </v-layout>
</template>

<script>
    import {GetStockUnit, CreateStockUnit, UpdateStockUnit, DeleteStockUnit} from '../../utils/stock-unit'
    import { hasAnyRole } from '../../utils/helpers'
    export default {
        beforeRouteEnter (to, from, next) {
            hasAnyRole(['supervisor','admin','manager'], (res)=> {
                if(res) {
                    next()
                } else {
                    next(from)
                }
            })
        },
        mounted() {
            this.fetchStockUnit();
        },
        data(){
            return {
                search:'',
                isOpenCreateStockUnitDialog: false,
                isOpenEditStockUnitDialog: false,
                stock_units: [],
                headers: [{text:'label', value:'label'}, {text:'created on', value:'created_at'},{text:'', value:'buttons'}],
                new_stock_unit: {label:''},
                edit_stock_unit: {id:null, label:''},
                isLoading: false,
                isUpdating: false,
                myRows: [10,20,30],
            }
        },
        methods: {
            fetchStockUnit: function(){
                this.isLoading = true;
                GetStockUnit({})
                    .then(result=> {
                        this.isLoading = false;
                        this.stock_units = result.stock_units || []
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            createStockUnit: function(){
                let data = Vue.util.extend({}, this.new_stock_unit);
                data.label = this.new_stock_unit.label.toUpperCase();
                this.isUpdating = true;
                CreateStockUnit(data)
                    .then(result=> {
                        this.isUpdating = false;
                        this.stock_units.push(result.stock_unit || {});
                        this.isOpenCreateStockUnitDialog = false;
                        this.new_stock_unit = {};
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                        this.new_stock_unit = {label:''};
                    })
            },
            openEditStockUnitDialog: function(row, index){
                this.edit_stock_unit = Vue.util.extend({},row);
                this.edit_stock_unit.index = index;
                this.isOpenEditStockUnitDialog = true;
            },
            updateStockUnit: function(){
                this.isUpdating = true;
                UpdateStockUnit(this.edit_stock_unit)
                    .then(result=> {
                        this.isUpdating = false;
                        this.stock_units = result.stock_units || [];
                        this.isOpenEditStockUnitDialog = false;
                        this.$forceUpdate();
                    })
                    .catch(err=> {
                        this.isUpdating = false;
                        this.new_stock_unit = {}
                    })
            },
            deleteStockUnit: function(row) {
                if(confirm('Are you sure you want to delete? ')) {
                    DeleteStockUnit(row)
                        .then(result=> {
                            let index = this.stock_units.indexOf(row);
                            this.stock_units.splice(index, 1);
                        })
                        .catch(err=> {
                            
                        })
                }
            }
        }
    }
</script>