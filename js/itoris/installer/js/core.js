
	function IInstaller(){
		this.method = null;
		this.installedProducts = null;
		this.avaliableProducts = null;
	}
	
	IInstaller.prototype.METHOD_PROXY = 'proxy';
	IInstaller.prototype.METHOD_AJAX = 'ajax';
	
	IInstaller.prototype.showLoading = function(){
		$('loading-mask').removeClassName('hide');
		$('loading-mask').addClassName('show');
		$('loading-mask').style.display = '';
	};
	
	IInstaller.prototype.hideLoading = function(){
		$('loading-mask').removeClassName('show');
		$('loading-mask').addClassName('hide');
	};
	
	IInstaller.prototype.initialize = function(){
		
		installer.showLoading();
		this.findMethod();
		this.installedProducts = _installedProducts;

		if(_avaliableProducts != null){
			installer.avaliableProducts = installer.parseXml(_avaliableProducts);
		}else{
			this.getAvaliableProducts();
		}
		
		window.dialog = new IDialog();
		dialog.initialize();
		
		this._init_dataLoadingWait();
	};
	
	IInstaller.prototype.update = function(){
		installer.showLoading();
		installer.installedProducts = null;
		this.getInstalledProducts();
		this._init_dataLoadingWait();
	};
	
	function isset(variable){
		return (typeof(window[variable]) == 'undefined') ? false : true;
	}
	
	IInstaller.prototype._init_dataLoadingWait = function(){
		if(installer.avaliableProducts == null 
				|| installer.installedProducts == null)
		{
			this.showLoading();
			window.setTimeout(function(){
				installer._init_dataLoadingWait();
			},100);
		}else{
			this.prepareExtensions();
			this.buildUI();
			installer.hideLoading();
		}
	};
	
	IInstaller.prototype.prepareExtensions = function(){
		var installedProducts = {};
		installedProducts.count = 0;
		var installedTemplates = {};
		installedTemplates.count = 0;
		
		var avaliableProducts = {};
		avaliableProducts.count = 0;
		var avaliableTemplates = {};
		avaliableTemplates.count = 0;		

		var pids = this.avaliableProducts.getElementsByTagName('pid');
		for(var i = 0; i < pids.length; i++){
			var pid = pids[i].childNodes.item(0).nodeValue; 
			var product = new IProductControl(pid);
			if(product.isInstalled() === true){
				if( product.isTemplate() === false){
					installedProducts[installedProducts.count] = product;
					installedProducts.count++;
				}else{
					installedTemplates[installedTemplates.count] = product;
					installedTemplates.count++;
				}
			}else{
				if( product.hasInstalledBrother() == false ){
					if( product.isTemplate() === false){
						
						var mustBreak = false;
						for(var j = 0; j < avaliableProducts.count; j++){
							if(avaliableProducts[j].getAlias() == product.getAlias()){
								mustBreak = true;
								break;
							}
						}
						if(mustBreak === true){
							continue;
						}
						
						avaliableProducts[avaliableProducts.count] = product;
						avaliableProducts.count++;
						
					}else{
						avaliableTemplates[avaliableTemplates.count] = product;
						avaliableTemplates.count++;
					}
				}
			}
		}
		
		this.cInstalledProducts = installedProducts;
		this.cInstalledTemplates = installedTemplates;
		this.cAvaliableProducts = avaliableProducts;
		this.cAvaliableTemplates = avaliableTemplates;
	};
	
	IInstaller.prototype.getInstalledExtenstionData = function(pid){
		for(var i = 0; i < this.installedProducts.size(); i++){
			var prod = this.installedProducts[i];
			if(prod.pid == pid) {
				return prod;
			}
		}
		return null;
	};

	IInstaller.prototype.getInstalledProductByPid = function(pid) {
		for(var i = 0; i < installer.cInstalledProducts.count; i++){
			var product = installer.cInstalledProducts[i];
			if (product.getPid() == pid) {
				return product;
			}
		}

		return null;
	};

	IInstaller.prototype.getInstalledProducts = function(response){
		if ( response == undefined ){
			this.sendUserRequest('getInstalledProducts', this.getInstalledProducts, {});
		}else{
			installer.installedProducts = eval('['+response+']')[0];
		}
	};
	
	IInstaller.prototype.getAvaliableProducts = function(response){
		if( installer.method == null) {
			window.setTimeout(function(){
				installer.getAvaliableProducts();
			},100); 
		}else{
			if (response == undefined){
				var params = {'action':'get_products'};
				this.sendApiRequest(params, this.getAvaliableProducts);
			}else{
				installer.avaliableProducts = installer.parseXml(response);
			}
		}	
	};
	
	IInstaller.prototype.findMethod = function(response){
		installer.method = IInstaller.prototype.METHOD_PROXY; 
	};
	
	IInstaller.REQUEST_ERROR = "LSDKFJD(Fsfdksjdklfsd0f9j2lkfjdslfU)(SDUlkdfsd";
	
	IInstaller.prototype.sendRequest = function(url, callback, params){
		//if(typeof(installer.requestkeys) == "undefined"){
		//	installer.requestkeys = new Array();
		//	installer.nextRequestKey = 101011;
		//}
		
		//installer.requestkeys.push('' + installer.nextRequestKey);
		//params.respkey = '' + installer.nextRequestKey;
		//installer.nextRequestKey++;
		
		
		var call = new Ajax.Request(url,{method:'post',
			parameters : params,
			onSuccess:function(transport){
				var response = transport.responseText;
				if(response == 'success'){
					callback('success');
					return;
				}
				if(response.lastIndexOf('success', 0) >= 0){
					var respkey = response.substr(8);
					var founded = false;
					for(var i = 0; i < installer.requestkeys.length; i++){
						var key = installer.requestkeys[i];
						if(key == '') continue;
						if(key == respkey){
							installer.requestkeys[i] = '';
							founded = true;
							callback('success');
							break;
						}
					}
					if(founded == false){
						callback('Error. Cached request.');
					}
				}else{
					callback(response);
				}
			},
			onFailure: function(){
				callback(IInstaller.REQUEST_ERROR);
			}
		});
	};
	
	IInstaller.prototype.sendUserRequest = function(task,callback,params){
		params.task = task;
		params.formkey = FORM_KEY;
		this.sendRequest(INSTALLER_CALL_URL, callback, params);
	};
	
	IInstaller.prototype.sendApiRequest = function(params, callback){
		if( this.method == this.METHOD_PROXY ){
			params.task = 'apiRequest';
			params.formkey = FORM_KEY;
			this.sendRequest(INSTALLER_CALL_URL, callback, params);
		}else if( this.method == this.METHOD_AJAX){
			callback(IInstaller.REQUEST_ERROR);
		}else{
			alert('Cannot connect to the update server. Please check your Internet connection and refresh the page.');
		}
	};
	
	IInstaller.prototype.parseXml = function (str){
		var xmlDoc = null;
		if(window.DOMParser){
			var parser = new DOMParser();
			xmlDoc = parser.parseFromString(str,'text/xml');
		}else{
			xmlDoc = new ActiveXObject('Microsoft.XMLDOM');
			xmlDoc.async = 'false';
			xmlDoc.loadXML(str);
		}
		return xmlDoc;
	};
	
	IInstaller.prototype.getProductData = function(pid, field){
		var els = this.avaliableProducts.getElementsByTagName('pid');
		var product_data = null;
		for(var i = 0; i < els.length; i++){
			var el = els[i];
			if(el.childNodes.item(0).nodeValue == pid){
				pidel = el;
				product_data = el.parentNode;
			}
		}
		
		if(product_data == null) return null;
		var fields = product_data.getElementsByTagName(field);
		if(fields.length != 1) return null;
		
		return fields[0].childNodes.item(0).nodeValue;
		
	};
	
	IInstaller.prototype.buildUI = function(){
		var builder = new IUIBuilder();
		builder.updateLeftSide();
		builder.updateMainContent();
	};

	Event.observe(window,'load',function(){
		if($('installer_container')){
			window.installer = new IInstaller();
			window.installer.initialize();
		}
	});
	
	