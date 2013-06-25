CRUDFormQBehavior = {
    query: ".qimnet-crud-form-standalone form",
    initialize: function(target) {
        var iframe = target.find("iframe")
        iframe.load(function(event){
            var content = $(iframe.prop("contentWindow").document.body)
            var children = content.find(".crud-page>*")
            if (children.size()) {
                var page =$(".crud-page")
                page.empty().append(children)
                window.setTimeout(function(){ QBehavior.initialize(page) }, 200)
            }
        })
    }
}
QBehavior.plugins.push(CRUDFormQBehavior);