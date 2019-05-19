<template>
    <v-container fluid>
        <v-layout column>
            <v-flex xs12>
                <v-card>
                    <v-toolbar dense color="cyan" dark>
                        <v-toolbar-side-icon></v-toolbar-side-icon>
                        <v-toolbar-title>Upload Products CSV / EXCEL</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-layout row>
                            <v-flex xs6>
                                <input
                                @change="handleFileUpload()"
                                type="file"
                                ref="file"/>
                            </v-flex>
                            <v-flex sx6>
                                <v-btn
                                dark
                                @click="uploadCsv"
                                color="cyan">
                                    <img src="/img/rolling.svg" alt="" v-if="isUploading">
                                    Upload
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
    import { CreateProductCsv } from '../../utils/product'
    export default {
        data() {
            return {
                file: null,
                isUploading: false,
            }
        },
        methods: {
            handleFileUpload: function() {
                this.file = this.$refs.file.files[0];
                console.log(this.file);
            },
            uploadCsv: function() {
                this.isUploading = true;
                let fd = new FormData();
                fd.append('file', this.file);
                CreateProductCsv(fd)
                    .then(result=> {
                        this.isUploading = false;
                        this.$router.push({ name: 'product_list' })
                    })
                    .catch(err=> {
                        this.isUploading = false;
                    })
            }
        },
    }
</script>