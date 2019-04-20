import request from '../utils/request'


export function updateBatch(_data) {
    const data = _data || null;
    return request({
        url: `/api/batches/${data.id}`,
        method: 'put',
        data
    })
}

export function deleteBatch(_data) {
    const data = _data || null;
    return request({
        url: `/api/batches/${data.id}`,
        method: 'delete',
        data
    })
}