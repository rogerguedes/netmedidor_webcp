//System elements
var meterModels;

function arrayList(initArray){
    this.elements = initArray;

    this.push = function(element){
        this.elements.push(element);
    }

    this.findAndRemove = function findAndRemove(attr, value){
        var occurrences = [];
        if(this.elements.length > 0){
            for(var i = 0 ; i < this.elements.length ; i++){
                if(this.elements[i][attr] && this.elements[i][attr] === value){
                    occurrences.push(this.elements.splice(i, 1)[0]);
                }
            }
        }
        return occurrences;
    }
}

//MeterModels view elements
var meterModelsDiv;
var meterModelsAddBtn;

//Modal elements
function ModalPane(containerElement){
    this.container = containerElement;
    this.header = undefined;

    this.setHeaderParams = function(title, closeLabel){
        this.header = $('<div>', {class: "modal-header"}).append(
                $('<button>', {type: 'button', class: "close", 'data-dismiss': "modal", 'aria-label': closeLabel}).append(
                    $('<span>', {'aria-hidden': "true", html: "&times;"})
                    ),
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: title})
                ); 
    }

    this.setToCreateMM = function(){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        this.setHeaderParams( window.backEnd.glossary.GENERAL.ADD+" "+window.backEnd.glossary.GENERAL.MODEL, window.backEnd.glossary.GENERAL.CLOSE);
        //############ Body
        var nameTxtField = $('<input>', {type: "text", class: "form-control", id: "form-metermodel-name"});

        var descTxtField = $('<textarea>', {type: "text", class: "form-control", id: "form-metermodel-desc"});

        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-metermodel-name", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NAME}),
                        nameTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-metermodel-desc", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
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
                url: window.backEnd.appFullPath+"metermodels/create",
                data: {name: nameTxtField.val(), desc: descTxtField.val()},
                success: function (data){
                    if(data.status){
                        nameTxtField.val("");
                        descTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        window.meterModelsDiv.empty();
                        window.showMeterModels();
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

        modalContent.append( this.header );
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    this.setToEditMM = function(meterModel){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        this.setHeaderParams( window.backEnd.glossary.GENERAL.EDIT + " " + window.backEnd.glossary.GENERAL.MODEL, window.backEnd.glossary.GENERAL.CLOSE);
        //############ Body
        var nameTxtField = $('<input>', {type: "text", class: "form-control", id: "form-metermodel-name"});
        nameTxtField.val(meterModel.name);
        var descTxtField = $('<textarea>', {type: "text", class: "form-control", id: "form-metermodel-desc"});
        descTxtField.val(meterModel.description);
        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-metermodel-name", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NAME}),
                        nameTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-metermodel-desc", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.DESCRIPTION}),
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
                url: window.backEnd.appFullPath+"metermodels/update",
                data: {id: meterModel.id, name: nameTxtField.val(), desc: descTxtField.val()},
                success: function (data){
                    if(data.status){
                        nameTxtField.val("");
                        descTxtField.val("");
                        errorPane.empty();
                        console.log(event.data);
                        event.data.hide();
                        window.meterModelsDiv.empty();
                        window.showMeterModels();
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

        modalContent.append( this.header );
        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }

    this.setToAppendCmd = function(meterModel){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Header
        this.setHeaderParams( window.backEnd.glossary.GENERAL.ADD + " " + window.backEnd.glossary.APP.COMMAND, window.backEnd.glossary.GENERAL.CLOSE);
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
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary", text: window.backEnd.glossary.GENERAL.ADD});

        createButton.off("click");

        createButton.click(this, function(event){
            console.log(meterModel);
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"metermodels/appendCmd",
                data: {id: meterModel.id, name: nameTxtField.val(), desc: descTxtField.val(), query: queryTxtField.val()},
                success: function (data){
                    if(data.status){
                        nameTxtField.val("");
                        descTxtField.val("");
                        queryTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        meterModel.commands.push(data.object);
                        window.meterModelPane.showModel( meterModel );
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

        modalContent.append( this.header );
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
        this.setHeaderParams( window.backEnd.glossary.GENERAL.EDIT + " " + window.backEnd.glossary.APP.COMMAND, window.backEnd.glossary.GENERAL.CLOSE);
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
            //console.log({id: command.id, name: nameTxtField.val(), desc: descTxtField.val(), query: queryTxtField.val()});
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
                        window.meterModelPane.showModel( model );
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

        modalContent.append( this.header );
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

function MeterModelPane(containerElement){
    this.container = containerElement;
    this.currentDisplayed = null;

    this.showModel = function(meterModel){
        this.currentDisplayed = meterModel;
        this.container.empty();
        this.container.show();
        var paneHeader = $('<div>', {class: "panel-heading text-center"}).append(
                $('<span>',{class: "panel-title", text: meterModel.name})
                );
        var addCmdBtn = $('<button>', {id: "addModelBtn", type:"button", class: "btn btn-success btn-xs capitalize", "title": window.backEnd.glossary.GENERAL.ADD}).append(
                $('<span>', {class: "glyphicon glyphicon-plus", 'aria-hidden': "true"})
                );
        addCmdBtn.off("click");
        addCmdBtn.click(meterModel, function (event){
            window.modalPane.setToAppendCmd(event.data);
            window.modalPane.show();
        });
        var cmdList;
        if(meterModel.commands && meterModel.commands.length > 0){
            cmdList = $('<ul>', {class: "list-group"});
            for(index in meterModel.commands){
                var editBtn =  $('<button>', {class: "btn btn-warning btn-xs", title: window.backEnd.glossary.GENERAL.EDIT}).append(
                        $('<span>', {class: 'glyphicon glyphicon-edit', 'aria-hidden':'true'})
                        );
                
                editBtn.click({ model: meterModel, command: meterModel.commands[index] }, function(event){
                    window.modalPane.setToEditCmd(event.data.model, event.data.command);
                    window.modalPane.show();
                });

                var deleteBtn = $('<button>', {class: "btn btn-danger btn-xs", title: window.backEnd.glossary.GENERAL.DELETE}).append(
                        $('<span>', {class: 'glyphicon glyphicon-remove', 'aria-hidden':'true'})
                        );
                deleteBtn.click({model: meterModel, i: index},function(event){
                    $.ajax({
                        type: "POST",
                        url: window.backEnd.appFullPath+"metermodels/removeCmd",
                        data: {id_mm: event.data.model.id, id_cmd: event.data.model.commands[event.data.i].id},
                        success: function (data){
                            console.log(data);
                            if(data.status){
                                event.data.model.commands.splice(event.data.i, 1);
                                window.meterModelPane.showModel( meterModel );
                            }
                            else{
                                console.log("something gone wrong.");
                            }
                        },
                        dataType: "json",
                        accepts: {json: "application/json"}
                    });
                });
                cmdList.append(
                        $('<li>', {class:"list-group-item", text: meterModel.commands[index].name}).append(
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
                $('<p>', {text: meterModel.description}),
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

var meterModelPane;

function jsTable(containerElement, headers, attrNames){
    this.container = containerElement;
    this.headers = headers;
    this.attrNames = attrNames;
    this.currentDisplayed = null;
    
    this.draw = function(elements){
        this.container.empty();
        if( elements && elements.length > 0 && this.attrNames && this.attrNames.length > 0 ){
            var newTable = $('<table>',{"class": "table table-striped table-bordered table-hover table-condensed"});
            var headerRow = $('<tr>');
            for(index in this.headers){
                headerRow.append($('<th>',{"class": "capitalize", text: this.headers[index]}));
            }
            newTable.append(headerRow);

            for(index in elements){
                var newRow = $('<tr>');
                for(attrIndex in this.attrNames){
                    var colAttrs = {};
                    if( elements[index][this.attrNames[attrIndex]] ){
                        switch( typeof( elements[index][this.attrNames[attrIndex]] ) ){
                            case "string":
                                colAttrs.text = elements[index][this.attrNames[attrIndex]];
                                break;
                            case "object":
                                colAttrs.html = elements[index][this.attrNames[attrIndex]];
                                break;
                            default:
                                console.log("unknow type");
                                break;
                        }
                    }
                    newRow.append( $('<td>', colAttrs) );
                }
                newTable.append(newRow);
            }
            this.container.append(newTable);
        }else{
            this.container.append($('<h1>',{text: window.backEnd.glossary.MSGS.NO_REGISTERS, class:'text-center'}))
        }
    }

    this.show = function(){
        this.container.show();
    }

    this.hide = function(){
        this.container.hide();
    }
}

function showMeterModels(){
    $.ajax({
        type: "GET",
        url: window.backEnd.appFullPath+"metermodels/read",
        success: function (data){
            if(data.status){
                window.meterModels = data.object;
                for( index in window.meterModels ){

                    var actionsDiv = $('<div>');
    
                    var seeBtn = $('<button>', {class: "btn btn-primary btn-xs", title: window.backEnd.glossary.GENERAL.VIEW}).append(
                            $('<span>', {class: 'glyphicon glyphicon-eye-open', 'aria-hidden':'true'})
                            );
                    seeBtn.click(window.meterModels[index], function(event){
                        window.meterModelPane.showModel(event.data);
                    });
                    actionsDiv.append( seeBtn );

                    var updateBtn =  $('<button>', {class: "btn btn-warning btn-xs", title: window.backEnd.glossary.GENERAL.EDIT}).append(
                        $('<span>', {class: 'glyphicon glyphicon-edit', 'aria-hidden':'true'})
                        );
                    updateBtn.click(window.meterModels[index], function(event){
                        window.modalPane.setToEditMM(event.data);
                        window.modalPane.show();
                    });
                    actionsDiv.append( updateBtn );
                    
                    var deleteBtn = $('<button>', {class: "btn btn-danger btn-xs", title: window.backEnd.glossary.GENERAL.DELETE}).append(
                            $('<span>', {class: 'glyphicon glyphicon-remove', 'aria-hidden':'true'})
                            );
                    deleteBtn.click(window.meterModels[index], function(event){
                        $.ajax({
                            type: "POST",
                            url: window.backEnd.appFullPath+"metermodels/delete",
                            data: {id: event.data.id},
                            success: function (data){
                                console.log(data);
                                if(data.status){
                                    window.meterModelPane.hide();
                                    window.meterModelsDiv.empty();
                                    window.showMeterModels();
                                }
                                else{
                                    console.log("something gone wrong.");
                                }
                            },
                            dataType: "json",
                            accepts: {json: "application/json"}
                        });
                    });
                    actionsDiv.append( deleteBtn );
                    
                    window.meterModels[index].actions = actionsDiv;
                }
                window.meterModelsTable.draw( window.meterModels );
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
    window.meterModelsDiv = $("#meterModelsDiv");
    window.meterModelsTable = new jsTable( $("#meterModelsDiv"), ["ID", window.backEnd.glossary.GENERAL.MODEL, window.backEnd.glossary.GENERAL.DESCRIPTION, window.backEnd.glossary.GENERAL.ACTIONS], ["id", "name", "description", "actions"]);
    
    window.meterModelsAddBtn = $("#addModelBtn");
    
    window.modalPane = new ModalPane($("#myModal"));
    window.meterModelsAddBtn.off("click");
    window.meterModelsAddBtn.click(function(){
        window.modalPane.setToCreateMM();
        window.modalPane.show();
    });
    
    window.meterModelPane = new MeterModelPane($("#meterModelPane"));
    window.meterModelPane.hide();
    window.showMeterModels();
});

