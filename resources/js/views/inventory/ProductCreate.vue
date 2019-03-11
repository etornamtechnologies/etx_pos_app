<template>
    <v-container fluid>
        <v-layout>
            <v-flex>
                <v-card>
                    <v-toolbar color="cyan" dark flat>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>CREATE PRODUCT</v-toolbar-title>
                    </v-toolbar>
                    <form @submit.prevent="createProduct" style="width:500px">
                        <v-layout column justify-center>
                            <v-flex xs6 md6 align-center>
                                <v-text-field
                                label="LABEL"
                                v-model="product.label"></v-text-field>
                            </v-flex>
                            <v-flex xs6>
                                <v-text-field
                                label="BARCODE"
                                v-model="product.barcode"></v-text-field>
                            </v-flex>
                            <v-flex>
                                <v-select></v-select>
                            </v-flex>
                        </v-layout>
                    </form>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    export default {
        data(){
            return {
                product: {},
            }
        }
    }
</script>