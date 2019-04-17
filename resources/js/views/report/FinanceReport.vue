<template>
    <v-container fluid>
        <v-layout>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense color="cyan" dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Finance report</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs4 style="background-color:grey">
                                <v-form
                                @submit.prevent="generateReport">
                                    
                                </v-form>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    export default {
        data() {
            return {
                search_data: {}
            }
        },
        methods: {
            generateReport: function(){

            }
        },
    }
</script>