<template>
    <v-layout>
        <v-flex xs12>
            <v-card>
                <v-toolbar color="cyan" dark>
                    <v-toolbar-side-icon></v-toolbar-side-icon>
                    <v-toolbar-title>Backup and Restore</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-menu bottom left>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                dark
                                icon
                                v-on="on"
                            >
                                <v-icon>more_vert</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-tile
                            @click="createBackup">
                                <v-list-tile-title>Create Backup</v-list-tile-title>
                            </v-list-tile>
                                <v-list-tile>
                                <v-list-tile-title>Restore</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-toolbar>
                
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    export default {
        data(){
            return {

            }
        },
        methods: {
            createBackup: function(){
                console.log('create backup')
            }
        }
    }
</script>