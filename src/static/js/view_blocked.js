var timeInterruption;
var cont;
$(document).ready(function(){
    countLabel = $("#countDownLabel");
    if( countLabel.length > 0 ){
        countLabelInnerHTML = countLabel.html();
        if( countLabelInnerHTML.match(/\d\+/) ){
            cont = Number(countLabelInnerHTML);
            timeInterruption = setInterval(function(myPar){
                cont--;
                if( cont < 0 ){
                    clearInterval(timeInterruption);
                    window.location = window.location.href
                }
                else{
                    countLabel.html(cont);
                }
            },
            1000
            );
        }
    }
});
