<template>
    <div class="container fluid">
        <div class="card">
            <div class="card-body py-0">
                <div class="row">
                    <div class="col-md-2" style="border-right:solid 1px gray">
                        <div class="row">
                            <div class="col-md-12">
                                Filter Sale Report
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <form @submit.prevent="generateReport">
                                    <div class="form-group">
                                        <label for="">User</label>
                                        <select class="form-control sm" v-model="search.user_id">
                                            <option value="">All</option>
                                            <option  v-for="user in users" :value="user.id" :key="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="">Filter-By</label>
                                        <select class="form-control" v-model="search.filter_by">
                                            <option value="transaction">Transaction</option>
                                            <option value="product">Product</option>
                                        </select>
                                    </div>
                                    <hr>
                                    <div class="form-group" v-if="search.filter_by == 'product'">
                                        <label for="">Product Name</label>
                                        <input class="form-control" v-model="search.product_name"/>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="">Date-Range</label>
                                            <input type="checkbox" v-model="search.is_date_range">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        nncjejedjenfjnfjnfnejf
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data(){
            return {
                search: {user_id:"",filter_by:'transaction', is_date_range: false
                            ,date:'', from_date:'', to_date:'', product:''},
                reports: [],
                users: [],
            }
        },
        methods: {
            generateReport: function() {
                
            }
        }
    }
</script>