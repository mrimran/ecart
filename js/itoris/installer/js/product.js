	
	function IProductControl(pid){
		this.pid = pid;
		this.version = '';
		this.type = '';
		this._isInstalled = false;
		this.name = '';
		this.upgradePid = null;
		this.newVersion = null;
		this.alias = '';
		this.iconLink = '';
		this.link = '';
		this.description = '';
		this.ispaid = '';
		this.trialInformation = null;
		this.hosts = null;
		
		this.load();
	}

	IProductControl.INSTALLER_PID = 1;
	
	IProductControl.prototype.getLink = function(){
		return this.link;
	};
	
	IProductControl.prototype.load = function(){
		var idata = installer.getInstalledExtenstionData(this.pid);
		if(idata != null){
			this.version = idata.version;
			this._isInstalled = true;
			this.hosts = idata.hosts;
		}
		
		this.type = installer.getProductData(this.pid, 'type');
		this.name = installer.getProductData(this.pid, 'name');
		this.newVersion = installer.getProductData(this.pid, 'version');
		this.alias = installer.getProductData(this.pid, 'alias');
		this.iconLink = installer.getProductData(this.pid, 'icon_link');
		this.link = installer.getProductData(this.pid, 'link');
		this.description = installer.getProductData(this.pid,'description');
		this.ispaid = installer.getProductData(this.pid, 'is_paid');
		
		//finding if this product can be upgraded
		if(this.type == 'full' || this.type == 'dev'){
			this.upgradePid = null;
		}else{
			var alias = installer.getProductData(this.pid, 'alias');
			var aliases = installer.avaliableProducts.getElementsByTagName('alias');
			for(var i = 0; i < aliases.length; i++){
				var elAlias = aliases[i];
				if(elAlias.childNodes.item(0).nodeValue == alias){
					var elProduct = elAlias.parentNode;
					var elPid = elProduct.getElementsByTagName('pid')[0].childNodes.item(0).nodeValue;
					var type = installer.getProductData(elPid, 'type');
					if(type == 'full'){
						this.upgradePid = elPid;
					}
				}
			}
		}
	};
	
	IProductControl.prototype.hasUpdates = function(){
		return this.getVersion() !== this.newVersion;
	};
	
	IProductControl.prototype.hasUpgrades = function(){
		return this.upgradePid !== null;
	};
	
	IProductControl.prototype.getType = function(){
		return this.type;
	};
	
	IProductControl.prototype.isTemplate = function(){
		return this.getType() == 'template' ? true : false;
	};
	
	IProductControl.prototype.isInstalled = function(){
		return this._isInstalled;
		
	};
	
	IProductControl.prototype.getAlias = function(){
		return this.alias;
	};
	
	IProductControl.prototype.isPaid = function(){
		return this.ispaid == 'true';
	};
	
	IProductControl.prototype.getPid = function(){
		return this.pid;
	};
	
	IProductControl.prototype.getHosts = function(){
		return this.hosts;
	};
	
	IProductControl.prototype.addHost = function(host){
		this.hosts.push(host);
	};
	
	IProductControl.prototype.hasInstalledBrother = function(){
		var alias = installer.getProductData(this.pid, 'alias');
		
		var aliases = installer.avaliableProducts.getElementsByTagName('alias');
		for(var i = 0; i < aliases.length; i++){
			var elAlias = aliases[i];
			if(elAlias.childNodes.item(0).nodeValue == alias){
				
				var pid = elAlias.parentNode.getElementsByTagName('pid')[0].childNodes.item(0).nodeValue;
				if(pid == this.pid){
					continue;
				}
				
				for(var j = 0; j < installer.installedProducts.size(); j++){
					var prodData = installer.installedProducts[j];
					if(prodData.pid == pid){
						return true;
					}
				}
			}
		}
		return false;
	};
	
	IProductControl.prototype.getName = function(){
		return this.name;
	};
	
	IProductControl.prototype.getVersion = function(){
		return this.version;
	};
	
	IProductControl.prototype.getVersionString = function(){
		
		var result = this.getVersion() + ' ';
		if(this.getType() != 'template'){
			result += this.getType();
		}
		if(this.getType() == 'trial'){
			
			if(this.trialInformation === null){
				result +=' <span id="trial_'+this.getPid()+'">&nbsp;</span> ';
				refresher.add('trial_'+this.getPid(), {
					'action' : 'get_trial',
					'pid'	: this.getPid()
				},this.trialResponseProcess.bind(this));
			}else{
				result += ' <span id="trial_'+this.getPid()+'"> ' + this.getTrialInformationString() + ' </span>';
			}
		}
		return result;
	};
	
	IProductControl.prototype.trialResponseProcess = function(response){
		this.trialInformation = response;
		return this.getTrialInformationString();
	};
	
	IProductControl.prototype.getTrialInformationString = function(){
		if(this.trialInformation === null){
			return '';
		}
		
		if(this.trialInformation == 'error'){
			return '(connection error)';
		}
		
		var days = 0; 
		var doc = installer.parseXml(this.trialInformation);
		var days = doc.getElementsByTagName('daysleft')[0].childNodes.item(0).nodeValue;

		if(days <= 0){
			return '(trial has expired)';
		}else{
			return '('+days+' days left) ';
		}
		
	};

	IProductControl.prototype.propogationStop = function(){
		this._stopPropogation = true;
	};

	IProductControl.prototype.propogationReset = function(){
		if(this._stopPropogation == true ){
			this._stopPropogation = false;
			return true;
		}else{
			return false;
		}
	};
	
	IProductControl.prototype.renderAsInstalledProduct = function(){
		
		var product = new Element('div',{'style':'','title':'Click for details'});
		product.addClassName('product');
		
		product._model = this;
		product.observe('click',function(){
			if(this._model.propogationReset()){
				return;
			}
			this._model.showMore();
		});
		
		var ptable1 = new Element('table',{'cellpadding':'0', 'cellspacing':'0'});
		var ptable = new Element('tbody');
		ptable1.appendChild(ptable);
		product.appendChild(ptable1);
		
		var ptr = new Element('tr');
		ptable.appendChild(ptr);

		//product icon appending
		var ptd_ico = new Element('td',{'style':'vertical-align:middle;width:1px;'});
		ptr.appendChild(ptd_ico);
		
		var div_ico = new Element('img', {'src':this.iconLink, width:'150px', height:'150px'});
		div_ico.addClassName('product-icon installed');
		ptd_ico.appendChild(div_ico);

		//information appending
		informationCell = new Element('td');
		ptr.appendChild(informationCell);
		
		var divTitle = new Element('div').update(this.getName());
		divTitle.addClassName('title');
		informationCell.appendChild(divTitle);
		
		if(this.isInstalled() === true){
			var version = new Element('div').update(this.getVersionString());
			version.addClassName('version');
			informationCell.appendChild(version);
		}

		var linkContent = '';
		if(this.newVersion != this.version){
			linkContent = '<span class="new-version-msg">New version is available</span>';
		}else{
			linkContent = 'Official page';
		}
		
		var offPageLink = new Element('a',{ 'href' : this.link, 'target':'_blank','title':'Go to the official page'}).update(linkContent);
		offPageLink.addClassName('official-page-link');
		offPageLink._model = this;
		offPageLink.observe('click',function(){
			this._model.propogationStop();
		});
		var offPageLinkWrapper = new Element('div');
		offPageLinkWrapper.addClassName('official-page-link-wrapper');
		offPageLinkWrapper.appendChild(offPageLink);
		informationCell.appendChild(offPageLinkWrapper);

		//top control buttons
		var topButtonsCell = new Element('td',{'style':'white-space:nowrap;'});
		ptr.appendChild(topButtonsCell);
		
		var topButtonsCellDiv = new Element('div');
		topButtonsCellDiv.addClassName('top-buttons');
		topButtonsCell.appendChild(topButtonsCellDiv);
		

		if(this.isPaid() === true){
			var div = this._getTopButton('Licence Keys Manager', 'licenses', function(){
				this._model.propogationStop();
				this._model.openKeysManager();
			});
			topButtonsCellDiv.appendChild(div);
		}
		
		if(this.hasUpgrades() === true){
			var div = this._getTopButton('Upgrade to full version', 'upgrade', function(ev){
				this._model.propogationStop();
				this._model.upgrade();
			});
			topButtonsCellDiv.appendChild(div);
		}
		
		if(this.hasUpdates() === true){
			var div = this._getTopButton('Update to '+ this.newVersion+ ' version', 'update', function(){
				this._model.propogationStop();
				this._model.update();
			});
			topButtonsCellDiv.appendChild(div);
		}
		
		if(this.getAlias() != 'installer'){
			var div = this._getTopButton('Uninstall', 'uninstall', function(){
				this._model.propogationStop();
				this._model.uninstall();
			});
			topButtonsCellDiv.appendChild(div);
		}
		
		return product;
	};
	
	IProductControl.prototype._getTopButton = function(title, className, onClick){
		var div = new Element('div',{'title' : title});
		div.addClassName('top-button ' + className);
		div._model = this;
		div.observe('click', onClick);
		return div;
	};
	
	IProductControl.prototype.renderAsInstalledTemplate = function(){
		return this.renderAsInstalledProduct();
	};
	
	IProductControl.prototype.renderAsAvaliableProduct = function(){
		
		var pdiv = new Element('div',{'title':'Click for details'});
		pdiv.addClassName('product');
		
		pdiv._model = this;
		pdiv.observe('click',function(){
			if(this._model.propogationReset()){
				return;
			}
			this._model.showMore();
		});
		
		var ptable1 = new Element('table',{'cellpadding':'0','cellspacing':'0'});
		pdiv.appendChild(ptable1);
		
		var ptable = new Element('tbody');
		ptable1.appendChild(ptable);
		
		var ptr = new Element('tr');
		ptable.appendChild(ptr);
		
		var ptd = new Element('td');
		ptr.appendChild(ptd);
		
		
		var vtable = new Element('table',{'style':'width:100%;','cellpadding':'0','cellspacing':'0'});
		ptd.appendChild(vtable);
		var vtbody = new Element('tbody');
		vtable.appendChild(vtbody);
		
		var vtr = new Element('tr');
		vtbody.appendChild(vtr);
		var vtd = new Element('td',{'style':'width:1px;'});
		vtd.appendChild(new Element('img', {'src':this.iconLink, width:'150px', height:'150px',
			'style':'margin:2px;vertical-align:middle;'}));
		vtr.appendChild(vtd);
		
		
		var vtd = new Element('td');
		vtr.appendChild(vtd);
		
		
		var divTitle = new Element('div',{'style':'margin-left:3px;'}).update(this.getName());
		divTitle.addClassName('title');
		vtd.appendChild(divTitle);
		
		var linkContent = 'Official page';
		var a = new Element('a',{ 'href' : this.link, 'target':'_blank','title':'Go to the official page'});
		a.addClassName('official-page-link');
		a._model = this;
		a.observe('click',function(ev){
			this._model.propogationStop();
		});
		a.update(linkContent);
		var offLink = new Element('div',{'style':'margin-left:3px;'});
		offLink.addClassName('official-page-link-wrapper');
		offLink.appendChild(a);
		vtd.appendChild(offLink);
		
		
		var vtd = new Element('td',{'style':'width:1px;'});
		vtr.appendChild(vtd);
		
		
		
		var install = new Element('button',{'title':'Install'}).update('Install');
		install.addClassName('scalable ');
		install._model = this;
		install.observe('click',function(){
			this._model.propogationStop();
			this._model.install();
		});
		var divInstall = new Element('div',{'style':'width:55px;padding-right:4px;'});
		divInstall.addClassName('top-buttons');
		divInstall.appendChild(install);
		vtd.appendChild(divInstall);
		
		
		//description
		var trDesc = new Element('tr');
		
		ptable.appendChild(trDesc);
		var tdDesc = new Element('td');
		trDesc.appendChild(tdDesc);
		var divDescCapt = new Element('div').update('Description:');
		this._resize_descriptionCaption = divDescCapt;
		divDescCapt.addClassName('description-caption');
		tdDesc.appendChild(divDescCapt);
		
		var divDesc = new Element('div',{'style':'height:170px; width:300px; overflow-y:auto;'}).update(this.description);
		this._resize_description = divDesc;
		divDesc.addClassName('description');
		tdDesc.appendChild(divDesc);
		
		return pdiv;
	};
	
	IProductControl.prototype.setSizes = function(){
		if(this.isInstalled() === false){
			var width = this._resize_descriptionCaption.clientWidth;
			if(width == 0){
				width = this._resize_descriptionCaption.offsetWidth;
			}
			if(width == 0) return;


			if(!this._resize_description.style.width){alert("1");}
			this._resize_description.style.width = width + 'px';
			//this._designfix_install.width='59px';
			//alert(this.getName());
		}
	};
	
	IProductControl.prototype.getProductsLine = function(){
		var alias = this.alias;
		var result = new Array();
		var aliases = installer.avaliableProducts.getElementsByTagName('alias');
		for(var i = 0; i < aliases.length; i++){
			var elAlias = aliases[i];
			if(alias == elAlias.childNodes.item(0).nodeValue){
				var product = elAlias.parentNode;
				var pid = product.getElementsByTagName('pid')[0].childNodes.item(0).nodeValue;
				var elProduct = new IProductControl(pid);
				result.push(elProduct);
			}
		}
		return result;
	};
	
	IProductControl.prototype.install = function(){
		var installationProcess = new IInstallationProcess(this);
		installationProcess.start();
	};
	
	IProductControl.prototype.renderAsAvaliableTemplate = function(){
		return this.renderAsAvaliableProduct();
	};
	
	IProductControl.prototype.uninstall = function(){
		var installationProcess = new IInstallationProcess(this);
		installationProcess.startUninstall();
	};
	
	IProductControl.prototype.showMore = function(){
		dialog.clearButtons();
		
		
		dialog.addButton('Close',function(){
			this._dialog.hide();
		},{'class':'scalable'});
		dialog.setCaption(this.name+' details');
		
		if(this.isInstalled() == true){
			if(this.hasUpgrades() == true){
				dialog.addButton('Upgrade',function(){
					this._attch.upgrade();
				},{'class':'scalable'},this);
			}
			
			if(this.hasUpdates() == true){
				dialog.addButton('Update',function(){
					this._attch.update();
				},{'class':'scalable'},this);
			}
			if(this.getAlias() != 'installer'){
				dialog.addButton('Uninstall',function(){
					this._attch.uninstall();
				},{'class':'scalable'},this);
			}
			
		}else{
			dialog.addButton('Install',function(){
				this._attch.install();
			},{'class':'scalable'},this);
		}
		
		
		

		
		var content = new Element('div');
		content.addClassName('details');
		var table = new Element('table',{'style':'width:100%;'});
		table.addClassName('details-table');
		content.appendChild(table);
		var tbody = new Element('tbody');
		table.appendChild(tbody);
		table = tbody;
		
		var tr = new Element('tr');
		table.appendChild(tr);

		var td = new Element('td');
		tr.appendChild(td);

		
		
		var vtable = new Element('table',{'style':'width:100%;'});
		td.appendChild(vtable);
		var vtbody = new Element('tbody');
		vtable.appendChild(vtbody);
		
		var vtr = new Element('tr');
		vtbody.appendChild(vtr);
		
		var vtd = new Element('td',{'style':'width:1px;'}).update('<img  src="'+this.iconLink+'" />');
		vtr.appendChild(vtd);
		
		var vtd = new Element('td');
		
		var offlinkVersionDiv = new Element('div',{'style':'margin-left:3px;'});
		offlinkVersionDiv.addClassName('info-part');
		var title = new Element('div').update(this.getName());
		title.addClassName('title');
		offlinkVersionDiv.appendChild(title);
		
		
		td.appendChild(offlinkVersionDiv);
		
		var offLink = new Element('div').update('<a href="'+this.link+'">Go to the official page</a>');
		offLink.addClassName('off-link');
		offlinkVersionDiv.appendChild(offLink);
		
		if(this.isInstalled() == true){
			var version = new Element('div').update(this.getVersionString() + ' version installed');
			version.addClassName('version');
			offlinkVersionDiv.appendChild(version);
		}

		
		
		vtd.appendChild(offlinkVersionDiv);
		vtr.appendChild(vtd);
		/****/
		
		//details row
		tr = new Element('tr');
		td = new Element('td');
		var description = new Element('div').update(this.description);
		description.addClassName('moredescription');
		var descriptionCaption = new Element('div').update('Description:');
		descriptionCaption.addClassName('description-caption');
		td.appendChild(descriptionCaption);
		td.appendChild(description);
		
		tr.appendChild(td);
		table.appendChild(tr);
		
		dialog.setContent(content);
		dialog.showModal();
		
	};
	
	IProductControl.prototype.update = function(){
		var installationProcess = new IInstallationProcess(this);
		installationProcess.startUpdate();
	};
	
	IProductControl.prototype.upgrade = function(){
		var installationProcess = new IInstallationProcess(this);
		installationProcess.startUpgrade(new IProductControl(this.upgradePid));
	};
	
	IProductControl.prototype.getNewVersion = function(){
		return this.newVersion;
	};
	
	IProductControl.prototype.openKeysManager = function(){
		var manager = new IKeysManager(this);
		manager.show();
	};