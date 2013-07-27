/**
 * Project: Selfology
 * User: Hector Ordonez
 * Description:
 * Description: Action JS Library
 * Date: 25/07/13 01:30
 */

jQuery().ready(function () {
    var grid = '#grid';
    var pagination = '#gridpager';

    // General creation of the Grid.
    jQuery(grid).jqGrid({
        //Url from where jqGrid gets the data. The columns will be filled in the order in which the definition in this url is done.
        url: root_url + 'action/getActions',
        datatype: 'json',
        mtype: 'post',
        colNames: [
            //ColNames is the visual name of the column, which appears at the top.
            'Action Id',
            'Title',
            'Added date'
        ],
        colModel: [
            //ColModel defines the columns and its names.
            //Name is the key of the column. Index is needed for sorting the grid.
            {
                name: 'id', index: 'id', hidden: true
            },
            {
                name: 'title', index: 'title', width: 80, editable: true, edittype: 'text', editrules: {required: true}
            },
            {
                name: "date_creation", width: 20, align: "center", sorttype: "date",
                formatter: "date", formatoptions: { newformat: "Y-m-d" }, editable: false,
                editoptions: { required: false}
            }
        ],
        //Toppager adds the pagination to the top of the form.
        //Besides is needed for allowing the option 'cloneToTop'. Without the toppager cloneToTop does nothing.
        toppager: true,
        rowNum: 40,
        rowList: [10, 40, 100],
        pager: pagination,
        sortname: 'id',
        sortorder: 'asc',
        viewrecords: true,
        caption: 'Actions',
        jsonReader: {
            page: "page",
            total: "total",
            records: "records",
            root: "actions",
            repeatitems: false,
            id: "id"
        },
        height: '100%',
        autowidth: true,
        loadComplete: function () {
            // Binding the resizing event.
            resizeJQGridWidth('grid', 'grid_container', 400);
        }
    });

    // 2 - Adding additional options to the grid
    jQuery(grid).navGrid(pagination,
        {edit: false, add: false, del: true, search: false, refresh: true, cloneToTop: false}, // Pagination options
        {},
        {},
        { // Delete options
            url: 'action/completeAction',
            caption: 'Complete Action',
            errorTextFormat: function (data) {
                return data.statusText;
            }
        }, //del options
        {} // search options
    );
});