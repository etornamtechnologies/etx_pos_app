<template>
    <v-layout>
        <v-flex xs12>
            <v-card>
                <v-layout column justify-center>
                    <v-flex xs12>
                        <v-toolbar flat color="cyan" dark dense>
                            <v-toolbar-side-icon></v-toolbar-side-icon>
                            <v-toolbar-title>SHOP CONFIGURATION</v-toolbar-title>
                        </v-toolbar>
                    </v-flex>
                    <v-flex>
                        <v-form @submit.prevent="handleSubmit" 
                        style="max-width:500px; margin:auto; margin-top:20px; margin-bottom:20px">
                            <v-layout column>
                                <v-flex xs12>
                                    <v-text-field
                                    label = "shop name"
                                    v-model="info.shop_name"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field
                                    label="shop phone"
                                    v-model="info.shop_phone"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field
                                    label="shop email"
                                    v-model="info.shop_email"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field
                                    label="shop address"
                                    v-model="info.shop_address"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field
                                    label="shop message"
                                    v-model="info.shop_message"></v-text-field>
                                </v-flex>
                                <!-- <v-flex xs12>
                                    <v-text-field
                                    label="profit margin"
                                    v-model="info.profit_margin"></v-text-field>
                                </v-flex> -->
                                <v-flex>
                                    <v-btn type="submit">
                                        <v-icon>{{ btnIcon }}</v-icon>
                                        {{ btnLabel }}
                                    </v-btn>
                                </v-flex>
                            </v-layout>
                        </v-form>
                    </v-flex>
                </v-layout>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    import { CreateShopSetup, UpdateShopSetup, GetShopSetup } from '../../utils/config' 
    export default {
        mounted() {
            this.fetchShopSetup();
        },
        data(){
            return {
                info:{}
            }
        },
        methods: {
            fetchShopSetup: function() {
                GetShopSetup({})
                    .then(result=> {
                        console.log('res',result);
                        this.info = result.config;
                    })
            },
            createShopSetup: function() {
                CreateShopSetup(this.info)
                    .then(result=> {
                        this.info = result.config
                    })
            },
            updateShopSetup: function() {
                UpdateShopSetup(this.info)
                    .then(result=> {
                        this.info = result.config
                    })
            },
            handleSubmit: function() {
                let infoId = this.info.id || null;
                if(infoId) {
                    this.updateShopSetup();
                } else {
                    this.createShopSetup();
                }
            }
        },
        computed: {
            btnIcon: function() {
                let id = this.info.id || null;
                if(id) {
                    return 'update'
                } else {
                    return 'add'
                }
            },
            btnLabel: function() {
                let id = this.info.id || null;
                if(id) {
                    return 'UPDATE'
                } else {
                    return 'CREATE'
                }
            }
        }
    }
</script>