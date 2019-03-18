
export function makeQueryString(exp_data) {
    let data = exp_data || {};
    let dataList = Object.keys(data) || [];
    let strList = dataList.map(key=> {
        return `${key}=${data[key]}`;
    }) || []
    return strList.join('&');
}