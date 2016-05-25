//System elements
var syncNodeModels;
//MeterModels view elements
var meterModelsDiv;
var meterModelsAddBtn;

//Modal elements
function ModalPane(containerElement){
    this.container = containerElement;

    this.setToCreateSyncNodeModel = function(){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        var modalHeader = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': window.backEnd.glossary.GENERAL.CLOSE}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.ADD+" "+window.backEnd.glossary.GENERAL.MODEL})
                );
        //############ Body
        var nameTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-name"});

        var descTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-desc"});


        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-name", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NAME}),
                        nameTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-desc", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
                        descTxtField
                        )
                    )
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary capitalize", text: window.backEnd.glossary.GENERAL.ADD});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodemodels/create",
                data: {name: nameTxtField.val(), desc: descTxtField.val()},
                success: function (data){
                    if(data.status){
                        nameTxtField.val("");
                        descTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        window.loadSyncNodeModels();
                    }
                    else{
                        console.log("something gone wrong.");
                        if(data.errors.length > 0){
                            var errorList = $('<ul>')
                            for(index in data.errors){
                                errorList.append( $('<li>', {text: data.errors[index]}) );
                            }
                            errorPane.empty();
                            errorPane.append(errorList);
                        }
                    }
                },
                dataType: "json",
                accepts: {json: "application/json"}
            });
        });
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default capitalize", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CLOSE}),
                createButton
                );

        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    this.setToEditSyncNodeModel = function(syncNodeModel){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        var modalHeader = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': window.backEnd.glossary.GENERAL.CLOSE}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.EDIT + " " + window.backEnd.glossary.GENERAL.MODEL})
                );
        //############ Body
        var nameTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-name", value: syncNodeModel.name});

        var descTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-desc", value: syncNodeModel.description});


        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-name", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NAME}),
                        nameTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-desc", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
                        descTxtField
                        )
                    )
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary capitalize", text: window.backEnd.glossary.GENERAL.SAVE});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodemodels/update",
                data: {id:syncNodeModel.id, name: nameTxtField.val(), desc: descTxtField.val()},
                success: function (data){
                    if(data.status){
                        nameTxtField.val("");
                        descTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        window.loadSyncNodeModels();
                    }
                    else{
                        console.log("something gone wrong.");
                        if(data.errors.length > 0){
                            var errorList = $('<ul>')
                            for(index in data.errors){
                                errorList.append( $('<li>', {text: data.errors[index]}) );
                            }
                            errorPane.empty();
                            errorPane.append(errorList);
                        }
                    }
                },
                dataType: "json",
                accepts: {json: "application/json"}
            });
        });
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default capitalize", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CLOSE}),
                createButton
                );

        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    this.setToDeleteSyncNodeModel = function( elementToDelete ){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        var modalHeader = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': backEnd.glossary.GENERAL.CLOSE}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: backEnd.glossary.GENERAL.ALERT+"!"})
                );
        //############ Body

        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<p>',{"class": "capitalize-fl font-bold", text: window.backEnd.glossary.MSGS.SURE_ACK+" "+window.backEnd.glossary.GENERAL.DELETE+" "+backEnd.glossary.GENERAL.MODEL.toUpperCase()+" '"+elementToDelete.name+"'?"})
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtni", type: "button", class: "btn btn-danger capitalize", text: backEnd.glossary.GENERAL.DELETE});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodemodels/delete",
                data: {id: elementToDelete.id},
                success: function (data){
                    if(data.status){
                        errorPane.empty();
                        event.data.hide();
                        window.loadSyncNodeModels();
                    }
                    else{
                        console.log("something gone wrong.");
                        if(data.errors.length > 0){
                            var errorList = $('<ul>')
                            for(index in data.errors){
                                errorList.append( $('<li>', {text: data.errors[index]}) );
                            }
                            errorPane.empty();
                            errorPane.append(errorList);
                        }
                    }
                },
                dataType: "json",
                accepts: {json: "application/json"}
            });
        });
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default capitalize", 'data-dismiss': "modal", text: backEnd.glossary.GENERAL.CANCEL}),
                createButton
                );

        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    
    
    this.setToAppendCmd = function(syncNodeModel){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        var modalHeader = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': window.backEnd.glossary.GENERAL.CLOSE}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.ADD + " " + window.backEnd.glossary.APP.COMMAND})
                );
        //############ Body
        var nameTxtField = $('<input>', {type: "text", class: "form-control", id: "form-command-name"});
        var descTxtField = $('<textarea>', {type: "text", class: "form-control", id: "form-command-desc"});
        var queryTxtField = $('<textarea>', {type: "text", class: "form-control", id: "form-command-query"});
        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-command-name", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NAME}),
                        nameTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-command-desc", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
                        descTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-command-query", class: "control-label capitalize", text: window.backEnd.glossary.APP.QUERY_STRING}),
                        queryTxtField
                        )
                    )
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary capitalize", text: window.backEnd.glossary.GENERAL.ADD});
        createButton.off("click");

        createButton.click(this, function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodemodels/appendCmd",
                data: {id: syncNodeModel.id, name: nameTxtField.val(), desc: descTxtField.val(), query: queryTxtField.val()},
                success: function (data){
                    if(data.status){
                        nameTxtField.val("");
                        descTxtField.val("");
                        queryTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        syncNodeModel.commands.push(data.object);
                        window.syncNodeModelPane.showModel( syncNodeModel );
                    }
                    else{
                        console.log("something gone wrong.");
                        if(data.errors.length > 0){
                            var errorList = $('<ul>')
                            for(index in data.errors){
                                errorList.append( $('<li>', {text: data.errors[index]}) );
                            }
                            errorPane.empty();
                            errorPane.append(errorList);
                        }
                    }
                },
                dataType: "json",
                accepts: {json: "application/json"}
            });
        });
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default capitalize", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CLOSE}),
                createButton
                );

        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    this.setToEditCmd = function(model, command){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        var modalHeader = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': window.backEnd.glossary.GENERAL.ADD}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.EDIT + " " + window.backEnd.glossary.APP.COMMAND})
                );
        //############ Body
        var nameTxtField = $('<input>', {type: "text", class: "form-control", id: "form-command-name"});
        nameTxtField.val( command.name );
        var descTxtField = $('<textarea>', {type: "text", class: "form-control", id: "form-command-desc"});
        descTxtField.val( command.description );
        var queryTxtField = $('<textarea>', {type: "text", class: "form-control", id: "form-command-query"});
        queryTxtField.val( command.query );
        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-command-name", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NAME}),
                        nameTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-command-desc", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
                        descTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-command-query", class: "control-label capitalize", text: window.backEnd.glossary.APP.QUERY_STRING}),
                        queryTxtField
                        )
                    )
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary capitalize", text: window.backEnd.glossary.GENERAL.SAVE});

        createButton.off("click");

        createButton.click(this, function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"commands/update",
                data: {id: command.id, name: nameTxtField.val(), desc: descTxtField.val(), query: queryTxtField.val()},
                success: function (data){
                    if(data.status){
                        command.name = nameTxtField.val();
                        command.description = descTxtField.val();
                        command.query = queryTxtField.val();
                        errorPane.empty();
                        event.data.hide();
                        window.syncNodeModelPane.showModel( model );
                    }
                    else{
                        console.log("something gone wrong.");
                        if(data.errors.length > 0){
                            var errorList = $('<ul>')
                            for(index in data.errors){
                                errorList.append( $('<li>', {text: data.errors[index]}) );
                            }
                            errorPane.empty();
                            errorPane.append(errorList);
                        }
                    }
                },
                dataType: "json",
                accepts: {json: "application/json"}
            });
        });
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default capitalize", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CANCEL}),
                createButton
                );

        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    this.setToDeleteCmd = function( model, commandIndex ){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        var modalHeader = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': backEnd.glossary.GENERAL.CLOSE}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: backEnd.glossary.GENERAL.ALERT+"!"})
                );
        //############ Body

        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<p>',{"class": "capitalize-fl font-bold", text: window.backEnd.glossary.MSGS.SURE_ACK+" "+window.backEnd.glossary.GENERAL.DELETE+" "+backEnd.glossary.APP.COMMAND.toUpperCase()+" '"+model.commands[commandIndex].name+"'?"})
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-danger capitalize", text: window.backEnd.glossary.GENERAL.DELETE});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodemodels/removeCmd",
                data: { id_snm: model.id, id_cmd: model.commands[commandIndex].id },
                success: function (data){
                    console.log(data);
                    if(data.status){
                        errorPane.empty();
                        event.data.hide();
                        model.commands.splice(commandIndex, 1);
                        window.syncNodeModelPane.showModel( model );
                    }
                    else{
                        console.log("something gone wrong.");
                    }
                },
                dataType: "json",
                accepts: {json: "application/json"}
            });
        });
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default capitalize", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CANCEL}),
                createButton
                );

        modalContent.append(modalHeader);
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }

    this.show = function(){
        this.container.modal('show');
    }

    this.hide = function(){
        this.container.modal('hide');
    }

}

