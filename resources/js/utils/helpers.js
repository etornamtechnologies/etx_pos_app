
export function makeQueryString(_data) {
    let data = _data || {};
    let dataList = Object.keys(data) || [];
    let strList = dataList.map(key=> {
        return `${key}=${data[key]}`;
    }) || []
    return strList.join('&');
}