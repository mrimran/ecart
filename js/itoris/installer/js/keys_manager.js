
	function IKeysManager(product){
		this._product = product;
		this._showAddKeyForm = false;
		this._pid = null;
		this._host = null;
		this._serial = null;
		this._defaultHosts = [];
	}
	
	IKeysManager.prototype.show = function(){
		this._updateButtons();
		this._getDialog().setCaption('Manage Licenses of ' + this._getProduct().getName());
		this._updateContent();
		this._getDialog().showModal();
	};
	
	IKeysManager.prototype.close = function(){
		this._getDialog().hide();
	};
	
	/**
	 * @returns {IDialog} diaglog
	 */
	IKeysManager.prototype._getDialog = function(){
		return dialog;
	};
	
	/**
	 * @returns {IProductControl} product
	 */
	IKeysManager.prototype._getProduct = function(){
		return this._product;
	};
	
	IKeysManager.prototype._updateContent = function(){
		this._getDialog().setContent(this._getContent());
	};
	
	IKeysManager.prototype._getContent = function(){
		var sites = this._getProduct().getHosts();
		var content = '';
		content += '<div class="description-caption">Activated hosts:</div>';
		content += '<div class="section-content host-list"><table>';
		sites.each(function(item){
			content += '<tr>';
			content += '<td class="host"><span class="half-show">http[s]://[www.]</span>' + item + ' </td>';
			//content += '<td> activated </td>';
			content += '</tr>';
		});
		content += '</table></div>';
		
		if(this._showAddKeyForm === true){
			content += '<div class="description-caption">Activate license key:</div>';
			content += '<div class="section-content key-form"><table>';
			content += '<tr class="key">';
			content += '<td>Serial number: </td><td><input type="text" name="key_form_serial" id="key_form_serial" size="60" />'
				+'<br/><span class="hint">XXXX-XXXX-XXXX-XXXX-XXXX</span>'
				+'</td>';
			content += '</tr>';
		
			content += '<tr>';
			content += '<td>Host: </td><td><select name="key_form_host" id="key_form_host">';
			for (var i = 0; i < IKeysManager._defaultHosts.length; i++) {
				content += '<option value="' + i + '">'+ IKeysManager._defaultHosts[i] +'</option>';
			}
			content += '</select>';
				//+'<input type="text"  name="key_form_host" id="key_form_host" size="60" />'
				//+'<br/><span class="hint">http[s]://[www.][subdomain.]example.com/[subfolder/]</span>';
				+'</td>';
			content += '</tr>';
			content += '</table></div>';
		}
		
		return (new Element('div', {'class':'details'})).update(content);
	};
	
	IKeysManager.prototype._updateButtons = function(){
		this._getDialog().clearButtons();
		this._getDialog().addButton('Close', function(){
			this._attch.close();
		}, {}, this);
		
		if(this._showAddKeyForm === false){
			this._getDialog().addButton('Add', function(){
				this._attch._showAddKeyForm = true;
				this._attch._updateContent();
				this._attch._updateButtons();
			}, {}, this);
		}else{
			this._getDialog().addButton('Activate', function(){
				var errors = new Array();
				var error = IKeysManager.validateSerial($('key_form_serial').value);
				if(error !== true){
					errors.push(error);
				}
				var host = IKeysManager._defaultHosts[$('key_form_host').value];
				var error = IKeysManager.validateHost(host);
				if(error !== true){
					errors.push(error);
				}
				if(errors.length > 0){
					alert(errors.join("\n"));
					return false;
				}else{
					this._attch._host = IKeysManager.getHost(host);
					this._attch._serial = IKeysManager.getSerial($('key_form_serial').value);
					this._attch._pid = this._attch._getProduct().getPid();
					this._attch.register();
				}
			}, {'id' : 'activate_button'}, this);
			
			this._getDialog().addButton('Cancel', function(){
				this._attch._showAddKeyForm = false;
				this._attch._updateContent();
				this._attch._updateButtons();
			}, {}, this);
		}
	};
	
	IKeysManager.prototype.register = function(response){
		if(response == undefined){
			installer.showLoading();
			installer.sendApiRequest({
				'action'	:	'register',
				'serial'	:	this._serial, 
				'pid'		:	this._pid,
				'host'		:	this._host
					}, function(response){
							this.register(response);
						}.bind(this)
				);
			$('activate_button').disabled = true;
			$('activate_button').addClassName('disabled');
		}else{
			installer.hideLoading();
			$('activate_button').disabled = false;
			$('activate_button').removeClassName('disabled');
			var response = installer.parseXml(response);
			var error = response.getElementsByTagName('error')[0];
			var errorCode = error.getElementsByTagName('code')[0].childNodes.item(0).nodeValue;
			if(errorCode == 0){
				this._getProduct().addHost(this._host);
				this._showAddKeyForm = false;
				this._updateContent();
				this._updateButtons();
				alert('Your key has been successfully registered.');
			}else{
				if(errorCode == 5){
					var msg = error.getElementsByTagName('msg')[0].childNodes.item(0).nodeValue;
					alert(msg);
				}else{
					alert('Unknown error. Please try again or contact IToris.');
				}
			}
		}
	};
	
	IKeysManager.validateSerial = function(serial){
		if(serial == ''){
			return 'Please enter serial.';
		}
		if(IKeysManager.getSerial(serial) === false){
			return 'Please enter valid serial number.';	
		}
		
		return true;
	};
	
	IKeysManager.validateHost = function(host){
		if(host == ''){
			return 'Please enter host.';
		}
		if(IKeysManager.getHost(host) === false){
			return 'Please enter valid host.';
		}
		return true;
	};
	
	IKeysManager.getHost = function(host){
		var regexp = /^[\s]*(?:https*:\/\/)?(?:www\.)?([a-z0-9][\-a-z0-9]*(?:\.[-a-z0-9]+)*(?:\/[-_a-z0-9\.\~]+)*)[\/]?[\s]*$/i;
		if(!regexp.test(host)){
			return false;
		}else{
			var match = regexp.exec(host);
			return match[1];
		}
	};
	
	IKeysManager.getSerial = function(serial){
		var regexp = /^[\s]*([a-z0-9]{4}(?:-[a-z0-9]{4}){4})[\s]*$/i;
		if(regexp.test(serial)){
			var match = regexp.exec(serial);
			return match[1];
		}else{
			return false;
		}
	};