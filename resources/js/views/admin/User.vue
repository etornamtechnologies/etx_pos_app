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
                <v-card-text style="position:relative">
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
                    <v-btn color="warning" 
                    @click="openResetDataDialog"
                    style="bottom:0">RESET PASSWORD</v-btn>
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
                                <th>Supervisor</th>
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
                                        v-model="supervisor">
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
        <v-dialog v-model="isOpenResetDialog" persistent width="500">
            <v-card style="position:relative">
                <div class="my-loader" v-if="isUpdating"></div>
                <form @submit.prevent="resetPassword">
                <v-card-title class="headline">Reset User Password</v-card-title>
                <v-card-text>
                    <v-text-field
                    label="New-Password"
                    type="password"
                    required
                    v-model="reset_data.password"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="red darken-1" flat ripple @click="isOpenResetDialog = false">CANCEL</v-btn>
                    <v-btn color="green darken-1" flat type="submit">RESET</v-btn>
                </v-card-actions>
                </form>
            </v-card>
        </v-dialog>
    </v-layout>
</template>
<script>
    import { ShowUser, AssignRoleToUser, ResetUserPassword } from '../../utils/admin'
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
                supervisor: false,
                isOpenResetDialog: false,
                isUpdating: false,
                reset_data: {password: ''},
            }
        },
        methods: {
            fetchUser: function() {
                let id = this.$route.params.id;
                ShowUser({ id })
                    .then(result=> {
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
                if(this.supervisor) {
                    data.supervisor = this.supervisor;
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
                    if(role.label == 'supervisor') {
                        this.supervisor = true;
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
            },
            openResetDataDialog: function() {
                this.reset_data.password = '';
                this.isOpenResetDialog = true;
            },
            resetPassword: function() {
                console.log(this.reset_data);
                let data = Vue.util.extend({}, this.reset_data);
                data.id = (this.user || {}).id || null;
                ResetUserPassword(data)
                    .then(result=> {
                        this.isOpenResetDialog = false;
                    })
                    .catch(err=> {
                        
                    })
            }
        }
    }
</script>