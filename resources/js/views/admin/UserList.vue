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
                <v-data-table
                :items="users"
                :headers="headers"
                :search="filter"
                >
                    <template v-slot:items="props">
                        <td>{{ props.item.name }}</td>
                        <td class="text-xs-left">{{ props.item.username }}</td>
                        <td class="text-xs-left">{{ props.item.email }}</td>
                        <td class="text-xs-left">{{ props.item.phone }}</td>
                        <td class="text-xs-left">{{ props.item.created_at }}</td>
                        <td class="text-xs-left">
                            <v-icon
                            @click="showUser(props.item)">info</v-icon>
                            <v-icon color="red"
                            @click="deleteUser(props.item)">delete</v-icon>
                        </td>
                    </template>
                    <v-alert v-slot:no-results :value="true" color="error" icon="warning">
                        Your search for "{{ search }}" found no results.
                    </v-alert>
                </v-data-table>
            </v-card>
        </v-flex>
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
            }
        },
        methods: {
            fetchUsers: function() {
                this.isLoading = true;
                GetUser({})
                    .then(result=> {
                        console.log(result)
                        this.users = result.users || []
                        this.isLoading = false;
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            deleteUser: function(row) {
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
            }
        }
    }
</script>