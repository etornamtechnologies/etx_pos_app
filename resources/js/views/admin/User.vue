<template>
    <v-layout row wrap>
        <v-flex xs12 sm12 md4>
            <v-card height="600px">
                <v-card-title>
                    <v-btn
                    color="pink"
                    dark
                    @click="goBack"
                    >
                        <v-icon>arrow_back</v-icon>
                        Users
                        </v-btn>
                </v-card-title>
                <v-card-text>
                    <v-list two-line>
                        <v-list-tile>
                            <v-list-tile-action>
                                <v-icon color="#f25">menu</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>NAME</v-list-tile-title>
                                <v-list-tile-sub-title>{{ user.name }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>

                        <v-list-tile>
                            <v-list-tile-action>
                                <v-icon color="#f25">menu</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>USERNAME</v-list-tile-title>
                                <v-list-tile-sub-title>{{ user.username }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>

                        <v-list-tile>
                            <v-list-tile-action>
                                <v-icon color="#f25">message</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>EMAIL</v-list-tile-title>
                                <v-list-tile-sub-title>{{ user.email }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>

                        <v-list-tile>
                            <v-list-tile-action>
                                <v-icon color="#f25">phone</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title>PHONE</v-list-tile-title>
                                <v-list-tile-sub-title>{{ user.phone }}</v-list-tile-sub-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                </v-card-text>
            </v-card>
        </v-flex>
        <v-flex xs12 sm12 md8 px-2>
            <v-card>
                <v-card-title>user roles</v-card-title>
                <v-card-text>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sales Rep</th>
                                <th>Manager</th>
                                <th>Admin</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox" class=""
                                        v-model="sales_rep">
                                </td>
                                <td><input type="checkbox" class=""
                                        v-model="manager">
                                </td>
                                <td><input type="checkbox" class=""
                                        v-model="admin">
                                </td>
                                <td>
                                    <button class="btn btn-primary" @click="assignRole()">
                                        assign
                                    </button>
                                </td>      

                            </tr>
                        </tbody>
                    </table>
                </v-card-text>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    import { ShowUser, AssignRoleToUser } from '../../utils/admin'
    export default {
        beforeRouteEnter: function(to, from, next) {
            next();
        },
        mounted: function() {
            this.fetchUser()
        },
        beforeRouteUpdate(to, from, next) {
            this.fetchUser();
            next();
        },
        watch: {
            userId: function() {
                this.fetchUser();
            }
        },
        data(){
            return {
                user: {},
                userId: null,
                admin:false,
                sales_rep: false,
                manager: false,  
            }
        },
        methods: {
            fetchUser: function() {
                let id = this.$route.params.id;
                ShowUser({ id })
                    .then(result=> {
                        console.log(result)
                        this.user = result.user;
                        this.initRoles();
                    })
            },
            assignRole: function() {
                this.isLoading = true;
                let data = {};
                data.id = this.user.id
                if(this.sales_rep) {
                    data.sales_rep = this.sales_rep;
                }
                if(this.manager) {
                    data.manager = this.manager;
                }
                if(this.admin) {
                    data.admin = this.admin;
                }
                AssignRoleToUser(data)
                    .then(result=> {
                        this.isLoading = false;
                        this.user = result.user;
                        this.initRoles()
                    })
                    .catch(err=> {
                        this.isLoading = false;
                    })
            },
            initRoles: function() {
                let roles = this.user.roles || [];
                roles.forEach((role)=> {
                    if(role.label == 'admin') {
                        this.admin = true;
                    }
                    if(role.label == 'manager') {
                        this.manager = true;
                    }
                    if(role.label == 'sales-rep') {
                        this.sales_rep = true;
                    }
                })
            },
            goBack: function() {
                this.$router.go(-1);
            }
        }
    }
</script>