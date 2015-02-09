(function($){
    var ePrivacy = Class.create({
        options: {
            accepted:false,
            displaytype: 'message',
            policyurl: '',
            media:'',
            autoopen:true,
            modalclass: '',
            modalwidth: '600',
            modalheight: '400',
            lawlink: '',
            version: 0,
            root: ''
        },
        init: function(options){ 
            var self = this;
            $.each(options,function(index,value){ self.options[index]=value; });
            var decline = parseInt(self.getDataValue());
            if(decline === 1 || decline === 2 || !self.options.autoopen) {
                self.hideMessage();
            } else {
                self.showMessage();
            }
            this.initElements();
            this.reloadAfterDecision();        
        },  
        initElements: function() {
            var self = this;
            $('button.plg_system_eprivacy_agreed').click(function() {
                    self.acceptCookies();
            });   
            $('button.plg_system_eprivacy_accepted').click(function() {
                    self.unacceptCookies();
            }); 
            $('button.plg_system_eprivacy_declined').click(function() {
                    self.declineCookies();
            });    
            $('button.plg_system_eprivacy_reconsider').click(function() {
                    self.undeclineCookies();
            });          
        },
        acceptCookies: function() {
            var self = this;
            self.setDataValue(2);
//            if(version_compare(self.options.version,'3.2','>=')) {
//                var url = $.url.parse(self.options.root);
//                delete url.query;
//                delete url.source;
//                url.params = {
//                    option:'com_ajax',
//                    plugin:'eprivacy',
//                    format:'json',
//                    task:'accept'
//                }
//                url = $.url.build(url);
//                $.ajax({url:url}).done(function(result){
//                    self.ajaxResponse('accept',result.data[0]);
//                });
//            } else {
                var myURI = $.url.parse(window.location.href);
                delete myURI.query;
                delete myURI.source;

                if(myURI.hasOwnProperty('params') && myURI.params.hasOwnProperty('eprivacy_decline'))
                    delete myURI.params.eprivacy_decline;

                if(myURI.params) {
                    myURI.params['eprivacy'] = 1;
                } else {
                    myURI.params = { eprivacy:1 };
                }
                window.location.href = $.url.build(myURI);
//            }
        },
        unacceptCookies: function() { 
            var self = this;
            var r = confirm(Joomla.JText._('PLG_SYS_EPRIVACY_CONFIRMUNACCEPT'));
            if(r===true) {
                self.setDataValue(1);
//                if(version_compare(self.options.version,'3.2','>=')) {
//                    var url = $.url.parse(self.options.root);
//                    delete url.query;
//                    delete url.source;
//                    url.params = {
//                        option:'com_ajax',
//                        plugin:'eprivacy',
//                        format:'json',
//                        task:'unaccept'
//                    }
//                    url = $.url.build(url);
//                    $.ajax({url:url}).done(function(result){
//                        self.ajaxResponse('unaccept',result.data[0]);
//                    });
//                } else {
                    var myURI = $.url.parse(window.location.href);
                    delete myURI.query;
                    delete myURI.source;

                    if(myURI.hasOwnProperty('params') && myURI.params.hasOwnProperty('eprivacy'))
                        delete myURI.params.eprivacy;

                    if(myURI.hasOwnProperty('params')) {
                        myURI.params['eprivacy_decline'] = 1;
                    } else {
                        myURI.params = { eprivacy_decline:1 };
                    }
                    window.location.href = $.url.build(myURI);
//                }
            }
        },
//        ajaxResponse: function(type,response) {
//            var self = this;
//            switch(type) {
//                case 'accept':
//                    if(response.accept) {
//                        self.hideMessage();
//                        if(response.hasOwnProperty('cookie'))
//                            $.cookie(response.cookie[0],response.cookie[1],{
//                                expires:response.cookie[2],
//                                path:response.cookie[3]
//                            });
//                    }
//                    break;
//                case 'unaccept':
//                    if(response.unaccept) { 
//                        if(response.hasOwnProperty('cookies'))
//                            $.each(response.cookies,function(index,value){
//                                $.removeCookie(value);
//                            });
//                    }
//                    break;
//            }
//            self.reloadAfterDecision(true); 
//        },
        declineCookies: function() {  
            var self = this;
            self.setDataValue(1);
            self.hideMessage();
        },
        undeclineCookies: function() {   
            var self = this;
            self.setDataValue(0);
            self.showMessage();
        },
        showMessage: function() {
            var self = this;    
            $('div.plg_system_eprivacy_declined').each(function(index){$(this).hide();});
            $('div.plg_system_eprivacy_accepted').each(function(index){$(this).hide();}); 
            switch(self.options.displaytype) {
                case 'message':
                case 'module':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).show();});
                    break;
                case 'confirm':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();}); 
                    this.displayConfirm();
                    break;
                case 'modal':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();});
                    this.displayModal();
                    break;
                case 'ribbon':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();});
                    this.displayRibbon();
                    break;
                case 'cookieblocker':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();});
                    break;
            }
        },  
        hideMessage: function() {        
            var self = this; 
            if(parseInt(self.getDataValue()) === 1) {
                $('div.plg_system_eprivacy_declined').show();
                $('div.plg_system_eprivacy_accepted').hide(); 
            } else {     
                $('div.plg_system_eprivacy_declined').hide();
                $('div.plg_system_eprivacy_accepted').show();
            }  
            switch(self.options.displaytype) {
                case 'message':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();});
                    break; 
                case 'confirm':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();});    
                    break;
                case 'module':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();});
                    break;
                case 'modal':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();}); 
                    SqueezeBox.close();
                    if(Browser.ie6 || Browser.ie7) { // mostly for IE7 - but IE6 just in case
                        if($('.plg_system_eprivacy_modal').length) {
                            document.id('sbox-window').hide();
                        }
                    }
                    break;
                case 'ribbon':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();}); 
                    $('div.activebar-container').each(function(index){$(this).remove();});
                    break;
                case 'cookieblocker':
                    $('div.plg_system_eprivacy_message').each(function(index){$(this).hide();}); 
                    $('div.plg_system_eprivacy_declined').each(function(index){$(this).hide();});
                    $('div.plg_system_eprivacy_accepted').each(function(index){$(this).hide();}); 
                    break;
            }
        },
        setDataValue: function(value) {
            if(navigator.appVersion.indexOf("MSIE 6.")!==-1 || navigator.appVersion.indexOf("MSIE 7.")!==-1) { 
                var element = document.getElementById('plg_system_eprivacy');
                element.setAttribute('plg_system_eprivacy_decline',value);
                element.save("oDataStore");
                return;
            } else {
                var mydomstorage=(window.localStorage || (window.globalStorage? globalStorage[location.hostname] : null));
                if(mydomstorage) {
                    mydomstorage.plg_system_eprivacy_decline=value;
                    return;
                }
                if (window.sessionStorage){
                    sessionStorage.setItem('plg_system_eprivacy_decline',value);
                    return;
                }
            }      
        },
        getDataValue: function() {
            var value = 0;
            if(navigator.appVersion.indexOf("MSIE 6.")!==-1 || navigator.appVersion.indexOf("MSIE 7.")!==-1) { 
                var element = document.getElementById('plg_system_eprivacy');
                element.load("oDataStore");
                value = element.getAttribute('plg_system_eprivacy_decline');
                return value;
            } else {
                var mydomstorage=(window.localStorage || (window.globalStorage? globalStorage[location.hostname] : null));
                if(mydomstorage) {
                    value = mydomstorage.plg_system_eprivacy_decline;
                    return value;
                }                
                if (window.sessionStorage){
                    value = sessionStorage.getItem('plg_system_eprivacy_decline');
                    return value;
                }
            }
            return value;
        },
        newEl: function(tag,attributes) {
            el = document.createElement(tag);
            if(attributes !== undefined) {
                $.each(attributes,function(index,value){
                    switch(index) {
                        case 'html':
                            $(el).html(value);
                            break;
                        case 'class':
                            $(el).addClass(value);
                            break;
                    }
                });
            }
            return el;
        },
        displayRibbon: function(){
            var self = this;
            var ribbon = self.newEl('div',{'class':'activebar-container'});
            $(document.body).append(ribbon);
            var message = self.newEl('p',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_MESSAGE')});
            $(ribbon).append(message);
            var decline = self.newEl('button',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_DECLINE'),'class':'decline'});
            $(message).prepend(decline);
            var accept = self.newEl('button',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_AGREE'),'class':'accept'});
            $(message).prepend(accept);
            if((self.options.policyurl && self.options.policyurl.length >0) || (self.options.lawlink && self.options.lawlink.length > 0)) {
                var links = self.newEl('ul',{'class':'links'});
                $(message).append(links);
                var link;
                if(self.options.policyurl && self.options.policyurl.length > 0) {
                    link = self.newEl('li');
                    $(links).append(link);
                    var policyurl = self.newEl('a',{'href':self.options.policyurl,'html':Joomla.JText._('PLG_SYS_EPRIVACY_POLICYTEXT')});
                    $(link).append(policyurl);
                }
                if(self.options.lawlink && self.options.lawlink.length > 0) {
                    link = self.newEl('li');
                    $(links).append(link);
                    var lawlink = self.newEl('a',{'href':self.options.lawlink,'html':Joomla.JText._('PLG_SYS_EPRIVACY_LAWLINK_TEXT')});
                    $(link).append(lawlink);
                    $(lawlink).click(function(){ window.open(lawlink.href); return false;});
                }
            }
            $(decline).click(function(){self.declineCookies();});
            $(accept).click(function(){self.acceptCookies();});        
            if(Browser.ie6 || Browser.ie7) {
                $(ribbon).css('position','absolute');
            }
        },
        displayConfirm: function() {
            var self = this;
            if(parseInt(self.getDataValue()) !== 1) {
                var r=confirm(Joomla.JText._('PLG_SYS_EPRIVACY_MESSAGE') + ' ' + Joomla.JText._('PLG_SYS_EPRIVACY_JSMESSAGE'));
                if (r===true) {
                    self.acceptCookies();
                } else {
                    self.declineCookies();
                } 
            }      
        },
        displayModal: function() {
            var self = this;
            if(parseInt(self.getDataValue()) !== 1) {   
                var c = self.newEl('div');
                var ctitle = self.newEl('h1',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_MESSAGE_TITLE')});
                $(c).append(ctitle);
                var cmessage = self.newEl('p',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_MESSAGE')});
                $(c).append(cmessage);
                if(self.options.policyurl && self.options.policyurl.length > 0) {            
                    var cpolicy = self.newEl('p');
                    $(c).append(cpolicy);
                    var cpolicylink = self.newEl('a',{'href':self.options.policyurl,'html':Joomla.JText._('PLG_SYS_EPRIVACY_POLICYTEXT')});
                    $(cpolicy).append(cpolicylink);             
                }     
                if(self.options.lawlink && self.options.lawlink.length > 0) {            
                    var claw = self.newEl('p');
                    $(c).append(claw);
                    var clawlink = self.newEl('a',{'href':self.options.lawlink,'html':Joomla.JText._('PLG_SYS_EPRIVACY_LAWLINK_TEXT')});
                    $(claw).append(clawlink);  
                    $(clawlink).click(function(){
                        window.open(this.href); return false;
                    });           
                }
                var cagree = self.newEl('button',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_AGREE'),'class':'plg_system_eprivacy_agreed'});
                $(c).append(cagree);
                $(cagree).click(function(){self.acceptCookies();});
                var cdecline = self.newEl('button',{'html':Joomla.JText._('PLG_SYS_EPRIVACY_DECLINE'),'class':'plg_system_eprivacy_declined'});
                $(c).append(cdecline);
                $(cdecline).click(function(){self.declineCookies();});
                var modaloptions = {
                    handler: 'adopt',
                    classWindow: 'plg_system_eprivacy_modal',
                    closable: false,
                    closeBtn: false,
                    size: {
                        x:parseInt(self.options.modalwidth),
                        y:parseInt(self.options.modalheight)
                    }
                };
                if(self.options.modalclass && self.options.modalclass.length > 0) modaloptions.classWindow = modaloptions.classWindow + ' ' + self.options.modalclass;
                SqueezeBox.initialize();
                SqueezeBox.open(c,modaloptions);
                $('#sbox-btn-close').css('display','none');
                if(Browser.ie6 || Browser.ie7) {  // IE sucks
                    document.id('sbox-window').show();
                }
            }      
        },
        reloadAfterDecision:function(force){
            force = (force == undefined)?false:true;
            var myURI = $.url.parse(window.location.href);
            delete myURI.query;
            delete myURI.source;
            if(force || (myURI.hasOwnProperty('params') && (myURI.params.hasOwnProperty('eprivacy') || myURI.params.hasOwnProperty('eprivacy_decline')))) {
                if(myURI.hasOwnProperty('params') && myURI.params.hasOwnProperty('eprivacy')) delete myURI.params.eprivacy;
                if(myURI.hasOwnProperty('params') && myURI.params.hasOwnProperty('eprivacy_decline')) delete myURI.params.eprivacy_decline;
                if(myURI.hasOwnProperty('params') && !myURI.params.length) delete myURI.params;
                window.location = $.url.build(myURI);
            }
        }
    });
    $(document).ready(function(){
        if(!window.plg_system_eprivacy_options.accepted) {
            if(!document.__defineGetter__) {  
                if(navigator.appVersion.indexOf("MSIE 6.")===-1 || navigator.appVersion.indexOf("MSIE 7.")===-1) { // javascript cookies blocked only in IE8 and up
                    Object.defineProperty(document, 'cookie', {
                        get: function(){return '';},
                        set: function(){return true;}
                    });
                }
            } else { // non IE browsers use this method to block javascript cookies
                document.__defineGetter__("cookie", function() { return '';} );
                document.__defineSetter__("cookie", function() {} );
            }
        }
        var plg_system_eprivacy_class = new ePrivacy(window.plg_system_eprivacy_options);        
    });
})(jQuery);
/*
function version_compare (v1, v2, operator) {
  // From: http://phpjs.org/functions
  // +      original by: Philippe Jausions (http://pear.php.net/user/jausions)
  // +      original by: Aidan Lister (http://aidanlister.com/)
  // + reimplemented by: Kankrelune (http://www.webfaktory.info/)
  // +      improved by: Brett Zamir (http://brett-zamir.me)
  // +      improved by: Scott Baker
  // +      improved by: Theriault
  // *        example 1: version_compare('8.2.5rc', '8.2.5a');
  // *        returns 1: 1
  // *        example 2: version_compare('8.2.50', '8.2.52', '<');
  // *        returns 2: true
  // *        example 3: version_compare('5.3.0-dev', '5.3.0');
  // *        returns 3: -1
  // *        example 4: version_compare('4.1.0.52','4.01.0.51');
  // *        returns 4: 1
  // BEGIN REDUNDANT
  this.php_js = this.php_js || {};
  this.php_js.ENV = this.php_js.ENV || {};
  // END REDUNDANT
  // Important: compare must be initialized at 0.
  var i = 0,
    x = 0,
    compare = 0,
    // vm maps textual PHP versions to negatives so they're less than 0.
    // PHP currently defines these as CASE-SENSITIVE. It is important to
    // leave these as negatives so that they can come before numerical versions
    // and as if no letters were there to begin with.
    // (1alpha is < 1 and < 1.1 but > 1dev1)
    // If a non-numerical value can't be mapped to this table, it receives
    // -7 as its value.
    vm = {
      'dev': -6,
      'alpha': -5,
      'a': -5,
      'beta': -4,
      'b': -4,
      'RC': -3,
      'rc': -3,
      '#': -2,
      'p': 1,
      'pl': 1
    },
    // This function will be called to prepare each version argument.
    // It replaces every _, -, and + with a dot.
    // It surrounds any nonsequence of numbers/dots with dots.
    // It replaces sequences of dots with a single dot.
    //    version_compare('4..0', '4.0') == 0
    // Important: A string of 0 length needs to be converted into a value
    // even less than an unexisting value in vm (-7), hence [-8].
    // It's also important to not strip spaces because of this.
    //   version_compare('', ' ') == 1
    prepVersion = function (v) {
      v = ('' + v).replace(/[_\-+]/g, '.');
      v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
      return (!v.length ? [-8] : v.split('.'));
    },
    // This converts a version component to a number.
    // Empty component becomes 0.
    // Non-numerical component becomes a negative number.
    // Numerical component becomes itself as an integer.
    numVersion = function (v) {
      return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
    };
  v1 = prepVersion(v1);
  v2 = prepVersion(v2);
  x = Math.max(v1.length, v2.length);
  for (i = 0; i < x; i++) {
    if (v1[i] == v2[i]) {
      continue;
    }
    v1[i] = numVersion(v1[i]);
    v2[i] = numVersion(v2[i]);
    if (v1[i] < v2[i]) {
      compare = -1;
      break;
    } else if (v1[i] > v2[i]) {
      compare = 1;
      break;
    }
  }
  if (!operator) {
    return compare;
  }

  // Important: operator is CASE-SENSITIVE.
  // "No operator" seems to be treated as "<."
  // Any other values seem to make the function return null.
  switch (operator) {
  case '>':
  case 'gt':
    return (compare > 0);
  case '>=':
  case 'ge':
    return (compare >= 0);
  case '<=':
  case 'le':
    return (compare <= 0);
  case '==':
  case '=':
  case 'eq':
    return (compare === 0);
  case '<>':
  case '!=':
  case 'ne':
    return (compare !== 0);
  case '':
  case '<':
  case 'lt':
    return (compare < 0);
  default:
    return null;
  }
}
*/