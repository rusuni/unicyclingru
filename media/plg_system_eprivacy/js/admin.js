window.addEvent('domready',function(){
    document.id('jform_params_displaytype').getChildren('input').each(function(el){
        el.setStyle('clear','left');
        if(el.checked) {
            plg_system_eprivacy_switchtype(el);
        }        
    });
    $$('.longtermcookie').each(function(el){
        if(el.checked) {
            plg_system_eprivacy_longtermcookieduration(el);
        }   
    });
});
var plg_system_eprivacy_switchtype = function(el) {
    var eloption = el;
    var displaytype = 'display' + el.value;
    $$('.displayspecific').each(function(el){
        var parent = el.getParent('li');
        if(parent === null) parent = el.getParent('div.control-group');
        if(el.hasClass(displaytype)) {
            parent.show();
        } else {
            parent.hide();
        }        
    });
    plg_system_eprivacy_typeoptions(eloption);
}
var plg_system_eprivacy_typeoptions = function(el) {
    var parentpanel;
    $$('.typeconfig').each(function(cel){
        parentpanel = cel.getParent('div.panel');
        if(parentpanel !== null) {
            if(cel.hasClass(el.value)) {
                parentpanel.show();
            } else {
                parentpanel.hide();
            }
        }
    });
}
var plg_system_eprivacy_longtermcookieduration = function(el){
    var parent = el.getParent('li');
    if(parent === null) parent = el.getParent('div.control-group');
    if(el.value == 1) {
        if(parent.isDisplayed()) {
            parent.show();
        }
    } else {
        parent.hide();        
    }
}