var modalPane;

function jsPane(containerElement){
    this.container = containerElement;
    this.currentDisplayed = null;

    this.showModel = function(syncNodeModel){
        this.container.empty();
        this.container.show();
        this.currentDisplayed = syncNodeModel;
        var paneHeader = $('<div>', {class: "panel-heading text-center"}).append(
                $('<span>',{class: "panel-title", text: syncNodeModel.name})
                );
        var addCmdBtn = $('<button>', {id: "addModelBtn", type:"button", class: "btn btn-success btn-xs", "title": window.backEnd.glossary.GENERAL.ADD}).append(
                $('<span>', {class: "glyphicon glyphicon-plus", 'aria-hidden': "true"})
                );
        addCmdBtn.off("click");
        addCmdBtn.click(syncNodeModel, function (event){
            window.modalPane.setToAppendCmd(event.data);
            window.modalPane.show();
        });
        var cmdList;
        if( syncNodeModel.commands && syncNodeModel.commands.length > 0 ){
            cmdList = $('<ul>', {class: "list-group"});
            for(index in syncNodeModel.commands){
                var editBtn =  $('<button>', {class: "btn btn-warning btn-xs capitalize", title: window.backEnd.glossary.GENERAL.EDIT}).append(
                        $('<span>', {class: 'glyphicon glyphicon-edit', 'aria-hidden':'true'})
                        );
                
                editBtn.click({ model: syncNodeModel, command: syncNodeModel.commands[index] }, function(event){
                    window.modalPane.setToEditCmd(event.data.model, event.data.command);
                    window.modalPane.show();
                });

                var deleteBtn = $('<button>', {class: "btn btn-danger btn-xs capitalize", title: backEnd.glossary.GENERAL.DELETE}).append(
                        $('<span>', {class: 'glyphicon glyphicon-remove', 'aria-hidden':'true'})
                        );
                deleteBtn.click({model: syncNodeModel, i: index},function(event){
                    window.modalPane.setToDeleteCmd(event.data.model, event.data.i);
                    window.modalPane.show();
                });
                cmdList.append(
                        $('<li>', {class:"list-group-item", text: syncNodeModel.commands[index].name}).append(
                            $('<div>', {class: "pull-right"}).append(
                                editBtn,
                                deleteBtn
                                )
                            )
                        );
            }
        }else{
            cmdList = $('<div>', {class: "alert alert-info", role: "alert", text: window.backEnd.glossary.MSGS.MODEL_NO_CMD});
        }
        var paneBody = $('<div>', {class: "panel-body"}).append(
                $('<strong>', {"class": "capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
                $('<p>', {text: syncNodeModel.description}),
                $('<strong>', {"class": "capitalize", text: window.backEnd.glossary.APP.COMMANDS}),
                addCmdBtn,
                cmdList
                );
        this.container.append(
                paneHeader,
                paneBody
                );
    }
    
    this.show = function(){
        this.container.show();
    }

    this.hide = function(){
        this.container.hide();
    }

}

var syncNodeModelPane;

function jsTable(containerElement, headers, attrNames){
    this.container = containerElement;
    this.headers = ["ID", backEnd.glossary.GENERAL.MODEL, backEnd.glossary.GENERAL.DESCRIPTION, backEnd.glossary.GENERAL.ACTIONS];
    this.attrNames = attrNames;
    this.currentDisplayed = null;
    
    this.draw = function(elements){
        this.container.empty();
        if(elements && elements.length > 0){
            var newTable = $('<table>',{id:"meterModelsTable", class:"table table-striped table-bordered table-hover table-condensed"});

            var headerRow = $('<tr>');
            for(index in this.headers){
                headerRow.append($('<th>',{"class": "capitalize", text: this.headers[index]}));
            }
            newTable.append(headerRow);

            for(index in elements){
                var newRow = $('<tr>');

                var idColumn = $('<td>', {text: elements[index].id});
                newRow.append( idColumn );
                var nameColumn = $('<td>', {text: elements[index].name});
                newRow.append( nameColumn );
                var descColumn = $('<td>', {text: elements[index].description});
                newRow.append( descColumn );
                
                var actionsColumn = $('<td>');
                
                var seeBtn = $('<button>', {class: "btn btn-primary btn-xs", title: backEnd.glossary.GENERAL.VIEW}).append(
                        $('<span>', {class: 'glyphicon glyphicon-eye-open', 'aria-hidden':'true'})
                        );

                seeBtn.click(elements[index], function(event){
                    window.syncNodeModelPane.showModel(event.data);
                });

                actionsColumn.append(seeBtn);
                
                var updateBtn =  $('<button>', {class: "btn btn-warning btn-xs", title: backEnd.glossary.GENERAL.EDIT});
                updateBtn.click( elements[index] , function(event){
                    window.modalPane.setToEditSyncNodeModel(event.data);
                    window.modalPane.show();
                });
                updateBtn.append( $('<span>', {class: 'glyphicon glyphicon-edit', 'aria-hidden':'true'}) );
                actionsColumn.append( updateBtn );
                
                var deleteBtn =  $('<button>', {class: "btn btn-danger btn-xs", title: backEnd.glossary.GENERAL.DELETE});
                deleteBtn.click( elements[index] , function(event){
                    window.modalPane.setToDeleteSyncNodeModel(event.data);
                    window.modalPane.show();
                });
                deleteBtn.append( $('<span>', {class: 'glyphicon glyphicon-remove', 'aria-hidden':'true'}) );

                actionsColumn.append( deleteBtn );
                
                newRow.append( actionsColumn );
                
                newTable.append(newRow);
            }
            this.container.append(newTable);
        }else{
            this.container.append($('<h1>',{text:'There is no registred Sync Node Model.', class:'text-center'}))//lang
        }
    }

    this.show = function(){
        this.container.show();
    }

    this.hide = function(){
        this.container.hide();
    }
}

var syncNodeModelsTable;

function loadSyncNodeModels(){
    $.ajax({
        type: "GET",
        url: window.backEnd.appFullPath+"syncnodemodels/read",
        success: function (data){
            if(data.status){
                window.syncNodeModels = data.object;
                window.syncNodeModelsTable.draw( data.object );
            }
            else{
                console.log("something gone wrong.");
            }
        },
        dataType: "json",
        accepts: {json: "application/json"}
    });
}


$(document).ready(function(){
    window.syncNodeModelsTable = new jsTable( $("#syncNodeModelsPane") );
    
    window.loadSyncNodeModels();
    
    window.modalPane = new ModalPane($("#myModal"));
    
    window.meterModelsAddBtn = $("#addModelBtn");
    window.meterModelsAddBtn.off("click");
    window.meterModelsAddBtn.click(function(){
        window.modalPane.setToCreateSyncNodeModel();
        window.modalPane.show();
    });
    window.syncNodeModelPane = new jsPane( $("#selectedSyncNodeModelPane") );
    window.syncNodeModelPane.hide();
});

