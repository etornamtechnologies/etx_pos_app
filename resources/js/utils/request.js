import axios from 'axios'
import { Message } from 'element-ui'
import store from '../store'
import { getApiToken } from '../utils/auth'
import router from '../router';

// create an axios instance
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // api base_url
  timeout: 500000 // request timeout
})

// request interceptor
service.interceptors.request.use(
    config => {
        console.log('req token',getApiToken())
        // Do something before request is sent
        if (store.getters.token) {
            config.headers['X-Token'] = getApiToken();
            config.headers['Authorization'] = getApiToken();
        }
        return config
    },
    error => {
        Promise.reject(error);
    }
)
// response interceptor
service.interceptors.response.use(
  response => {
    const res = response.data
    if (res.code !== 0) {
        Message({
            message: res.message,
            type: 'error',
            duration: 2 * 1000
        })
        if (res.code === 2) {
            store.dispatch('FedLogOut').then(() => {
                
            })
        }
        return Promise.reject('error')
    } else {
        if(res.message) {
            Message({
                message: res.message,
                type: 'success',
                duration: 2 * 1000
            })
        }
        return response.data
    }
  },
  error => {
    let errResponse = error.response || {};
    let status = errResponse.status;
    if(status === 422) {
        let data = errResponse.data || {}
        let errors = data.errors
        Object.keys(errors).forEach((error)=> {
            let er = errors[error];
            Message({
                message: er[0],
                type: 'error',
                duration: 2 * 1000
            })
        }) 
    } else if(status == 404) {
        Message({
            message: "Resource not available",
            type: 'error',
            duration: 2 * 1000
        })
    } 
    else if(status == 401) {
        router.push('/account/login')
    } else {
        Message({
            message: error.message,
            type: 'error',
            duration: 2 * 1000
        })
    }
    return Promise.reject(error)
  }
)

export default service