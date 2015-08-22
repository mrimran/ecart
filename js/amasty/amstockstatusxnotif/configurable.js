/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Stockstatus Amasty_Xnotif
*/

StockStatus = Class.create();
StockStatus.prototype = 
{
    options : null,
    configurableStatus : null,
	spanElement: null,
    
    initialize : function(options)
    {
        this.options = options;
        document.observe("dom:loaded", function() {
            stStatus.onConfigure('', $$('select.super-attribute-select'));
        })
		this.spanElement = $$('p.availability span:last-child')[0];

        this._rewritePrototypeFunction();
    },
/**
 * show stock alert block in wrapper
 */
    showStockAlert: function(code)
    {
        var wrapper = $('product-options-wrapper');
        var beforeNode = wrapper.childElements()[0];
        var span = document.createElement('span');
        span.id  = 'amstockstatus-stockalert'; 
        span.innerHTML = code;
        wrapper.insertBefore(span, beforeNode);

        $$('.product-options p.required').each(function(required) {
                    required.style.position = 'relative';
                    required.style.top = '0px';
        }.bind(this));
    },
    /**
     * remove stock alert block
     */
    _hideStockAlert: function()
    {
    	if ($('amstockstatus-stockalert'))
    	{
    		$('amstockstatus-stockalert').remove();
    	}
    },
    /*
    * configure statuses at product page
    */
    onConfigure : function(key, settings, realKey)
    {
	    this._hideStockAlert();
        this._removeStockStatus();
        if (null == this.configurableStatus && this.spanElement)
        {
            this.configurableStatus = this.spanElement.innerHTML;
        }
        //get current selected key
        var selectedKey = "";
        for (var i = 0; i < settings.length; i++){
            if(parseInt(settings[i].value) > 0){
                selectedKey += settings[i].value + ',';
            }
        }
        var trimSelectedKey = selectedKey.substr(0, selectedKey.length - 1);
        var countKeys = selectedKey.split(",").length - 1;

        /*reload main status*/
        if ('undefined' != typeof(this.options[trimSelectedKey]))
        {
            this._reloadContent(trimSelectedKey);
        }
        else{
            this._reloadDefaultContent(trimSelectedKey);
        }

        /*add statuses to dropdown*/
        for (var i = 0; i < settings.length; i++)
        {
            for (var x = 0; x < settings[i].options.length; x++)
            {
                if(!settings[i].options[x].value) continue;

                if(countKeys  ==  i + 1) {
                    var keyCheckParts = explode(',', trimSelectedKey);
                    keyCheckParts[keyCheckParts.length - 1] = settings[i].options[x].value;
                    var keyCheck = implode(',', keyCheckParts);

                }
                else{
                    var keyCheck  = selectedKey + settings[i].options[x].value;
                }

                if ('undefined' != typeof(this.options[keyCheck]) && this.options[keyCheck])
                {
                    var status = this.options[keyCheck]['custom_status'];
                    if (status)
                    {
                        status     = status.replace(/<(?:.|\n)*?>/gm, ''); // replace html tags
                        if (!strpos(settings[i].options[x].text, status))
                        {
                            if('undefined' != typeof(Product.ConfigurableSwatches)){
                                var text = settings[i].options[x].text;
                                if(text.indexOf('(') > 0){
                                    text = text.substring(0, text.indexOf('('));
                                    settings[i].options[x].text = text;
                                }
                            }
                            settings[i].options[x].text = settings[i].options[x].text + ' (' + status + ')';
                        }
                    }
                }
            }
        }

    },
    /*
    * reload default stock status after select option
    */
    _reloadContent : function(key)
    {
        if ('undefined' != typeof(changeConfigurableStatus) && changeConfigurableStatus && this.spanElement)
        {
            if (this.options[key] && this.options[key]['custom_status'])
            {
                if(this.options[key]['custom_status_icon_only'] == 1){
                    this.spanElement.innerHTML = this.options[key]['custom_status_icon'];
                } else{
                    this.spanElement.innerHTML = this.options[key]['custom_status_icon'] + this.options[key]['custom_status'];
                }
            } else
            {
                this.spanElement.innerHTML = this.configurableStatus;
            }
        }
        if ('undefined' != typeof(this.options[key]) && this.options[key] &&  this.options[key]['custom_status'])
        {
            $$('.product-options-bottom .price-box').each(function(pricebox) {
                span = document.createElement('span');
                span.id = 'amstockstatus-status';
                span.style.paddingLeft = '10px';
                span.innerHTML = this.options[key]['custom_status'];
                pricebox.appendChild(span);
            }.bind(this));
        }
        if ('undefined' != typeof(this.options[key]) && this.options[key] && 0 == this.options[key]['is_in_stock']) {
            $$('.add-to-cart').each(function(elem) {
                elem.hide();
            });
            if (this.options[key]['stockalert']) {
                this.showStockAlert(this.options[key]['stockalert']);
            }
        } else {
            $$('.add-to-cart').each(function(elem) {
                elem.show();
            });
        }
    },

    _reloadDefaultContent : function(key)
    {
        if(this.spanElement) {
            this.spanElement.innerHTML = this.configurableStatus;
        }
        $$('.add-to-cart').each(function(elem) {
            elem.show();
        });
    },

    _removeStockStatus : function()
    {
        if ($('amstockstatus-status'))
        {
            $('amstockstatus-status').remove();
        }
    },
    /*
    * rewrite methods from /js/varien/configurable.js
    */
    _rewritePrototypeFunction : function(){
        /*Amasty Preorder*/
        if(typeof PreorderNoteConfigurable != 'undefined') {
            PreorderNoteConfigurable.prototype.enable = function(){
                return;
            }
            PreorderNoteConfigurable.prototype.disable = function(){
                return;
            }
        }

        Product.Config.prototype.amOrig_configure = Product.Config.prototype.configure;
        Product.Config.prototype.configure = function(event){
            this.amOrig_configure(event);

            stStatus.onConfigure('', this.settings);
        }

        if(typeof AmConfigurableData == 'undefined') {
            Product.Config.prototype.amOrig_configureElement = Product.Config.prototype.configureElement;
            Product.Config.prototype.configureElement = function (element) {
                this.amOrig_configureElement(element);

                stStatus.onConfigure('', this.settings);
                //Amasty code for Automatically select attributes that have one single value
                if (('undefined' != typeof(amConfAutoSelectAttribute) && amConfAutoSelectAttribute) || ('undefined' != typeof(amStAutoSelectAttribute) && amStAutoSelectAttribute)) {
                    var nextSet = element.nextSetting;
                    if (nextSet && nextSet.options.length == 2 && !nextSet.options[1].selected && element && !element.options[0].selected) {
                        nextSet.options[1].selected = true;
                        this.configureElement(nextSet);
                    }
                }

            }
            Product.Config.prototype.amOrig_configureForValues = Product.Config.prototype.configureForValues;
            Product.Config.prototype.configureForValues = function () {
                this.amOrig_configureForValues();

                //Amasty code for Automatically select attributes that have one single value
                if (('undefined' != typeof(amConfAutoSelectAttribute) && amConfAutoSelectAttribute) || ('undefined' != typeof(amStAutoSelectAttribute) && amStAutoSelectAttribute)) {
                    var select = this.settings[0];
                    if (select && select.options.length == 2 && !select.options[1].selected) {
                        select.options[1].selected = true;
                        this.configureElement(select);
                    }
                }
            }
        }
        else {
            Product.Config.prototype.amOrig_reloadOptionLabels = Product.Config.prototype.reloadOptionLabels;
            Product.Config.prototype.reloadOptionLabels = function (element) {
                this.amOrig_reloadOptionLabels(element);
                //add our function
                stStatus.onConfigure('', this.settings);
            }
        }

        if('undefined' != typeof(changeConfigurableStatus) && changeConfigurableStatus && 'undefined' != typeof(Product.ConfigurableSwatches)) {
            Product.ConfigurableSwatches.prototype.setStockStatus = function (inStock) {
                //disable magento changing stockstatus
                return false;
            }
        }

    }
};




