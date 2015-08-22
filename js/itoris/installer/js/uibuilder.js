	
	function IUIBuilder(){
		
	}
	
	IUIBuilder.prototype.updateLeftSide = function(){
		var maincol = $$('#installer_container .logotabs');
		if(maincol.size() > 0){
			return;
			$('installer_container').removeChild(maincol[0]);
		}
		var mainDiv = new Element('div', {'id':'page:left' });
		mainDiv.addClassName('logotabs');
		
		var header = new Element('div');
		header.addClassName('ilogo');
		mainDiv.appendChild(header);
		
		var ul = new Element('ul');
		mainDiv.appendChild(ul);
		var active = '';
		if(_at == 'p'){
			active = ' active ';
		}
		var li = new Element('li').update('<a class="tab-item-link '+active+'" onclick="activateTab(this,\'products\');"><span>IToris Products</span></a>');
		ul.appendChild(li);
		var active = '';
		if(_at == 't'){
			active = ' active ';
		}
		var li = new Element('li').update('<a class="tab-item-link '+active+'  " onclick="activateTab(this,\'templates\');"><span>IToris Templates</span></a>');
		ul.appendChild(li);
		$('installer_container').appendChild(mainDiv);
		return true;
	};
	
	IUIBuilder.prototype.updateMainContent = function(){
		var maincol = $$('#installer_container .imain');
		if(maincol.size() > 0){
			$('installer_container').removeChild(maincol[0]);
		}
		var maindiv = new Element('div', {'id':'page:main'});
		maindiv.addClassName('imain');
		
		maindiv.appendChild(this.getProductsTab());
		maindiv.appendChild(this.getTemplatesTab());
		$('installer_container').appendChild(maindiv);
		
		setSizeAll();
		
		return true;
	};
	
	IUIBuilder.prototype.__getTabPart = function(title, collection, renderType ){
		
		if(collection.count > 0){
			var divResult = new Element('div');
			var divTabPartHeader = new Element('div').update(title);
			divTabPartHeader.addClassName('iheader ' + renderType);
			var divTabPart = new Element('div');
			divTabPart.addClassName('product-line ' + renderType);
			
			divResult.appendChild(divTabPartHeader);
			divResult.appendChild(divTabPart);
			
			var table = new Element('table',{'cellpadding':'0', 'cellspacing':'0'});
			divTabPart.appendChild(table);
			var tbody = new Element('tbody');
			table.appendChild(tbody);
			var tr = new Element('tr');
			tbody.appendChild(tr);
			
			var divTabPart = new Element('td');
			tr.appendChild(divTabPart);
			var table = new Element('table',{'width':'100%'}).update('<tr><td>&nbsp;</td></tr>');
			divResult.appendChild(table);
			
			for(var i = 0; i < collection.count; i++){
				var product = collection[i];	
				eval('divTabPart.appendChild(product.renderAs'+renderType+'Product())');
			}
			
			return divResult;
		}else{
			return null;
		}
	};
	
	IUIBuilder.prototype.getProductsTab = function(){
		var divProductsTab = new Element('div', {'id':'tab_products'});
		if(_at == 't'){
			divProductsTab.style.display = 'none';
		}
		divProductsTab.addClassName('tab');
		
		var installedTabPart = this.__getTabPart('Installed Products', installer.cInstalledProducts, 'Installed');
		var moreItorisProductsCaption = 'IToris Products';
		if(installedTabPart != null){
			divProductsTab.appendChild(installedTabPart);
			moreItorisProductsCaption = 'More IToris Products';
		}
		var avaliableTabPart = this.__getTabPart(moreItorisProductsCaption, installer.cAvaliableProducts, 'Avaliable');
		if(avaliableTabPart != null)
			divProductsTab.appendChild(avaliableTabPart);
		
						
		return divProductsTab;
	};
	
	IUIBuilder.prototype.getTemplatesTab = function(){
		var divTemplatesTab = new Element('div', {'id':'tab_templates'});
		if(_at == 'p'){
			divTemplatesTab.style.display = 'none';
		}
		divTemplatesTab.addClassName('tab');
		
		var installedTabPart = this.__getTabPart('Installed Templates', installer.cInstalledTemplates, 'Installed');
		var moreItorisTemplatesCaption = 'IToris Templates';
		if(installedTabPart != null){
			divTemplatesTab.appendChild(installedTabPart);
			moreItorisTemplatesCaption = 'More IToris Templates';
		}
		var avaliableTabPart = this.__getTabPart(moreItorisTemplatesCaption, installer.cAvaliableTemplates, 'Avaliable');
		if(avaliableTabPart != null)
			divTemplatesTab.appendChild(avaliableTabPart);
		
		return divTemplatesTab;
	};
	
	function activateTab(el, tab){
		$$('.tab-item-link').each(function(name,index){
			this[index].removeClassName('active');
		},$$('.tab-item-link'));
		el.addClassName('active');
		
		$$('.tab').each(function(name,index){
			this[index].style.display = 'none';
		},$$('.tab'));
		
		if($('tab_'+tab)){
			$('tab_'+tab).style.display = '';
		}
		setSizeAll();
	}
	
	function setSizeAll(){
		var collections = new Array(installer.cInstalledProducts, installer.cAvaliableProducts,installer.cInstalledTemplates,installer.cAvaliableTemplates );
		
		for(var j = 0; j < collections.length; j++){
			var collection = collections[j];
			for(var i = 0; i < collection.count; i++){
				var product = collection[i];
				product.setSizes();
			}
		}
	}