import { apiGetDashborad } from '../api/dashboard'

export function GetDashboard( category_data ) {
    return new Promise((resolve, reject) => {
        apiGetDashborad(category_data)
        .then(response => {
            const data = response || {}
            resolve(data)
        }).catch(error => {
            reject(error)
        })
    })
}