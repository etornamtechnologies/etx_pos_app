<template>
    <div class="login-container">
        <div class="left">
            
        </div>
        <div class="right">
            <v-card
            style="padding:10px; border-radius:5px; outline:none"
            elevation = "10"
            width="70%"
            raised
            height="80%"
            max-height="80%">
                <form class="login-form" @submit.prevent="handleLogin">
                    <div class="login-form-header">
                        <span>Log in</span>
                    </div>
                    <div class="login-form-inputs">
                        <v-text-field
                        color="#2ccbe8"
                        label="Username / Email Address"
                        v-model="form.username"
                        single-line
                        ></v-text-field>

                        <v-text-field
                        :color="passwordFieldColor"
                        label="Password"
                        :type="password_type"
                        v-model="form.password"
                        :append-icon="passwordAppendIcon"
                        single-line
                        @click:append="togglePasswordVisibility"
                        ></v-text-field>
                        <div class="form-btn-div">
                        <v-btn
                        type="submit"
                        ripple
                        color="#2ccbe8">LOG IN</v-btn>
                        </div>
                    </div>
                </form>
            </v-card>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                agree_terms: false,
                form: {username:"", password:"", is_agree_terms: false},
                password_type:'password',
                loading: false,
            }
        },
        methods: {
            logInUser: function(){
                console.log(this.form);
            },
            togglePasswordVisibility: function() {
                if(this.password_type === 'password') {
                    this.password_type = '';
                } else {
                    this.password_type = 'password';
                }
            },
            handleLogin: function() {
                this.loading = true;
                this.$store.dispatch('LoginUser', this.form)
                    .then((res)=> {
                        console.log('signun', res);
                        this.loading = false;
                        this.$router.push({ path: '/' })
                    })
                    .catch((err)=> {
                        console.log('err', err);
                        this.loading = false;
                    })
            }
        },
        computed: {
            passwordAppendIcon: function() {
                if(this.password_type === 'password') {
                    return 'visibility_off';
                } else {
                    return 'visibility_on';
                }
            },
            passwordFieldColor: function(){
                return '#2ccbe8';
            }
        }
    }
</script>
<style scoped>
    .login-container {
        height: 100%;
        width: 100%;
        background-image: url('/img/analysis.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        background-attachment: scroll;
        resize: both;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
    }
    .left {
        background-color: black;
        opacity: 0.2;
        width: 50%;
    }
    .right {
        width: 50%;
        height: 100%;
        background-color: #12b4d1;
        -webkit-box-shadow: -4px 0px 27px 2px rgba(0,0,0,0.46); 
        box-shadow: -4px 0px 27px 2px rgba(0,0,0,0.46);
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }
    .login-form {
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        width: 100%;
        height: 100%;
    }
    .login-form-header {
        text-align: center;
        padding: 10px 0px 10px 0px; 
        height: 10%;
    }
    .login-form-header span {
        font-size: 25px;
        font-weight: 300;
        color: #2ccbe8;
    }
    .login-form-inputs {
        height: 90%;
        width: 100%;
        padding: 150px 40px 0 40px;
    }
    .form-btn-div {
        width: 100%;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
    }
</style>
