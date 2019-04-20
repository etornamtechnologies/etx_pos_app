<template>
    <v-container>
        <v-layout column>
            <v-flex xs12>
                <v-layout row wrap>
                    <v-flex xs12 sm12 md8 pa-3>
                        <v-card 
                        @click="goToInventoryPage"
                        raised :hover="allowForRoles(['admin','manager'])" style="background-color:red" class="card-flex">
                            Inventory Management
                        </v-card>
                    </v-flex>
                    <v-flex pa-3 xs12 sm12 md4>
                        <v-card 
                        @click="goToSalePage"
                        raised hover style="background-color:#039BE5" class="card-flex">
                            POS
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-flex>
            <v-flex xs12>
                <v-layout row wrap>
                    <v-flex xs12 sm12 md4 pa-3>
                        <v-card 
                        @click="goToReportPage"
                        raised :hover="allowForRoles(['admin','manager'])" style="background-color:#4CAF50" class="card-flex">
                            REPORTING
                        </v-card>
                    </v-flex>
                    <v-flex pa-3 xs12 sm12 md8>
                        <v-card 
                        @click="goToCustomersPage"
                        raised hover style="background-color:#FF6F00" class="card-flex">
                            Customer Management
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import { HasAnyRole } from '../utils/role'
    export default {
        data() {
            return {
                
            }
        },
        methods: {
            allowForRoles: function(roles) {
                if(HasAnyRole(roles)) {
                    return true;
                } else {
                    return false;
                }
            },
            goToInventoryPage: function() {
                if(this.allowForRoles(['admin','manager'])) {
                    this.$router.push({name: 'product_list'})
                }
            },
            goToSalePage: function(){
                this.$router.push({name:'sale_create'})
            },
            goToReportPage: function() {
                if(this.allowForRoles(['admin','manager'])) {
                    this.$router.push({name: 'sale_report'})
                }
            },
            goToCustomersPage: function() {
                this.$router.push({ name: 'list-customer' })
            }
        },
        computed: {
            
        },
    }
</script>


<style scoped>
    .card-flex {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        color: white;
        font-weight: bold;
        font-size: 30px;
        height: 200px;
    } 
</style>

