import * as userApi from '../api/user'


export function GetUser( _data ) {
    return new Promise((resolve, reject) => {
        userApi.getUser(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}

export function GetUserInfo( _data ) {
    return new Promise((resolve, reject) => {
        userApi.getUserInfo(_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}