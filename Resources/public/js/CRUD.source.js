CRUD = {
    getRoutePrefix: function() {
        return $(".qimnet-crud").attr("data-route-prefix")
    },
    getRouteParams: function() {
        return $.parseJSON($(".qimnet-crud").attr("data-route-parameters"))
    },
    getToken: function() {
        return $(".qimnet-crud").attr("data-csrf-token")
    },
    getConfirmMessage: function() {
        return $(".qimnet-crud").attr("data-confirm-message")
    },
    openPage: function(url,force,replace) {
        (replace ? History.replaceState :History.pushState)(force ? {stamp: (new Date()).getTime()} : null,null, url)
    },
    getPath: function(type, parameters) {
        if (!parameters) parameters = {}
        $.each(CRUD.getRouteParams(), function(key, value){
            parameters[key] = value
        })
        return Routing.generate(CRUD.getRoutePrefix() + "_" + type, parameters)
    },
    redirectToIndex: function(force, replace) {
        CRUD.openPage(CRUD.getPath('index'), force, replace)
    }
};