
export function makeQueryString(_data) {
    let data = _data || {};
    let dataList = Object.keys(data) || [];
    let strList = dataList.map(key=> {
        return `${key}=${data[key]}`;
    }) || []
    return strList.join('&');
}

export function formatDate(date_str) {
    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    let today  = new Date();
    return today.toLocaleDateString("en-US", options)
}