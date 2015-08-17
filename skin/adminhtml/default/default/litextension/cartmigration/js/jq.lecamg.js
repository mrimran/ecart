/**
 * @project: CartMigration
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

(function($){
    $.extend({
        LeCaMg : function(options){
            var defaults = {
                url: '',
                formKey: '',
                menuSetup: '#menu-setup',
                menuConfig: '#menu-config',
                menuConfirm: '#menu-confirm',
                formSetupWrap: '#lecamg-setup',
                formConfigWrap: '#lecamg-config',
                formConfirmWrap: '#lecamg-confirm',
                formImportWrap: '#lecamg-import',
                formResumeWrap: '#lecamg-resume',
                formSetup: '#form-setup',
                formConfig: '#form-config',
                formConfirm: '#form-confirm',
                formImport: '#form-import',
                formResume: '#form-resume',
                formSetupId: 'form-setup',
                formConfigId: 'form-config',
                formConfirmId: 'form-confirm',
                formImportId: 'form-import',
                formResumeId: 'form-resume',
                formSetupLoading: '#form-setup-loading',
                formConfigLoading: '#form-config-loading',
                formConfirmLoading: '#form-confirm-loading',
                formImportLoading: '#form-import-loading',
                formResumeLoading: '#form-resume-loading',
                formSetupSubmit: '#form-setup-submit',
                formConfigSubmit: '#form-config-submit',
                formConfirmSubmit: '#form-confirm-submit',
                formImportSubmit: '#form-import-submit',
                formResumeSubmit: '#form-resume-submit',
                formConfigBack: '#form-config-back',
                formConfirmBack: '#form-confirm-back',
                errorMsg: 'Request timeout or server isn\'t responding, please reload the page.',
                msgTryError: '<p class="error">Request timeout or server isn\'t responding, please try again.</p>',
                msgTryWarning: '<p class="warning">Please try again.</p>',
                msgTryImport: '<p class="success"> - Resuming import ...</p>',
                importText: 'Imported',
                errorText: 'Errors',
                processTaxes: '#process-taxes',
                processManufacturers: '#process-manufacturers',
                processCategories: '#process-categories',
                processProducts: '#process-products',
                processCustomers: '#process-customers',
                processOrders: '#process-orders',
                processReviews: '#process-reviews',
                tryImportTaxes: '#try-import-taxes',
                tryImportManufacturers: '#try-import-manufacturers',
                tryImportCategories : '#try-import-categories',
                tryImportProducts : '#try-import-products',
                tryImportCustomers : '#try-import-customers',
                tryImportOrders : '#try-import-orders',
                tryImportReviews: '#try-import-reviews',
                fnResume: 'clearStore',
                timeDelay: 2000,
                autoRetry: 30000
            };
            var settings = $.extend(defaults, options);
            var try_config = 0;
            var try_confirm = 0;
            function enabledMenu(elm) {
                $(elm).addClass('open');
            }

            function disabledMenu(elm) {
                $(elm).removeClass('open');
            }

            function convertFromToData(elm){
                var data = '';
                var element = $(elm);
                if(element.length !== 0){
                    data = element.serialize();
                }
                return data;
            }

            function insertFormKey(data){
                var new_data = data+'&form_key='+settings.formKey;
                return new_data;
            }

            function validateForm(form, form_id){
                var result = true;
                if($(form).length !== 0){
                    var leFormValid = new varienForm(form_id, '');
                    if(leFormValid.validator && leFormValid.validator.validate()){
                        result = true;
                    }else {
                        result = false;
                    }
                }
                return result;
            }

            function resetValidateForm(form, form_id){
                if($(form).length !== 0){
                    var leFormValid = new varienForm(form_id, '');
                    leFormValid.validator.reset();
                }
            }

            function showConsoleLog(msg){
                var element = $('#lecm-import-log .lecm-console-log');
                if(element.length !== 0){
                    element.append(msg);
                    element.animate({scrollTop: element.prop("scrollHeight")});
                }
            }

            function createLecmCookie(value){
                var date = new Date();
                date.setTime(date.getTime()+(24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
                document.cookie = "le_cart_migration_run="+value+expires+"; path=/";
            }

            function getLecmCookie(){
                var nameEQ = "le_cart_migration_run=";
                var ca = document.cookie.split(';');
                for(var i=0;i < ca.length;i++) {
                    var c = ca[i];
                    while (c.charAt(0)===' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }

            function deleteLecmCookie(){
                var date = new Date();
                date.setTime(date.getTime()+(-1*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
                document.cookie = "le_cart_migration_run="+expires+"; path=/";
            }

            function checkLecmCookie(){
                var check = getLecmCookie();
                var result = false;
                if(check === '1'){
                    result = true;
                }
                return result;
            }

            function checkOptionDuplicate(elm){
                var check = new Array();
                $(elm).each(function(index, value){
                    var element = $(value);
                    check[index] = element.val();
                });
                var result = true;
                check.forEach(function(value, index) {
                    check.forEach(function(value_tmp, index_tmp){
                        if(value_tmp === value && index !== index_tmp){
                            result = false;
                        }
                    });
                });
                return result;
            }

            function checkSelectEntity(){
                var result = false;
                var element = $('#input-fields-select');
                if($('input:checkbox:checked', element).length > 0){
                    $('.le-error').fadeOut();
                    result = true;
                }else {
                    $('.le-error').fadeIn();
                }
                return result;
            }

            function checkSelectLangDuplicate(){
                var check = checkOptionDuplicate('#lecm-lang-dup select');
                if(check === true){
                    $('.lecm-error', '#lecm-lang-dup').hide();
                } else{
                    $('.lecm-error', '#lecm-lang-dup').show();
                }
                return check;
            }

            function checkSelectCatDuplicate(){
                var check = checkOptionDuplicate('#lecm-cat-dup select');
                if(check === true){
                    $('.lecm-error', '#lecm-cat-dup').hide();
                } else{
                    $('.lecm-error', '#lecm-cat-dup').show();
                }
                return check;
            }

            function checkSelectAttrDuplicate(){
                var check = checkOptionDuplicate('#lecm-attr-dup select');
                if(check === true){
                    $('.lecm-error', '#lecm-attr-dup').hide();
                } else{
                    $('.lecm-error', '#lecm-attr-dup').show();
                }
                return check;
            }

            function showAllIconSuccess(elm){
                var element = $(elm);
                $('.success-icon', element).css({'display': 'inline-block'});
            }

            function hideAllIconSuccess(elm){
                var element = $(elm);
                $('.success-icon', element).css({'display': 'none'});
            }

            function hideIconSuccessByValid(elm){
                var error = $(elm);
                var icon = error.parent().find('.success-icon');
                icon.css({'display':'none'});
            }

            function checkElementShow(elm){
                var check = $(elm).is(':visible');
                return check;
            }

            function autoRetry(elm){
                if(settings.autoRetry > 0){
                    setTimeout(function(){triggerClick(elm)}, settings.autoRetry);
                }
            }

            function triggerClick(elm){
                var par_elm = elm+' .try-import';
                var check_show = checkElementShow(par_elm);
                var button = $(par_elm).children('div');
                if(check_show){
                    button.trigger('click');
                }
            }

            function showTryAgainImport(elm){
                var element = $(elm).find('.try-import');
                if(element.length > 0){
                    element.show();
                }
                deleteLecmCookie();
            }

            function hideTryAgainImport(elm){
                var element = $(elm).find('.try-import');
                if(element.length > 0){
                    element.hide();
                }
                createLecmCookie(1);
            }

            function showProcessBar(elm, total, imported, error, point){
                var element = $(elm);
                if(element.length > 0){
                    showProcessBarConsole(element, total, imported, error);
                    showProcessBarWidth(element, point);
                }
            }

            function showProcessBarConsole(element, total, imported, error){
                var pbc = element.find('.console-log');
                if(pbc.length !== 0){
                    var html = 'Imported: '+imported+'/'+total+', Errors: '+error;
                    pbc.show();
                    pbc.html(html);
                } else {
                    return false;
                }
            }

            function showProcessBarWidth(element, point){
                var pbw = element.find('.process-bar-width');
                if(pbw.length !== 0 && point !== null){
                    pbw.css({
                        'display' :'block',
                        'width' : point+'%'
                    });
                } else {
                    return false;
                }
            }

            function clearStore(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=clear'),
                    success: function(response, textStatus, jqXHR) {
                        if(response.msg != ''){
                           showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            setTimeout(importCurrencies, settings.timeDelay);
                            $(document).find('#process-clear-data').hide();
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            $('#try-import-not-clear').show();
                        } else if(response.result === 'process'){
                            setTimeout(clearStore, settings.timeDelay);
                        } else {
                            setTimeout(importCurrencies, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        $('#try-import-not-clear').show();
                    }
                });
            }

            function importCurrencies(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=currencies'),
                    success: function(response, textStatus, jqXHR) {
                        setTimeout(importTaxes, settings.timeDelay);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        setTimeout(importTaxes, settings.timeDelay);
                    }
                });
            }

            function importTaxes(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=taxes'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processTaxes);
                            autoRetry(settings.processTaxes);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processTaxes, response.taxes.total, response.taxes.imported, response.taxes.error, response.taxes.point);
                            setTimeout(importManufacturers, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processTaxes);
                            autoRetry(settings.processTaxes);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processTaxes, response.taxes.total, response.taxes.imported, response.taxes.error, response.taxes.point);
                            setTimeout(importTaxes, settings.timeDelay);
                        } else {
                            setTimeout(importManufacturers, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processTaxes);
                        autoRetry(settings.processTaxes);
                    }
                });
            }

            function importManufacturers(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=manufacturers'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processManufacturers);
                            autoRetry(settings.processManufacturers);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processManufacturers, response.manufacturers.total, response.manufacturers.imported, response.manufacturers.error, response.manufacturers.point);
                            setTimeout(importCategories, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processManufacturers);
                            autoRetry(settings.processManufacturers);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processManufacturers, response.manufacturers.total, response.manufacturers.imported, response.manufacturers.error, response.manufacturers.point);
                            setTimeout(importManufacturers, settings.timeDelay);
                        } else {
                            setTimeout(importCategories, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processManufacturers);
                        autoRetry(settings.processManufacturers);
                    }
                });
            }

            function importCategories(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=categories'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processCategories);
                            autoRetry(settings.processCategories);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processCategories, response.categories.total, response.categories.imported, response.categories.error, response.categories.point);
                            setTimeout(importProducts, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processCategories);
                            autoRetry(settings.processCategories);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processCategories, response.categories.total, response.categories.imported, response.categories.error, response.categories.point);
                            setTimeout(importCategories, settings.timeDelay);
                        } else {
                            setTimeout(importProducts, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processCategories);
                        autoRetry(settings.processCategories);
                    }
                });
            }

            function importProducts(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=products'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processProducts);
                            autoRetry(settings.processProducts);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processProducts, response.products.total, response.products.imported, response.products.error, response.products.point);
                            setTimeout(importCustomers, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processProducts);
                            autoRetry(settings.processProducts);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processProducts, response.products.total, response.products.imported, response.products.error, response.products.point);
                            setTimeout(importProducts, settings.timeDelay);
                        } else {
                            setTimeout(importCustomers, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processProducts);
                        autoRetry(settings.processProducts);
                    }
                });
            }

            function importCustomers(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=customers'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processCustomers);
                            autoRetry(settings.processCustomers);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processCustomers, response.customers.total, response.customers.imported, response.customers.error, response.customers.point);
                            setTimeout(importOrders, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processCustomers);
                            autoRetry(settings.processCustomers);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processCustomers, response.customers.total, response.customers.imported, response.customers.error, response.customers.point);
                            setTimeout(importCustomers, settings.timeDelay);
                        } else {
                            setTimeout(importOrders, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processCustomers);
                        autoRetry(settings.processCustomers);
                    }
                });
            }

            function importOrders(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=orders'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processOrders);
                            autoRetry(settings.processOrders);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processOrders, response.orders.total, response.orders.imported, response.orders.error, response.orders.point);
                            setTimeout(importReviews, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processOrders);
                            autoRetry(settings.processOrders);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processOrders, response.orders.total, response.orders.imported, response.orders.error, response.orders.point);
                            setTimeout(importOrders, settings.timeDelay);
                        } else {
                            setTimeout(importReviews, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processOrders);
                        autoRetry(settings.processOrders);
                    }
                });
            }

            function importReviews(){
                createLecmCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=reviews'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processReviews);
                            autoRetry(settings.processReviews);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processReviews, response.reviews.total, response.reviews.imported, response.reviews.error, response.reviews.point);
                            $(settings.formImportSubmit).show();
                            deleteLecmCookie();
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processReviews);
                            autoRetry(settings.processReviews);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processReviews, response.reviews.total, response.reviews.imported, response.reviews.error, response.reviews.point);
                            setTimeout(importReviews, settings.timeDelay);
                        } else {
                            $(settings.formImportSubmit).show();
                            deleteLecmCookie();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processReviews);
                        autoRetry(settings.processReviews);
                    }
                });
            }

            return run();

            function run(){
                deleteLecmCookie();
                $(document).on('click', settings.formSetupSubmit, function(){
                    $(settings.formResumeWrap).hide();
                    $('#error-cart , #error-url, #error-token').hide();
                    hideIconSuccessByValid(settings.formSetup);
                    if(validateForm(settings.formSetup, settings.formSetupId) === true){
                        $(settings.formSetupLoading).show();
                        resetValidateForm(settings.formSetup, settings.formSetupId);
                        var data = convertFromToData(settings.formSetup);
                        data = insertFormKey(data);
                        $.ajax({
                            url: settings.url,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            success: function(response, textStatus, jqXHR) {
                                $(settings.formSetupLoading).hide();
                                if(response.result === 'success'){
                                    showAllIconSuccess(settings.formSetup);
                                    enabledMenu(settings.menuConfig);
                                    $(settings.formConfigWrap).html(response.html);
                                    $(settings.formSetupWrap).hide();
                                    $(settings.formConfigWrap).show();
                                } else if(response.result === 'warning'){
                                    if(try_config < 2){
                                        try_config++;
                                        $(settings.formSetupSubmit).trigger('click');
                                        return false;
                                    }
                                    if(response.msg !== ''){
                                        $(response.elm).html(response.msg);
                                    }
                                    $(response.elm).show();
                                    hideIconSuccessByValid(response.elm);
                                } else{
                                    if(try_config < 2){
                                        try_config++;
                                        $(settings.formSetupSubmit).trigger('click');
                                        return false;
                                    }
                                    alert(response.msg);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                if(try_config < 2){
                                    try_config++;
                                    $(settings.formSetupSubmit).trigger('click');
                                    return false;
                                }
                                $(settings.formSetupLoading).hide();
                                alert("Request timeout or server isn\'t responding, please try again.");
                            }
                        });
                    }
                });

                $(document).on('click', settings.formConfigBack, function(){
                    $(settings.formSetupWrap).show();
                    $(settings.formConfigWrap).hide();
                    disabledMenu(settings.menuConfig);
                });

                $(document).on('click', settings.formConfigSubmit, function(){
                    if(validateForm(settings.formConfig, settings.formConfigId) === true
                        && checkSelectCatDuplicate() === true
                        && checkSelectAttrDuplicate() === true
                        && checkSelectLangDuplicate() === true
                        && checkSelectEntity() === true){
                        resetValidateForm(settings.formConfig, settings.formConfigId);
                        $(settings.formConfigLoading).show();
                        var data = convertFromToData(settings.formConfig);
                        data = insertFormKey(data);
                        $.ajax({
                            url: settings.url,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            success: function(response, textStatus, jqXHR) {
                                $(settings.formConfigLoading).hide();
                                if(response.result === 'success'){
                                    enabledMenu(settings.menuConfirm);
                                    $(settings.formConfirmWrap).html(response.html);
                                    $(settings.formConfigWrap).hide();
                                    $(settings.formConfirmWrap).show();
                                } else {
                                    if(try_confirm < 2){
                                        try_confirm++;
                                        $(settings.formSetupSubmit).trigger('click');
                                        return false;
                                    }
                                    alert(response.msg);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                if(try_confirm < 2){
                                    try_confirm++;
                                    $(settings.formSetupSubmit).trigger('click');
                                    return false;
                                }
                                $(settings.formConfigLoading).hide();
                                alert(settings.errorMsg);
                            }
                        });
                    }
                });

                $(document).on('click', '#select-all', function(){
                    $('#input-fields-select input:checkbox').prop('checked',this.checked);
                });

                $(document).on('click', '.lv1', function(){
                    var _this = $(this);
                    if (_this.prop('checked') === false) {
                        _this.parents('.lv0').find('.lv2').prop('checked', false);
                    }
                });

                $(document).on('click', '.lv2', function() {
                    var _this = $(this);
                    if (_this.prop('checked') === true) {
                        _this.parents('.lv0').find('.lv1').prop('checked', true);
                    }
                });

                $(document).on('click', '.le-select-checkbox', function(){
                    var _this = $(this);
                    _this.parent().children('input').trigger('click');
                });

                $(document).on('click', settings.formConfirmSubmit, function(){
                    $(settings.formConfirmLoading).show();
                    var data = convertFromToData(settings.formConfirm);
                    data = insertFormKey(data);
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response, textStatus, jqXHR) {
                            $(settings.formConfirmLoading).hide();
                            if(response.result === 'success'){
                                $(settings.formImportWrap).html(response.html);
                                $(settings.formConfirmWrap).hide();
                                $(settings.formImportWrap).show();
                                createLecmCookie(1);
                                setTimeout(clearStore, settings.timeDelay);
                            } else {
                                alert(response.msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $(settings.formConfirmLoading).hide();
                            alert(settings.errorMsg);
                        }
                    });
                });

                $(document).on('click', settings.formConfirmBack, function(){
                    disabledMenu(settings.menuConfirm);
                    $(settings.formConfirmWrap).hide();
                    $(settings.formConfigWrap).show();
                });

                $(document).on('click', settings.formResumeSubmit, function(){
                    $(settings.formResumeLoading).show();
                    var data = convertFromToData(settings.formResume);
                    data = insertFormKey(data);
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response, textStatus, jqXHR) {
                            $(settings.formResumeLoading).hide();
                            if(response.result === 'success'){
                                $(settings.formResumeWrap).hide();
                                $(settings.formSetupWrap).hide();
                                $(settings.formImportWrap).html(response.html);
                                $(settings.formImportWrap).show();
                                setTimeout(eval(settings.fnResume), settings.timeDelay);
                                createLecmCookie(1);
                            } else {
                                alert(response.msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $(settings.formResumeLoading).hide();
                            alert(settings.errorMsg);
                        }
                    });
                });

                $(document).on('click', settings.tryImportCategories, function(){
                    importCategories();
                    hideTryAgainImport(settings.processCategories);
                });

                $(document).on('click', settings.tryImportProducts, function(){
                    importProducts();
                    hideTryAgainImport(settings.processProducts);
                });

                $(document).on('click', settings.tryImportCustomers, function(){
                    importCustomers();
                    hideTryAgainImport(settings.processCustomers);
                });

                $(document).on('click', settings.tryImportOrders, function(){
                    importOrders();
                    hideTryAgainImport(settings.processOrders);
                });

                $(document).on('click', settings.tryImportTaxes, function(){
                    importTaxes();
                    hideTryAgainImport(settings.processTaxes);
                });

                $(document).on('click', settings.tryImportManufacturers, function(){
                    importManufacturers();
                    hideTryAgainImport(settings.processManufacturers);
                });

                $(document).on('click', settings.tryImportReviews, function(){
                    importReviews();
                    hideTryAgainImport(settings.processReviews);
                });

                $(document).on('click', '#choose-seo', function(){
                    $('#seo_plugin').slideToggle();
                });

                $(document).on('click', '#try-import-not-clear', function(){
                    createLecmCookie(1);
                    $(this).hide();
                    setTimeout(clearStore, settings.timeDelay);
                });

                $(window).on('beforeunload', function(){
                    var check = checkLecmCookie();
                    if(check === true){
                        return "Migration is in progress, leaving current page will stop it! Are you sure want to stop?";
                    }
                });

                $(document).on('click', settings.formImportSubmit, function(){
                    $(document).find(settings.formImportLoading).css({display: 'block'});
                    var data = convertFromToData(settings.formImport);
                    data = insertFormKey(data);
                    var _this = $(this);
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response, textStatus, jqXHR) {
                            $(document).find(settings.formImportLoading).css({display: 'none'});
                            _this.hide();
                            showConsoleLog(response.msg);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $(document).find(settings.formImportLoading).css({display: 'none'});
                            _this.hide();
                            showConsoleLog(settings.msgTryError);
                        }
                    });
                });

            }
        }
    });
})(jle);