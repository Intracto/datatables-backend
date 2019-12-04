/*
 * This file is part of the Intracto datatables-backend package.
 *
 * (c) Intracto <http://www.intracto.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

var oDatatable;

/* ajax datatable */
function ajaxDatatable(path, sorting, filters, stateSaveAffix){
    var path = path || '';
    var sorting = sorting || [];
    var filters = filters || {};
    var stateSaveAffix = stateSaveAffix || '';

    if (oDatatable === undefined) {
        /* first init */
        initAjaxDatatable(path, sorting, filters, stateSaveAffix)
    } else {
        /* reload */
        oDatatable.ajax.reload();
    }
}

function initAjaxDatatable(path, sorting, filters, stateSaveAffix){
    var $ajaxDatatable = $('.ajaxdatatable');

    stateKey = window.location.pathname + stateSaveAffix;

    oDatatable = $ajaxDatatable.DataTable({
        "oLanguage": {
            "sLengthMenu": "Toon _MENU_ entries",
            "sZeroRecords": "Niets gevonden voor deze zoekopdracht",
            "sInfo": "Toon <b>_START_ tot _END_</b> van <b>_TOTAL_</b> entries",
            "sInfoEmpty": "Toon <b>0 tot 0</b> van <b>0</b> records",
            "sInfoFiltered": "(gefilterd uit <b>_MAX_</b> entries in totaal)",
            "sSearch": "Zoeken",
            "oPaginate": {
                "sFirst": "Eerste",
                "sPrevious": "« Vorige",
                "sNext": "Volgende »",
                "sLast": "Laatste"
            }
        },
        "bStateSave": true,
        "processing": true,
        "serverSide": true,
        "orderMulti": false,
        "ajax": {
            "url": path,
            "data": function(d) {
                d.filters = {};
                for (var filter in filters) {
                    d.filters[filter] = filters[filter].val();
                }
            }
        },
        "columns": sorting,
        "order": [[ getDefaultSortField($ajaxDatatable), getDefaultSortOrder($ajaxDatatable)]],
        "stateSaveCallback": function(settings, data) {
            localStorage.setItem('DataTables-' + settings.sInstance + stateKey, JSON.stringify(data))
        },
        "stateLoadCallback": function(settings) {
            return JSON.parse( localStorage.getItem('DataTables-' + settings.sInstance + stateKey))
        }
    });
}

function getDefaultSortField($datatable){

    var defaultSortCol = 0;
    var $sortCol = $datatable.find('th.default_sort');

    if ($sortCol.index() >= 0) {
        defaultSortCol = $sortCol.index();
    }

    return defaultSortCol;
}

function getDefaultSortOrder($datatable){
    var defaultSortOrder = 'asc';
    var $sortCol = $datatable.find('th.default_sort');

    if ($sortCol.index() >= 0) {
        if ($sortCol.hasClass('desc')) {
            defaultSortOrder = 'desc';
        }
    }

    return defaultSortOrder;
}