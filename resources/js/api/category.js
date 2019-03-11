import request from '../utils/request'


export function getCategory(category_data) {
    const data = category_data || null;
    return request({
        url: '/api/categories',
        method: 'get',
        data
    })
}



export function createCategory(category_data) {
    const data = category_data;
    return request({
        url: '/api/categories',
        method: 'post',
        data
    })
}

export function updateCategory(category_data) {
    const data = category_data;
    return request({
        url: `/api/categories/${data.id}`,
        method: 'put',
        data
    })
}

export function deleteCategory(category_data) {
    const data = category_data;
    return request({
        url: `/api/categories/${data.id}`,
        method: 'delete',
    })
}