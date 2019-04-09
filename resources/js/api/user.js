import request from '../utils/request';

export function getUser(token) {
    return request({
        url: '/api/users',
        method: 'get',
    })
}

export function getUserInfo() {
    return request({
        url: '/api/user/info',
        method: 'get',
    })
}