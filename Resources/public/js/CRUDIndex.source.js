CRUDIndexQBehavior = {
    query: ".qimnet-crud-index",
    redirectDelete: function() {
        CRUD.redirectToIndex(true, true)
    },
    initialize: function(target) {
        target.find(".delete").click(function() {
            if (confirm(CRUD.getConfirmMessage())) {
                $.post($(this).attr("href"), {
                        _token: CRUD.getToken(),
                        _ajax_link: 1,
                        _crud_flash: 1
                    },function() {
                        CRUDIndexQBehavior.redirectDelete()
                    })
            }
            return false;
        })
        var headerCheckbox = target.find("thead input[type='checkbox']")
        var checkboxes = target.find("tbody input[type='checkbox']")
        headerCheckbox.click(function(){
            checkboxes.prop('checked', headerCheckbox.prop("checked"))
        })
        checkboxes.click(function(){
            if (!$(this).prop("checked")) {
                headerCheckbox.prop("checked", false)
            }
        })
        target.find(".qimnet-crud-index-actions input").click(function(){
            if (confirm(target.attr("data-confirm-message"))) {
                var ids=[]
                checkboxes.each(function() {
                    var checkbox = $(this)
                    if (checkbox.prop("checked")) ids.push(checkbox.val())
                })
                if (ids.length && confirm(CRUD.getConfirmMessage())) {
                    $.post(CRUD.getPath('batchdelete'), {
                        ids: ids,
                        _token: CRUD.getToken(),
                        _ajax_link: 1,
                        _crud_flash: 1
                        
                    }, function(){
                        CRUDIndexQBehavior.redirectDelete()
                    })
                }
            }
            return false
        })
    }
}

QBehavior.plugins.push(CRUDIndexQBehavior);