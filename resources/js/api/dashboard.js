import request from '../utils/request'
import { makeQueryString } from '../utils/helpers'

export function apiGetDashborad(data) {
    return request({
        url: `/api/dashboard`,
        method: 'get',
        data
    })
}