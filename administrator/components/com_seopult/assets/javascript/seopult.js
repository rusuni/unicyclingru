/**
 * Created with JetBrains PhpStorm.
 * User: mitrich
 * Date: 08.10.12
 * Time: 13:01
 */
window.addEvent("load",function(){
    var msg = $("seoMessage");
    var hdr = $$("header")[0];
    msg.inject(hdr,"after");

    var closers = $$('.removeMsg');
    if (closers.length > 0)
    {
        closers.addEvent('click',function(e){

            var par = this.getParent('pre');
            var myRequest = new Request({url: 'index.php?option=com_seopult&task=readMsg&msg_id='+this.get('rel')+'&format=raw', method: 'post',
                    onSuccess: function(responseText)
                    {
                        par.dispose();
                    },
                    onRequest: function(){
                    }
                }
            );
            myRequest.send();




        });
    }
});