
	/*UPDATE PROCESS CODE*/
	IInstallationProcess.prototype.startUpdate = function(){
		this.productToInstall = this.product;
		this.stepStartUpdate();
	};
	
	IInstallationProcess.prototype.stepStartUpdate = function(){
		var product = this.product;
		dialog.setCaption('Updating '+ product.getName());
		
		dialog.clearButtons();
		
		dialog.addButton('Close', function(){
			dialog.hide();
		}, {'class':'scalable'}, this);
		
		dialog.addButton('Start update', function(){
			this._attch.stepUpdateProcess();
		}, {'class':'scalable'}, this);
		
		dialog.setContent(new Element('div').addClassName('more').update('<div class="title">Welcome to the IToris extensions Installation Wizard!</div><div style="height:60px;"></div>You are about to update the following extension: '+this.product.getName()+'. Please do not interrupt the process once it is started.<div style="height:90px;"></div> Please press <b>Start update</b> to continue.'));
		dialog.showModal();
	};
	
	IInstallationProcess.prototype.stepUpdateProcess = function(){
		this.fire('update');
	};
	
	IInstallationProcess.prototype.generateUpdateTasksList = function(){
		var result = new Array();
		result.push(new ITask('checkSystemConfiguration','Checking the system configuration',this, 3));
		result.push(new ITask('getListOfFiles','Getting list of files',this, 3));
		result.push(new ITask('generateListOfFiles','Generating list of files to download',this, 3));
		result.push(new ITask('checkDirectoryPermissions','Checking directory permissions',this, 2));
		result.push(new ITask('downloadFiles','Downloading files',this, 80));
		result.push(new ITask('createDirectoryStructure','Creating directory structure',this, 2));
		result.push(new ITask('finishUpdating','Finishing the update',this, 2));
		this.tasksList = result;
	};
	
	ITask.prototype.finishUpdating = function(response){
		if(response == undefined){
			installer.sendApiRequest({
				'action'	:	'finish_update',
				'pid'		:	this.installation.productToInstall.getPid(),
				'version'	:	this.installation.productToInstall.getNewVersion(),
				'ftc' 		:	this.installation.filesToCheck
			}, function(response){
				this.finishUpdating(response);
			}.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	
	
	/*UNINSTALLATION PROCESS CODE*/
	IInstallationProcess.prototype.startUninstall = function(){
		this.productToInstall = this.product;
		this.stepStartUninstall();
	};
	
	
	IInstallationProcess.prototype.stepStartUninstall = function(){
		var product = this.product;
		dialog.setCaption('Uninstalling '+ product.getName());
		
		dialog.clearButtons();
		
		dialog.addButton('Close', function(){
			dialog.hide();
		}, {'class':'scalable'}, this);
		
		dialog.addButton('Start uninstall', function(){
			this._attch.stepUninstallWhy();
		}, {'class':'scalable'}, this);
		
		dialog.setContent(new Element('div').addClassName('more').update('<div class="title">Welcome to the IToris extensions Installation Wizard!</div><div style="height:60px;"></div>You are about to uninstall the following extensions: '+this.product.getName()+'. Please do not interrupt the process once it is started.<div style="height:90px;"></div> Press <b>Start uninstall</b> to continue.'));
		dialog.showModal();
	};
	
	IInstallationProcess.prototype.stepUninstallWhy = function(){
		if(this.skipUninstallationReason == undefined){
			this.skipUninstallationReason = false;
		}
		if(this.uninstallationReason == undefined){
			this.uninstallationReason = '';
		}
		dialog.clearButtons();
		
		dialog.addButton('Prev',function(){
			this._attch.stepStartUninstall();
		}, {'class':'scalable'}, this);
		
		dialog.addButton('Uninstall',function(){
			if(this._attch.skipUninstallationReason === true 
					|| (this._attch.skipUninstallationReason === false 
							&& this._attch.uninstallationReason !== undefined
							&& this._attch.uninstallationReason !== ''))
			{
				this._attch.stepUninstallationProcess();
			}else{
				alert('We would really appreciate it if you describe the reason of removing. It will help us to make the extension better.');
			}
		}, {'class':'scalable'}, this);
		
		dialog.addButton('Close', function(){
			dialog.hide();
		}, {'class':'scalable'}, this);
		
		var divTextArea = new Element('div').addClassName('comment');
		divTextArea.appendChild(new Element('div').update('We would really appreciate it if you describe the reason of removing. It will help us to make the extension better.'));
		var textArea = new Element('textarea');
		textArea.addClassName('uninstal-reason');
		textArea._model = this;
		textArea.value = this.uninstallationReason;
		textArea.disabled = this.skipUninstallationReason;
		if(textArea.disabled === true){
			textArea.addClassName('disabled');
		}

		textArea.observe('keyup', function(){
			this._model.uninstallationReason = this.value;
		});
		divTextArea.appendChild(textArea);
		
		var divCheckBox = new Element('div').addClassName('skip_comment');
		var checkBox = new Element('input', {'type':'checkbox', 'id':'skip_reason'});
		divCheckBox.appendChild(checkBox);
		divCheckBox.appendChild(new Element('label', {'for':'skip_reason'}).update('Skip the comment'));
		checkBox._model = this;
		checkBox._textArea = textArea;
		checkBox.checked = this.skipUninstallationReason;
		checkBox.observe('click',function(){
			this._model.skipUninstallationReason = this.checked;
			this._textArea.disabled =  this.checked;
			if(this.checked === true){
				this._textArea.addClassName('disabled');
			}else{
				this._textArea.removeClassName('disabled');
			}
		});
		
		var div = new Element('div').addClassName('more');
		div.appendChild(divTextArea);
		div.appendChild(divCheckBox);
		
		dialog.setContent(div);
	};
	
	IInstallationProcess.prototype.stepUninstallationProcess = function(){
		this.fire('uninstall');
	};
	
	IInstallationProcess.prototype.generateUninstallTasksList = function(){
		var result = new Array();
		result.push(new ITask('preparingUninstallation', 'Preparing to uninstall',this,20));
		result.push(new ITask('runSelfUninstallationScript', 'Run self-uninstallation script',this,20));
		result.push(new ITask('removeUninstallationFiles','Removing files',this, 40));
		result.push(new ITask('finishUninstallation','Finishing the un-installation',this, 20));
		this.tasksList = result;
	};
	
	ITask.prototype.preparingUninstallation = function(response){
		if (response == undefined){
			installer.sendApiRequest({
				'action'	:	'prepare_uninstallation',
				'pid'		: 	this.installation.productToInstall.getPid()
			},function(response){
				this.preparingUninstallation(response);
			}.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	
	ITask.prototype.runSelfUninstallationScript = function(response){
		if (response == undefined){
			installer.sendApiRequest({
				'action'	:	'run_self_uninstallation_script',
				'pid'		: 	this.installation.productToInstall.getPid()
			},function(response){
				this.runSelfUninstallationScript(response);
			}.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	
	ITask.prototype.removeUninstallationFiles = function(response){
		if (response == undefined){
			installer.sendApiRequest({
				'action'	:	'remove_files',
				'pid'		: 	this.installation.productToInstall.getPid()
			},function(response){
				this.removeUninstallationFiles(response);
			}.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	ITask.prototype.finishUninstallation = function(response){
		if (response == undefined){
			installer.sendApiRequest({
				'action'	:	'finish_uninstallation',
				'pid'		: 	this.installation.productToInstall.getPid(),
				'reason'	: 	this.installation.uninstallationReason
			},function(response){
				this.finishUninstallation(response);
			}.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	
	
	/*UPGRADE PROCESS CODE*/
	/**
	 * @param {IProductControl} to ProductTo install
	 */
	IInstallationProcess.prototype.startUpgrade = function(to){
		this.productToInstall = to;
		this.type = 'upgrade';
		this.stepStartUpgrade();
	};
	
	/**
	 * @returns {IDialog}
	 */
	IInstallationProcess.prototype.getDialog = function(){
		return dialog;
	};
	
	IInstallationProcess.prototype.stepStartUpgrade = function(){
		this.getDialog().clearButtons();
		this.getDialog().setCaption('Upgrading Extension');
		this.getDialog().addButton('Close', function(){
			this._attch.getDialog().hide();
		}, {'class':'scalable'}, this);
		
		if(this.productToInstall.isPaid() === true){
			this.getDialog().addButton('Next', function(){
				this._attch.stepSerial('upgrade');
			}, {'class':'scalable'}, this);
		}else{
			this.getDialog().addButton('Upgrade', function(){
				this._attch.stepUpgradeProcess();
			}, {'class':'scalable'}, this);
		}
		
		dialog.setContent(new Element('div').addClassName('more').update('<div class="title"> Welcome to the IToris extensions Installation Wizard!</div><div style="height:60px;"></div> You are about to upgrade '+this.product.getName()+' to the full version. Please do not interrupt the process once it is started<div style="height:90px;"></div> Press <b>Start upgrade</b> to continue.'));
		this.getDialog().showModal();
	};
	
	IInstallationProcess.prototype.stepUpgradeProcess = function(){
		this.fire('upgrade');
	};
	
	IInstallationProcess.prototype.generateUpgradeTasksList = function(){
		var result = new Array();
		if(this.productToInstall.isPaid() === true){
			result.push(new ITask('register','Registering the product',this, 3));
		}
		result.push(new ITask('checkSystemConfiguration','Checking the system configuration',this, 3));
		result.push(new ITask('getListOfFiles','Getting list of files',this, 3));
		result.push(new ITask('generateListOfFiles','Generating list of files to download',this, 3));
		result.push(new ITask('checkDirectoryPermissions','Checking directory permissions',this, 2));
		result.push(new ITask('downloadFiles','Downloading files',this, 80));
		result.push(new ITask('createDirectoryStructure','Creating directory structure',this, 2));
		result.push(new ITask('finishUpgrading','Finishing the upgrade process',this, 2));
		this.tasksList = result;
	};
	
	ITask.prototype.finishUpgrading = function(response){
		if (response == undefined){
			installer.sendApiRequest({
				'action'	:	'finish_upgrading',
				'frompid'		: 	this.installation.product.getPid(),
				'topid'		: 	this.installation.productToInstall.getPid(),
				'ftc'		:	this.installation.filesToCheck,
				'version'	: 	this.installation.productToInstall.getNewVersion(),
				'alias'		:	this.installation.productToInstall.getAlias()
			},function(response){
				this.finishUpgrading(response);
			}.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	
	var UPDATE_LOADED = true;