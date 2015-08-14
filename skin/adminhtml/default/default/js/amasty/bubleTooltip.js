/**
* @author Amasty Team
* @copyright Copyright (c) 2013 Amasty (http://www.amasty.com)
* @package Amasty_Audit 
*/
function findTopLeft(obj)
{
    var curleft = curtop = 0;
    if (obj.offsetParent) {
        curleft = obj.offsetLeft
        curtop = obj.offsetTop
        while (obj = obj.offsetParent) {
            curleft += obj.offsetLeft
            curtop += obj.offsetTop
        }
    }
    return [curleft,curtop];
}

//class for onmouseover showing option name
Buble = Class.create();
Buble.prototype = 
{    
    isCreated : false,
    
    bubleTooltip : null,
    
    text : null, 
    
    parent : null,
    
     initialize : function()
    {
        var me = this;    
    },  
    
    showToolTip : function(element)
    {
        var bubleTooltip = $('bubble');
        var bubleMiddle = $('buble_middle');
        var parent  =  element.parentNode;
        parent.appendChild(bubleTooltip);
        var idItemNum = element.id;
        postData = 'idItem=' + idItemNum + "&form_key=" + amauditFormKey;
        new Ajax.Updater(bubleMiddle, amauditConlrollUrl, {
            method: 'post',
            postBody : postData,
            onComplete: function()
            {
                bubleTooltip.style.display = 'block'; 
                var offset = findTopLeft(element);
                var newLeft = offset[0] + 10 - bubleTooltip.getWidth();
                if(newLeft < 0) newLeft = 0;
                var newTop = offset[1] - bubleTooltip.getHeight() + 5;
                if(newTop < 0) newTop = 0;
                bubleTooltip.style.left =  30 + "%";
                bubleTooltip.style.top = newTop  + "px";

                $('bubble').style.opacity = 0;
                new Effect.Opacity('bubble', { from: 0, to: 1, duration: 0.1 });    
                this.bubleTooltip = bubleTooltip;
                Event.observe(bubleTooltip, 'click',buble.clearEvent );
                this.isCreated = true;
                
            }.bind(this)
        }); 
    },
    
    hideToolTip : function()
    {
         if(this.isCreated){
            $('bubble').hide();
            $$('body')[0].appendChild($('bubble'));
            this.isCreated = false;   
        }    
    },
    clearEvent : function(event)
    {
        event.preventDefault();
        event.stopPropagation()
        
    }
    
}
 var buble = new Buble();

 Event.observe(window, 'keyup', function(evt) {
     var code;
     if (evt.keyCode) code = evt.keyCode;
     else if (evt.which) code = evt.which;
     if (code == Event.KEY_ESC) {
        buble.hideToolTip(this);
        return false;
     }
 });
 