function explode (delimiter, string, limit) 
{
    var emptyArray = { 0: '' };
    
    // third argument is not required
    if ( arguments.length < 2 ||
        typeof arguments[0] == 'undefined' ||
        typeof arguments[1] == 'undefined' )
    {
        return null;
    }
 
    if ( delimiter === '' ||
        delimiter === false ||
        delimiter === null )
    {
        return false;
    }
 
    if ( typeof delimiter == 'function' ||
        typeof delimiter == 'object' ||
        typeof string == 'function' ||
        typeof string == 'object' )
    {
        return emptyArray;
    }
 
    if ( delimiter === true ) {
        delimiter = '1';
    }
    
    if (!limit) {
        return string.toString().split(delimiter.toString());
    } else {
        // support for limit argument
        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;
    }
}

function implode (glue, pieces) {
    var i = '', retVal='', tGlue='';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof(pieces) === 'object') {
        if (pieces instanceof Array) {
            return pieces.join(glue);
        }
        else {
            for (i in pieces) {
                retVal += tGlue + pieces[i];
                tGlue = glue;
            }
            return retVal;
        }
    }
    else {
        return pieces;
    }
}

function strpos (haystack, needle, offset) 
{
    var i = (haystack+'').indexOf(needle, (offset ? offset : 0));
    return i === -1 ? false : i;
}

//Out Of stock notification
function send_alert_email(url, button)
{
    var f = document.createElement('form');
    var productId = button.id.replace(/\D+/g,"");
    var block = button.up('.amxnotif-block');
    if($('amxnotif_guest_email-' + productId)){
        $('amxnotif_guest_email-' + productId).addClassName("validate-email required-entry");
    }
    if(block) {
        block.childElements().each(function (child) {
            f.appendChild( Element.clone(child));
        });
    }

    var validator = new Validation(block);
    if (validator.validate()) {
        f.action = url;
        f.hide();
        $$('body')[0].appendChild(f);
        f.setAttribute("method", 'post');
        f.id = 'am_product_addtocart_form';
        f.submit();
        button.remove();
        return true;
    }
    if($('amxnotif_guest_email-' + productId)){
        $('amxnotif_guest_email-' + productId).removeClassName("validate-email required-entry");
    }
    return false;
}

function checkIt(evt,url, button) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 13) {
        return send_alert_email(url, button);
    }
    return true;
}