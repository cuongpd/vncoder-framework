function dtTruncateString(t,n){if(t.length<=n)return t;n=t.lastIndexOf(" ",n);return"."===t.charAt(n-1)?t.substring(0,n)+"..":t.substring(0,n)+"..."}
function dtWrapText(e,t){var r=/[\u4e00-\u9fa5]/.test(e)?e.split(""):e.split(" ");let o=[],n="";for(let e=0;e<r.length;e++)n.length+r[e].length<=t?n+=(""===n?"":" ")+r[e]:(o.push(n),n=r[e]);return""!==n&&o.push(n),o.join("<br>")}
function formatAbbreviatedNumber(e){return isNaN(e)?"":1e9<=e?Math.floor(e/1e9)+"B+":5e8<=e?"500M+":1e8<=e?"100M+":5e7<=e?"50M+":1e7<=e?"10M+":5e6<=e?"5M+":1e6<=e?Math.floor(e/1e6)+"M+":5e5<=e?"500k+":1e5<=e?"100k+":5e4<=e?"50k+":1e4<=e?Math.floor(e/1e3)+"k+":5e3<=e?"5k+":1e3<=e?"1k+":500<=e?"500+":100<=e?"100+":50<=e?"50+":10<=e?"10+":5<=e?"5+":1<=e?"1+":e.toString()}

$.fn.dataTable.ext.order['dom-input-checkbox'] = function  ( settings, col ) {
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? 1 : 0;
    });
};

let dataTableDefaults = {
    searching: true,
    ordering:  true,
    order: [[0, 'desc']],
    pagingType: 'simple_numbers',
    pageLength: 20,
    lengthMenu: [
        [20, 50, 100, 200, -1],
        [20, 50, 100, 200, "All"]
    ],
    dom: '<"dt-wrapper-lf"lf><"dt-wrapper-table table-responsive dt-responsive"t><"dt-wrapper-info"pi>',
    language: {
        lengthMenu: '_MENU_',
        search: '_INPUT_',
        searchPlaceholder: 'Tìm kiếm...',
        buttons: {
            copyTitle: 'Đã sao chép dữ liệu vào clipboard',
        }
    }
};
dataTableDefaults.columnDefs = [
    {
        targets: 'number',
        render: function (data, type) {
            if (type === 'display') {
                if(typeof data !== 'number') data = parseFloat(data);
                if(isNaN(data)) return '';
                if(Math.abs(data) > 999){
                    return data.toLocaleString('vi-VN', { maximumFractionDigits: 0 });
                }else{
                    if(Math.abs(data) > 1){
                        return data.toLocaleString('vi-VN', { maximumFractionDigits: 2 });
                    }else{
                        if(Math.abs(data) > 0.1){
                            return data.toLocaleString('vi-VN', { maximumFractionDigits: 3 });
                        }else{
                             return data.toLocaleString('vi-VN', { maximumFractionDigits: 6 });
                         }
                    }
                }
            }
            return data;
        }
    },
    {
        targets: 'float',
        render: function (data, type) {
            if (type === 'display') {
                if(typeof data !== 'number') data = parseFloat(data);
                if(isNaN(data)) return '';
                if(Math.abs(data) > 999){
                    return data.toLocaleString('vi-VN', { maximumFractionDigits: 1 });
                }else{
                    if(Math.abs(data) >= 10){
                        return data.toLocaleString('vi-VN', { maximumFractionDigits: 3 });
                    }else{
                        return data.toLocaleString('vi-VN', { maximumFractionDigits: 4 });
                    }
                }
            }
            return data;
        }
    },
    {
        targets: 'percent',
        render: function (data, type) {
            if (type === 'display') {
                if(typeof data !== 'number') data = parseFloat(data);
                if(isNaN(data) || data <= 0) return '';
                data = data * 100;
                return data.toLocaleString('vi-VN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
            }
            return data;
        }
    },
    {
        targets: 'number2',
        render: function (data, type) {
            if (type === 'display') {
                return formatAbbreviatedNumber(data);
            }
            return data;
        }
    },
    {
        targets: "link",
        render: function (data, type) {
            if (type === 'display' && data && (typeof data === 'string')) {
                let linkUrl = (data.startsWith('http://') || data.startsWith('https://')) ? data : 'https://' + data;
                return '<a href="' + linkUrl + '" target="_blank" class="dt-link">&#9758;Link</a>';
            }
            return data;
        }
    },
    {
        targets: "link-data",
        render: function (data, type) {
            if (type === 'display' && data && (typeof data === 'string')) {
                let linkUrl = (data.startsWith('http://') || data.startsWith('https://')) ? data : 'https://' + data;
                return '<a href="' + linkUrl + '" target="_blank" class="dt-link-data">' + linkUrl.replace(/^https?:\/\//, '') + '</a>';
            }
            return data;
        }
    },
    {
        targets: "photo",
        render: function (data, type) {
            if (type === 'display' && data && (typeof data === 'string')) {
                return '<img src="'+data+'" class="img-thumbnail dt-photo" style="max-height: 80px;" />';
            }
            return data;
        }
    },
    {
        targets: "photo200",
        render: function (data, type) {
            if (type === 'display' && data && (typeof data === 'string')) {
                return '<img src="'+data+'" class="img-thumbnail dt-photo" style="max-height: 200px;" />';
            }
            return data;
        }
    },
    {
        targets: "content",
        "createdCell": function (td, cellData, rowData, row, col) {
            let cleanData = cellData.replace(/\n/g, ' ');
            if (cleanData.length > 200) {
                let truncatedData = dtTruncateString(cleanData, 200);
                cleanData = truncatedData;
            }
            let wrappedText = dtWrapText(cleanData, 52);
            $(td).html(wrappedText);
            $(td).addClass('td-wrap-text');
        }
    },
    {
        targets: "checkbox",
        orderable: false,
        render: DataTable.render.select(),
    },
    {
        targets: "input-checkbox",
        orderDataType: "dom-input-checkbox"
    },
    {
        targets: "no-sort",
        orderable: false,
    }
];

if (typeof DATATABLE_EXPORT_BUTTON !== 'undefined' && DATATABLE_EXPORT_BUTTON) {
    dataTableDefaults.dom = '<"dt-wrapper-lf"lf><"dt-wrapper-table table-responsive dt-responsive"ti><"dt-wrapper-info"Bp>';
    dataTableDefaults.buttons = [
        {
            extend: 'print',
            text: '<i class="fa-duotone fa-solid fa-print"></i> Print',
            className: 'btn-sm btn-primary m-r-5',
            autoPrint: true
        },
        {
            extend: 'copy',
            text: '<i class="fa-duotone fa-solid fa-copy"></i> Copy',
            className: 'btn-sm btn-info m-r-5'
        },
        {
            extend: 'csv',
            text: '<i class="fa-duotone fa-solid fa-file-csv"></i> CSV',
            className: 'btn-sm btn-success m-r-5'
        }
    ];
}

$.extend($.fn.dataTable.defaults, dataTableDefaults);