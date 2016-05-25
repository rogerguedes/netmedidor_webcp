var system = {"time": new Date(backEnd.serverTime*1000)}
var statusBar;
var secIntervalVar;
function updateStatusBar(){
    window.statusBar.empty();
    window.system.time.setSeconds( window.system.time.getSeconds() +1 );
    window.statusBar.append($('<div>', {"class": "col-md-12 text-right", "text": window.system.time.toUTCString() }));
}
$(document).ready(function(){
    window.statusBar = $(".status-bar");
    console.log( system.time.toUTCString() );
    updateStatusBar();
    window.secIntervalVar = window.setInterval(updateStatusBar, 1000);
});
