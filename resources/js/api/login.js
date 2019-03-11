import request from '../utils/request'

export function loginUser(username, password) {
    const data = {
        username,
        password
    }
    return request({
        url: '/api/auth/login',
        method: 'post',
        data
    })
}

export function registerUser(name,username, phone, email, password) {
    const data = {
        username,
        phone,
        email,
        name,
        password
    }
    return request({
        url: '/api/auth/register',
        method: 'post',
        data
    })
}

export function logOutUser() {
    return request({
        url: '/api/auth/logout',
        method: 'post'
    })
}

export function getUserInfo(token) {
    return request({
        url: 'api/user/info',
        method: 'get',
        params: { token }
    })
}