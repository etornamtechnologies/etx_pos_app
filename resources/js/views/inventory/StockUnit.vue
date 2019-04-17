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
                <v-data-table
                :headers="headers"
                :items="stock_units"
                :search="search"
                >
                <template v-slot:items="props">
                    <td>{{ props.item.label }}</td>
                    <td class="text-xs-left">{{ props.item.created_at }}</td>
                    <td class="text-xs-left">
                        <v-icon
                        @click="openEditStockUnitDialog(props.item, props.index)">edit</v-icon>
                        <v-icon color="red"
                        @click="deleteStockUnit(props.item)">delete</v-icon>
                    </td>
                </template>
                <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                    Your search for "{{ search }}" found no results.
                </v-alert>
                </v-data-table>
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
            <v-card>
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
            <v-card>
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
    export default {
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
            }
        },
        methods: {
            fetchStockUnit: function(){
                GetStockUnit({})
                    .then(result=> {
                        console.log('cats',result)
                        this.stock_units = result.stock_units || []
                    })
            },
            createStockUnit: function(){
                let data = Vue.util.extend({}, this.new_stock_unit);
                data.label = this.new_stock_unit.label.toUpperCase();
                CreateStockUnit(data)
                    .then(result=> {
                        this.stock_units.push(result.stock_unit || {});
                        this.isOpenCreateStockUnitDialog = false;
                        this.new_stock_unit = {};
                    })
                    .catch(err=> {
                        this.new_stock_unit = {label:''};
                    })
            },
            openEditStockUnitDialog: function(row, index){
                this.edit_stock_unit = Vue.util.extend({},row);
                this.edit_stock_unit.index = index;
                this.isOpenEditStockUnitDialog = true;
            },
            updateStockUnit: function(){
                UpdateStockUnit(this.edit_stock_unit)
                    .then(result=> {
                        this.stock_units = result.stock_units || [];
                        this.isOpenEditStockUnitDialog = false;
                        this.$forceUpdate();
                    })
                    .catch(err=> {
                        this.new_stock_unit = {}
                    })
            },
            deleteStockUnit: function(row) {
                if(confirm('Are you sure you want to delete? ')) {
                    DeleteStockUnit(row)
                        .then(result=> {
                            let index = this.stock_units.indexOf(row);
                            console.log(index);
                            this.stock_units.splice(index, 1);
                        })
                        .catch(err=> {
                            
                        })
                }
            }
        }
    }
</script>