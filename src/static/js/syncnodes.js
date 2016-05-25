//Beans objects
var syncNodeModels;
var syncNodes;
//view objects 
var modalPane;
var syncNodesTable;
var syncNodePane;
var meterModelsAddBtn;

function ModalPane(containerElement){
    this.container = containerElement;

    this.setToCreateMeter = function(){
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
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.ADD+" "+window.backEnd.glossary.APP.NODE})
                );
        //############ Body
        var netAddrTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-netaddr"});
        var modelSelecList = $('<select>', {class:"form-control", id: "form-syncnode-model_id"});

        for(index in window.syncNodeModels){
            modelSelecList.append(
                    $('<option>', {value: window.syncNodeModels[index].id, text: window.syncNodeModels[index].name})
                    );
        }

        var addressTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-address"});


        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-model_id", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.MODEL}),
                        modelSelecList
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-netaddr", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NET_ADDRESS}),
                        netAddrTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-address", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.ADDRESS}),
                        addressTxtField
                        )
                    )
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary capitalize", text: window.backEnd.glossary.GENERAL.ADD});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodes/create",
                data: {id_snm: modelSelecList.val(), netaddr: netAddrTxtField.val(), address: addressTxtField.val()},
                success: function (data){
                    if(data.status){
                        modelSelecList.val("");
                        netAddrTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        window.loadSyncNodes();
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
    
    this.setToEditMeter = function( elementToEdit ){
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
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.EDIT+" "+ window.backEnd.glossary.APP.NODE})
                );
        //############ Body
        var netAddrTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-netaddr", value: elementToEdit.netAddr});
        var modelSelecList = $('<select>', {class:"form-control", id: "form-syncnode-model_id"});

        for(index in window.syncNodeModels){
            var optAttr = {value: window.syncNodeModels[index].id, text: window.syncNodeModels[index].name}
            if( window.syncNodeModels[index].id == elementToEdit.model.id ){
                optAttr.selected = true;
            }
            modelSelecList.append(
                    $('<option>', optAttr )
                    );
        }

        var addressTxtField = $('<input>', {type: "text", class: "form-control", id: "form-syncnode-address",value: elementToEdit.address});

        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<form>').append(
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-model_id", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.MODEL}),
                        modelSelecList 
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-netaddr", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.NET_ADDRESS}),
                        netAddrTxtField
                        ),
                    $('<div>', {class: "form-group"}).append(
                        $('<label>', {for: "form-syncnode-address", class: "control-label capitalize", text: window.backEnd.glossary.GENERAL.ADDRESS}),
                        addressTxtField
                        )
                    )
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-primary capitalize", text: backEnd.glossary.GENERAL.SAVE});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodes/update",
                data: {id: elementToEdit.id, id_snm: modelSelecList.val(), netaddr: netAddrTxtField.val(), address: addressTxtField.val()},
                success: function (data){
                    if(data.status){
                        modelSelecList.val("");
                        netAddrTxtField.val("");
                        errorPane.empty();
                        event.data.hide();
                        window.loadSyncNodes();
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
    
    this.setToDeleteMeter = function( elementToDelete ){
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
                $('<h4>', {class: "modal-title capitalize", id: "myModalLabel", text: window.backEnd.glossary.GENERAL.ALERT+"!"})
                );
        //############ Body

        var errorPane = $('<div>', {id: "myModalSubmitErr"});

        var modalBody = $('<div>', {class: "modal-body"}).append(
                errorPane,
                $('<p>',{"class": "capitalize-fl font-bold", text: window.backEnd.glossary.MSGS.SURE_ACK+" "+window.backEnd.glossary.GENERAL.DELETE+": "+backEnd.glossary.APP.SYNCNODE.toUpperCase()+" ID "+elementToDelete.id+"?"})
                );
        //############ Footer
        createButton = $('<button>', { id: "modalAddModelBtn", type: "button", class: "btn btn-danger capitalize", text: window.backEnd.glossary.GENERAL.DELETE});

        createButton.off("click");

        createButton.click(this,function(event){
            $.ajax({
                type: "POST",
                url: window.backEnd.appFullPath+"syncnodes/delete",
                data: {id: elementToDelete.id},
                success: function (data){
                    if(data.status){
                        errorPane.empty();
                        event.data.hide();
                        window.loadSyncNodes();
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
    
    this.showText = function(pString){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Body
        var modalBody = $('<div>', {class: "modal-body wordwrap", text: pString})
        //############ Footer
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CLOSE})
                );

        modalContent.append(modalBody);
        modalContent.append(modalFooter);
    }
    
    this.showHTML = function(HTMLelement){
        this.container.empty();
        var modalContent = $('<div>', {class:"modal-content"});
        this.container.append(
                $('<div>',{class: "modal-dialog", role: "document"}).append(
                    modalContent
                    )
                );
        //############ Body
        var modalBody = HTMLelement;
        //############ Footer
        var modalFooter = $('<div>', {class: "modal-footer"}).append(
                $('<button>', {type: "button", class: "btn btn-default", 'data-dismiss': "modal", text: window.backEnd.glossary.GENERAL.CLOSE})
                );

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

function jsTable(containerElement, headers, attrNames){

    this.container = containerElement;
    this.headers = headers 
    this.attrNames = attrNames;
    this.currentDisplayed = null;
    
    this.draw = function(elements){
        this.container.empty();
        if(elements && elements.length > 0){
            var newTable = $('<table>',{id:"meterModelsTable", class:"table table-striped table-bordered table-hover table-condensed"});

            var headerRow = $('<tr>');
            for(index in this.headers){
                headerRow.append($('<th>',{text: this.headers[index], "class": "capitalize"}));
            }
            newTable.append(headerRow);

            for(index in elements){
                var newRow = $('<tr>');

                var idColumn = $('<td>', {text: elements[index].id});
                newRow.append( idColumn );
                var nameColumn = $('<td>', {text: elements[index].model.name});
                newRow.append( nameColumn );
                var netAddrColumn = $('<td>', {text: elements[index].netAddr});
                newRow.append( netAddrColumn );
                var addrColumn = $('<td>', {text: elements[index].address});
                newRow.append( addrColumn );
                
                var actionsColumn = $('<td>');
         
                var seeBtn = $('<button>', {class: "btn btn-primary btn-xs", title: window.backEnd.glossary.GENERAL.VIEW}).append(
                        $('<span>', {class: 'glyphicon glyphicon-eye-open', 'aria-hidden':'true'})
                        );

                seeBtn.click(elements[index], function(event){
                    window.syncNodePane.showModel(event.data);
                });

                actionsColumn.append(seeBtn);
                
                var updateBtn =  $('<button>', {class: "btn btn-warning btn-xs", title: window.backEnd.glossary.GENERAL.EDIT});
                updateBtn.click( elements[index] , function(event){
                    window.modalPane.setToEditMeter(event.data);
                    window.modalPane.show();
                });
                updateBtn.append( $('<span>', {class: 'glyphicon glyphicon-edit', 'aria-hidden':'true'}) );
                actionsColumn.append( updateBtn );
                
                var deleteBtn =  $('<button>', {class: "btn btn-danger btn-xs", title: window.backEnd.glossary.GENERAL.DELETE});
                deleteBtn.click( elements[index] , function(event){
                    window.modalPane.setToDeleteMeter(event.data);
                    window.modalPane.show();
                });
                deleteBtn.append( $('<span>', {class: 'glyphicon glyphicon-remove', 'aria-hidden':'true'}) );

                actionsColumn.append( deleteBtn );
                
                newRow.append( actionsColumn );
                
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

function jsPane(containerElement){
    this.container = containerElement;
    this.currentDisplayed = null;

    this.showModel = function(syncNode){
        this.container.empty();
        this.container.show();
        this.currentDisplayed = syncNode;
        var paneHeader = $('<div>', {class: "panel-heading text-center"}).append(
                $('<span>',{class: "panel-title", text: syncNode.netAddr})
                );
        var cmdList;
        if( syncNode.model.commands && syncNode.model.commands.length > 0 ){
            cmdList = $('<ul>', {class: "list-group"});
            for(index in syncNode.model.commands){
                var sendCmdBtnIcon = $('<span>', {class: 'glyphicon glyphicon-play', 'aria-hidden':'true'});
                var sendCmdBtn = $('<button>', {class: "btn btn-info btn-xs", title: window.backEnd.glossary.GENERAL.RUN}).append(
                        sendCmdBtnIcon
                        );
                sendCmdBtn.click({sNode: syncNode, command: syncNode.model.commands[index], "icon": sendCmdBtnIcon},function(event){
                    var thisButton = this;
                    thisButton.setAttribute("disabled","");
                    event.data.icon[0].classList.remove("glyphicon-play");
                    event.data.icon[0].classList.add("glyphicon-refresh");
                    event.data.icon[0].classList.add("spinning");
                    console.log(event.data.icon[0]);
                    $.ajax({
                        type: "POST",
                        url: window.backEnd.appFullPath+"syncnodes/sendcmd",
                        data: { id_sn: event.data.sNode.id, id_cmd: event.data.command.id },
                        success: function (data){
                            event.data.icon[0].classList.remove("spinning");
                            event.data.icon[0].classList.remove("glyphicon-refresh");
                            event.data.icon[0].classList.add("glyphicon-play");
                            thisButton.removeAttribute("disabled");
                            if(data.status){
                                var responseString = JSON.stringify(data.object);
                                //window.modalPane.showText( responseString );
                                //
                                var ansValues = data.object.medida.split(",");
                                var ansLabels = ["Phase A Voltage", "Phase B Voltage", "Phase C Voltage","Phase A Current", "Phase B Current", "Phase C Current", "Phase A Power", "Phase B Power", "Phase C Power"] 
                                console.log( ansValues );

                                var ansTable = $('<table>', {"class": "table table-striped table-bordered table-hover table-condensed"});
                                for( indexAns in ansValues ){
                                    var newRow = $('<tr>').append(
                                        $('<td>', {text: ansLabels[indexAns]}),
                                        $('<td>', {text: parseFloat(ansValues[indexAns]).toFixed(2)})
                                        );
                                    ansTable.append(newRow);
                                }
                                window.modalPane.showHTML( ansTable );
                                window.modalPane.show();
                            }
                            else{
                                window.modalPane.showText(data.errors);
                                window.modalPane.show();
                            }
                        },
                        dataType: "json",
                        accepts: {json: "application/json"}
                    });
                });
                cmdList.append(
                        $('<li>', {class:"list-group-item", text: syncNode.model.commands[index].name}).append(
                            $('<div>', {class: "pull-right"}).append(
                                sendCmdBtn
                                )
                            )
                        );
            }
        }else{
            cmdList = $('<div>', {class: "alert alert-info", role: "alert", text: window.backEnd.glossary.MSGS.MODEL_NO_CMD});
        }
        var paneBody = $('<div>', {class: "panel-body"}).append(
                $('<strong>', {"class": "capitalize", text: "ID"}),
                $('<p>', {text: syncNode.id}),
                $('<strong>', {"class": "capitalize", text: window.backEnd.glossary.GENERAL.ADDRESS}),
                $('<p>', {text: syncNode.address}),
                $('<strong>', {"class": "capitalize", text: window.backEnd.glossary.GENERAL.MODEL}),
                $('<p>', {text: syncNode.model.name}),
                $('<strong>', {"class": "capitalize", text: window.backEnd.glossary.APP.COMMANDS}),
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

function loadSyncNodes(){
    $.ajax({
        type: "GET",
        url: window.backEnd.appFullPath+"syncnodes/read",
        success: function (data){
            if(data.status){
                window.syncNodes = data.object;
                window.syncNodesTable.draw(window.syncNodes);
            }
            else{
                console.log("something gone wrong.");
            }
        },
        dataType: "json",
        accepts: {json: "application/json"}
    });
}

function loadSyncNodeModels(){
    $.ajax({
        type: "GET",
        url: window.backEnd.appFullPath+"syncnodemodels/read",
        success: function (data){
            if(data.status){
                window.syncNodeModels = data.object;
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
    window.loadSyncNodes();
    window.loadSyncNodeModels();
    window.syncNodesTable = new jsTable( $("#syncNodesDiv"), ["ID", window.backEnd.glossary.GENERAL.MODEL, window.backEnd.glossary.GENERAL.NET_ADDRESS, window.backEnd.glossary.GENERAL.ADDRESS, window.backEnd.glossary.GENERAL.ACTIONS]);
    window.syncNodePane = new jsPane( $("#syncNodePane") );
    window.modalPane = new ModalPane( $("#myModal") );
    window.syncNodePane.hide();
    
    window.meterModelsAddBtn = $("#addModelBtn");
    window.meterModelsAddBtn.off("click");
    window.meterModelsAddBtn.click(function(){
        window.modalPane.setToCreateMeter();
        window.modalPane.show();
    });
});

