<template>
    <v-layout>
        <v-flex xs12>
            <v-card>
                <v-toolbar
                dense
                dark
                color="cyan">
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>users</v-toolbar-title>
                </v-toolbar>
                <v-card-title>
                    Users
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
                <v-card-text style="min-height:400px; position:relative">
                    <div class="my-loader" v-if="isLoading"></div>
                    <v-data-table
                    :items="users"
                    :headers="headers"
                    :search="filter"
                    :rows-per-page-items="myRows"
                    >
                        <template v-slot:items="props">
                            <td>{{ props.item.name }}</td>
                            <td class="text-xs-left">{{ props.item.username }}</td>
                            <td class="text-xs-left">{{ props.item.email }}</td>
                            <td class="text-xs-left">{{ props.item.phone }}</td>
                            <td class="text-xs-left">{{ props.item.created_at }}</td>
                            <td class="text-xs-right">
                                <v-btn small dark color="cyan" icon
                                @click="showUser(props.item)">
                                    <v-icon small>info</v-icon>
                                </v-btn>
                                <v-btn color="error" small dark icon
                                @click="deleteUser(props.item)">
                                    <v-icon small>delete</v-icon>
                                </v-btn>
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
                    @click="createUserDialog"
                    >
                        <v-icon>add</v-icon>
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-flex>

            <v-dialog v-model="showCreateDialog" persistent width="500">
                <v-card style="position:relative">
                    <div class="my-loader" v-if="isUpdating"></div>
                    <form @submit.prevent="createUser">
                    <v-card-title class="headline">create user</v-card-title>
                    <v-card-text>
                        <v-text-field
                        label="name"
                        required
                        v-model="new_user.name"></v-text-field>
                        <v-text-field
                        label="phone"
                        required
                        v-model="new_user.phone"></v-text-field>
                        <v-text-field
                        label="username"
                        required
                        v-model="new_user.username"></v-text-field>
                        <v-text-field
                        label="password"
                        required
                        v-model="new_user.password"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" flat ripple @click="showCreateDialog = false">CANCEL</v-btn>
                        <v-btn color="green darken-1" flat type="submit">REGISTER</v-btn>
                    </v-card-actions>
                    </form>
                </v-card>
            </v-dialog>
    </v-layout>
</template>
<script>
    import { GetUser, DeleteUser } from '../../utils/admin'
    export default {
        mounted() {
            this.fetchUsers();
        },
        data(){
            return {
                users: [],
                headers: [{text:'Name', value:'name'}, {text:'Username', value:'username'}, {text:'email', value:'eamil'}
                            ,{text:'Phone', value:'phone'}, {text:'Created on', value:'created_at'}, {text:'', value:'buttons'}],
                isLoading: false,
                filter: '',
                showCreateDialog: false,
                new_user: {name: '', username: '', password: '', phone: ''},
                edit_user: {},
                showEditDialog: false,
                isUpdating: false,
                isCreating: false,
                myRows: [10,20,30],
            }
        },
        methods: {
            fetchUsers: function() {
                this.isLoading = true;
                GetUser({})
                    .then(result=> {
                        this.users = result.users || []
                        this.isLoading = false;
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            deleteUser: function(row) {
                let index = this.users.indexOf(row);
                if(confirm('Are you sure you want to delete user?')) {
                    this.isLoading = true;
                    DeleteUser(row)
                        .then(result=> {
                            this.isLoading = false;
                            this.users.splice(index, 1);
                        })
                        .catch(err=> {
                            this.isLoading = false;
                        })
                }
            },
            showUser: function(row) {
                this.$router.push({ name: 'user_detail', params: {id: row.id}})
            },
            createUserDialog: function() {
                this.new_user = {name: '', username: '', password: '', phone: ''}
                this.showCreateDialog = true;
            },
            editUserDialog: function(row) {
                this.edit_user = Vue.util.extend({}, row);
                this.showEditDialog = true;
            }
        }
    }
</script>