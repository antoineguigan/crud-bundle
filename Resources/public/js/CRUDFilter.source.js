CRUDFilterQBehavior = {
    query: ".qimnet-crud-filters",
    initialize: function(target) {
        target.find("form").submit(function(){
            var data = $(this).serialize() + '&_ajax_link=1'
            $.post(CRUD.getPath('filter'), data, function(){
                CRUD.redirectToIndex(true, true)
            })
            return false
        })
    }
}
QBehavior.plugins.push(CRUDFilterQBehavior);