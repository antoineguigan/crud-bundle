var QimnetCRUD = (function($, confirmMessage){

    return {
        initialize: function() {
            $(".qimnet-crud-index").each(function(){
                var form = $(this),
                    headerCheckbox = $(".qimnet-crud-header-checkbox").find("input"),
                    checkboxes = $(".qimnet-crud-checkbox").find("input")
                headerCheckbox
                    .click(function(){
                        checkboxes.prop("checked",this.checked)
                    })
                checkboxes.click(function(){
                    if (!this.checked) {
                        headerCheckbox.prop("checked",false)
                    }
                })
                $(".qimnet-crud-index-actions").find("input")
                    .click(function(){
                        var ids = []
                        checkboxes.filter(":checked").each(function(){
                            ids.push(this.value)
                        })
                        if (checkboxes.length && confirm(confirmMessage)) {
                            form
                                .attr("action", this.getAttribute("data-action"))
                                .submit()
                        }
                    })
                $(".qimnet-crud-delete").click(function(){
                    if (confirm(confirmMessage)) {
                        $("<form method='POST'><input type='hidden' name='_token'></form>")
                            .insertAfter(form)
                            .attr("action", this.getAttribute("data-action"))
                            .children()
                            .val(form.find(".qimnet-crud-token").val())
                            .end()
                            .submit()
                    }
                })
            })
        }
    }
})(jQuery, "Are you sure?")
