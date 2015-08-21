
	/**
	 * @param {IProductControl} product ProductTo install
	 */
	function IInstallationProcess(product){
		
		this.product = product;
		this.productToInstall = null;
		this.key = null;
		this.tasksList = null;
		this.currentTask = null;
		this.progressbar = null;
		this.files = null;
	}
	
	IInstallationProcess.prototype.start = function(){
		this.stepStart();
	};
	
	IInstallationProcess.prototype.stepStart = function(){
		var product = this.product;
		dialog.setCaption('Installing '+ product.getName());
		
		dialog.clearButtons();
		
		dialog.addButton('Close', function(){
			dialog.hide();
		}, {'class':'scalable'}, this);
		
		dialog.addButton('Next', function(){
			this._attch.stepType();
		}, {'class':'scalable'}, this);
		
		dialog.setContent(new Element('div').addClassName('more').update('<div class="title">Welcome to the IToris extensions Installation Wizard!</div><div style="height:60px;"></div>You are about to install the following extension:  '+this.product.getName()+'. Please do not interrupt the process once it is started.<div style="height:90px;"></div>Press Next to continue...'));
		
		dialog.showModal();
		
	};
	
	IInstallationProcess.prototype.stepType = function(){

		dialog.clearButtons();
		dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
		dialog.addButton('Back', function(){this._attch.stepStart();},{'class':'scalable'},this);
		dialog.addButton('Next', function(){
				if(this._attch.productToInstall == null){
					alert('Please choose the product type');
				}else{
					if(this._attch.productToInstall.isPaid() === true){
						this._attch.stepSerial();
					}else{
						this._attch.stepConfirm();
					}
				}
		}, {'class':'scalable'}, this);
		
		var content = new Element('div').addClassName('more');
		
		var line = this.product.getProductsLine();
		if( line.length == 1 ){
			content.update('<div class="title">There is only one type available for the product at the time</div>');
		}else{
			content.update('<div class="title">Choose product type:</div>');
		}

		for(var i = 0; i < line.length; i++){
			var typeDiv = this._get_typeStep_typeStr(line[i], line.length);
			content.appendChild(typeDiv);
		}
		dialog.setContent(content);
		
	};
	
	/**
	 * 
	 * @param {IProductControl} product
	 * @param {int} count
	 * @returns {void}
	 */
	IInstallationProcess.prototype._get_typeStep_typeStr = function(product, count){
		var radio = null; 
		if(count != 1){
			radio = new Element('input',{'id':'radio_'+product.getType(),'type':'radio','name':'product_type_to_install'});
			if(this.productToInstall != null && this.productToInstall.getType() == product.getType()){
				radio.checked = true;
			}
		}else{
			this.productToInstall = product;
		}
		
		if(product.getType() == 'lite'){
			var msg = new Element('label').update('Lite version');
			var description = new Element('div').update('This version has limited functionality.');
		}
		if(product.getType() == 'trial'){
			var msg = new Element('label').update('Trial version');
			var description = new Element('div').update('This is a 7-day trial version. No functional limitations.');
		}
		if(product.getType() == 'full'){
			if(product.isPaid() === true){
				var msg = new Element('label').update('Full version');
				var description = new Element('div').update('Full version of the product.');
			}else{
				var msg = new Element('label').update('Full version');
				var description = new Element('div').update('Full and free version of the product.');
			}
		}

		if(product.getType() == 'dev'){
			if(product.isPaid() === true){
				var msg = new Element('label').update('Premium version');
				var description = new Element('div').update('Opensource multi-store version of the product.');
			}else{
				var msg = new Element('label').update('Premium version');
				var description = new Element('div').update('Opensource multi-store and free version of the product.');
			}
		}

		if(product.getType() == 'template'){
			if(product.isPaid() === true){
				var msg = new Element('label').update('Full version');
				var description = new Element('div').update('Full version of the theme.');
			}else{
				var msg = new Element('label').update('Full version');
				var description = new Element('div').update('Full and free version of the theme');
			}
		}

		description.style.textIndent = '10px';
		msg.style.marginLeft = '5px';
		msg.htmlFor = 'radio_'+product.getType();
		var div = new Element('div');
		var first = new Element('div');
		
		if(radio != null){
			first.appendChild(radio);
			
			radio._product = product;
			radio._model = this;
			radio.observe('click', function(){
				this._model.productToInstall = this._product;
			});
		}
		
		first.appendChild(msg);
		div.appendChild(first);
		div.appendChild(description);
		div.style.marginTop = '10px';
		return div;
	};
	
	IInstallationProcess.prototype.stepSerial = function(type){
		dialog.clearButtons();
		dialog.addButton('Close', function(){
			dialog.hide();
		}, {'class':'scalable'}, this);
		
		if(type == undefined){
			
			dialog.addButton('Back', function(){
				this._attch.stepType();
			},{'class':'scalable'},this);
			
			dialog.addButton('Next', function(){
				if(this._attch.key == null || this._attch.key == ''){
					alert('Please enter the serial number');
				}else{
					this._attch.stepConfirm();
				}
			},{'class':'scalable'},this);
		}else if(type == 'upgrade'){
			
			dialog.addButton('Back', function(){
				this._attch.stepStartUpgrade();
			},{'class':'scalable'},this);
			
			dialog.addButton('Confirm upgrade', function(){
				if(this._attch.key == null || this._attch.key == ''){
					alert('Please enter the serial number');
				}else{
					this._attch.stepUpgradeProcess();
				}
			},{'class':'scalable'},this);
		}
		
		
		
		var content = new Element('div',{'style':'margin:10px;'}).addClassName('more').update('<div class="title">Checking the registration. </div>');
		
		var information = new Element('div',{'style':'margin-top:50px;margin-bottom:50px;'}).update('You should obtain a serial number to activate the extension. Please visit the <a href="'+this.product.getLink()+'">official page</a> for more details.');
		
		var reginput = new Element('div').update('<div style="height:40px;"></div>Please enter the serial number into the field below: <br/>');
		var input = new Element('input',{'type':'text', 'size':'50'});
		input.addClassName('serial-input input-text');
		
		if(this.key != null){
			input.value = this.key;  
		}
		
		input._model = this;
		input.observe('keyup',function(){
			this._model.key = this.value;
		});
		input.observe('blur',function(){
			this._model.key = this.value;
		});
		reginput.appendChild(input);
		content.appendChild(reginput);
		content.appendChild(information);
		dialog.setContent(content);
	};
	
	IInstallationProcess.prototype.stepConfirm = function(){
		dialog.clearButtons();
		dialog.addButton('Close', function(){
			dialog.hide();
		}, {'class':'scalable'}, this);
		
		dialog.addButton('Back', function(){
			if(this._attch.productToInstall.isPaid() === true){
				this._attch.stepSerial();
			}else{
				this._attch.stepType();
			}
		},{'class':'scalable'},this);
		
		dialog.addButton('Confirm Installation',function(){
			this._attch.fire();
		},{'class':'scallable'},this);
		
		var content = new Element('div').addClassName('more').update('<div class="title">Confirm Installation</div>');
		var inf = new Element('div',{'style':'margin-top:50px;'}).update('Now we are ready to start the installation. Please do not close this page in order to configure the extension properly. If you experience any issues during the installation please contact IToris support.<div style="height:100px;"></div>Press <b>Confirm Installation</b> to continue.</div> ');
		content.appendChild(inf);
		
		dialog.setContent(content);
	};
	
	IInstallationProcess.prototype.fire = function(type){
		this.currentTask = null;
		dialog.clearButtons();
		
		bar = new IProgressBar();
		var content = new Element('div').addClassName('more');
		content.appendChild(bar.getBarDom());
		
		if(type == undefined){
			this.generateTasksList();
		}else if(type == 'update'){
			this.generateUpdateTasksList();
		}else if(type == 'uninstall'){
			this.generateUninstallTasksList();
		}else if(type == 'upgrade'){
			this.generateUpgradeTasksList();
		}
		
		for(var i = 0; i < this.tasksList.length; i++){
			var task = this.tasksList[i];
			content.appendChild(task.getDomView());
		}
		
		dialog.setContent(content);
		bar.set(1);
		this.progressbar = bar;
		
		installation = this;
		window.setTimeout(InstallationManager, 1);
	};
	
	IInstallationProcess.prototype.generateTasksList = function(){
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
		result.push(new ITask('runSelfInstallationScript','Executing self-installation script',this, 2));
		result.push(new ITask('finishingInstallation','Finishing the installation',this, 2));
		this.tasksList = result;
	};
	
	IInstallationProcess.prototype.finished = function(){
		this.progressbar.set(100);
		dialog.addButton('Ok', function(){
			//dialog.hide();
			this.style.display = 'none';
			
			//installer.update();
			if( this._attch.product.getType() == 'template'){
				var _at = 't';
			}else{
				var _at = 'p';
			}
			window.location.href = BASE_URL  + '?at=' + _at;
			
			
		}, {'class':'scalable','style':'float:right;margin-right:0px;'}, this);
	};
	
	function ITask(task, taskName, installation, percents){
		this.task = task;
		this.taskName = taskName;
		this.installation = installation;
		this.percents = percents;
		this.finishStatus = null;
	}
	ITask.prototype.SUCCESS = 'task_succes';
	ITask.prototype.ERROR = 'task_error';
	
	
	ITask.prototype.getDomView = function(){
		var task = new Element('div').addClassName('task');
		var name = new Element('div').addClassName('name').update(this.taskName);
		var error = new Element('div').addClassName('error');
		task.appendChild(name);
		task.appendChild(error);
		this._dom_task = task;
		this._dom_error = error;
		this._dom_name = name;

		return task;
	};
	
	ITask.prototype.setCaption = function(caption){
		this._dom_name.update(caption);
	};
	
	ITask.prototype.execute = function(){
		this._dom_task.addClassName('process');
		eval('this.'+ this.task + '();');
	};
	
	ITask.prototype.finish = function(status){
		this.finishStatus = status;
		this._dom_task.removeClassName('process');
		if(status == this.SUCCESS){
			this._dom_task.addClassName('success');
		}else if(status == this.ERROR){
			this._dom_task.addClassName('fail');
		}
	};
	
	ITask.prototype.register = function(response){
		if(response == undefined){
			installer.sendApiRequest({
				'action'	:	'register',
				'serial'	:	this.installation.key, 
				'pid'		:	this.installation.productToInstall.getPid()
					}, function(response){
							this.register(response);
						}.bind(this)
				);
			
		}else{
			var response = installer.parseXml(response);
			var error = response.getElementsByTagName('error')[0];
			var errorCode = error.getElementsByTagName('code')[0].childNodes.item(0).nodeValue;
			if(errorCode == 0){
				this.installation.progressbar.add(this.percents);
				this.finish(this.SUCCESS);
			}
			else{
				dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
				if(errorCode == 5){
					dialog.addButton('Enter serial number again', function(){
						if( this._attch.type == 'upgrade' )
						{
							this._attch.stepSerial( 'upgrade');
						}
						else
						{
							this._attch.stepSerial();
						}
					},{'class':'scalable'},this.installation);
					
					var msg = error.getElementsByTagName('msg')[0].childNodes.item(0).nodeValue;
					this.setError(msg);
					
					this.finish(this.ERROR);
				}else{
					this.setError('Unknown error ocured. Installation will not continue. Please contact Itoris.');
					this.finish(this.ERROR);
				}
			}
		}
	};
	
	ITask.prototype.checkSystemConfiguration = function(response){

		if (this.installation.productToInstall.getPid() != IProductControl.INSTALLER_PID ) {
			installerProduct = installer.getInstalledProductByPid(IProductControl.INSTALLER_PID);

			if (installerProduct.hasUpdates()) {
				dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
				var installerName = installerProduct.getName();
				this.setError('Please update "' + installerName + '" to the latest version.');
				this.finish(this.ERROR);
				return;
			}
		}

		if(response == undefined){
			installer.sendApiRequest({
				'action'	:	'check_configuration',
				'pid'		:	this.installation.productToInstall.getPid()
			},
			function(response){
				this.checkSystemConfiguration(response);
			}.bind(this)
					);
		}else{
			if(response == 'success'){
				this.installation.progressbar.add(this.percents);
				this.finish(this.SUCCESS);
			}else if(response == installer.REQUEST_ERROR){
				dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
				this.setError('Error. Can not connect to server. Please check your Internet connection and try again.');
				this.finish(this.ERROR);
			}else{
				dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
				this.setError(response);
				this.finish(this.ERROR);
			}
		}
	};
	
	ITask.prototype.getListOfFiles = function(response){
		if(response == undefined){
			installer.sendApiRequest({
				'action'	:	'get_list_of_files',
				'pid'		:	this.installation.productToInstall.getPid()
			},
			function(response){
				this.getListOfFiles(response);
			}.bind(this)
					);
		}else if( response == IInstaller.REQUEST_ERROR){
			this.setError('Please check your Internet connection and try again.');
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}else{
			var response = installer.parseXml(response);
			var error = response.getElementsByTagName('error')[0];
			
			if(error){
				dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
				var errorMsgNode = error.getElementsByTagName('msg')[0];
				if(errorMsgNode != undefined){
					this.setError(errorMsgNode.childNodes.item(0).nodeValue);
					this.finish(this.ERROR);
				}else{
					this.setError(error.getElementsByTagName('code')[0].childNodes.item(0).nodeValue);
					this.finish(this.ERROR);
				}
			}else{
				this.installation.files = response;
				var fileNodes = $A(this.installation.files.getElementsByTagName('file'));
				var filesToCheck = this.compressFiles(fileNodes);
				filesToCheck = filesToCheck.replace(/\//g, ' ');
				this.installation.filesToCheck = filesToCheck;
				this.installation.progressbar.add(this.percents);
				this.finish(this.SUCCESS);
			}
		}
	};
	
	ITask.prototype.compressFiles = function(arrayOfNodes) {

		var fileNodes = arrayOfNodes;
		var filesToCheck = "";
		
		for (var i = 0; i < fileNodes.length; i++) {
			var fileNode = fileNodes[i];
			var namesStack = this.getFoldersStack(fileNode);
			for (var o = 0; o < namesStack.length; o++) {
				filesToCheck += '/' + namesStack[o];
			}
			filesToCheck += "\n";
		}

		var ftc2 = filesToCheck.split("\n");
		for (var i = 0; i < ftc2.length - 2; i++) {
			for (var o = i + 1; o < ftc2.length - 1; o++) {
				if (ftc2[i] > ftc2[o]) {
					var tmp = ftc2[i];
					ftc2[i] = ftc2[o];
					ftc2[o] = tmp;
				}
			}
		}
		var subdir = '';
		for (var i = 0; i < ftc2.length; i++) {
			if (subdir != '') {
				if (ftc2[i].indexOf(subdir) == 0) {
					var tmp = ftc2[i].substr(0, ftc2[i].lastIndexOf('/'));
					ftc2[i] = ftc2[i].replace(subdir, '...');
					subdir = tmp;
				} else {
					subdir = ftc2[i].substr(0, ftc2[i].lastIndexOf('/'));
				}
			} else {
				subdir = ftc2[i].substr(0, ftc2[i].lastIndexOf('/'));
			}
		}
		var filesToCheck = '';
		for (var i = 0; i < ftc2.length; i++) {
			filesToCheck += ftc2[i] + "\n";
		}

		return filesToCheck;
	}

	ITask.prototype.checkDirectoryPermissions = function(response){
		
		if(response == undefined){
			var dirsToCheck = '';
			var filesToCheck = '';
			var n = 0;
			var dirs = this.installation.files.getElementsByTagName('folder');
			for(var i = 0; i < dirs.length; i++){
				var dir = dirs.item(i);
				var mustAdd = false;
				for(var j = 0; j < dir.childNodes.length; j++){
					if(dir.childNodes.item(j).nodeName == 'file'){
						mustAdd = true;
						break;
					}
				}
				if(mustAdd == true){
					var dirName = dir.getAttribute('name');
					var dirPath = '';
					var node = dir;
					while(node.getAttribute('name') != 'root'){
						dirPath = '/' + node.getAttribute('name') + dirPath;
						node = node.parentNode;
					}
					if(dirPath == ''){
						continue;
					}
						
					dirsToCheck += dirPath + "\n"; 
				}
			}
			dirsToCheck += '/var/tmp' + "\n";
			

			//fileNodes = fileNodes.concat(this.installation.filesToDelete);
			var filesToDelete = this.compressFiles(this.installation.filesToDelete);
			filesToDelete = filesToDelete.replace(/\//g, ' ');
			this.installation.filesToDelete = filesToDelete;

			var filesToCheck = this.installation.filesToCheck;

			installer.sendApiRequest({
				'action'	:	'check_directory_permissions',
				'dirs'		:	dirsToCheck.replace(/\//g, ' '),
				'pid'		:	this.installation.productToInstall.getPid(),
				'fls'		:	filesToCheck,
				'files_to_delete' : filesToDelete
			},
			function(response){
				this.checkDirectoryPermissions(response);
			}.bind(this)
					);
			
		}else{
			
			if(response == 'success'){
				this.installation.progressbar.add(this.percents);
				this.finish(this.SUCCESS);
			}else{
				dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
				var error = 'The following directories/files on your host are not writable:<br /><span style="color:#0000aa">'+response+'</span><br/>Installation will not continue. Please contact your system administrator or ISP to set up write permissions';
				this.setError(error);
				this.finish(this.ERROR);
			}
		}
		
	};
	
	ITask.prototype.getFoldersStack = function(node){
		//alert((new XMLSerializer()).serializeToString(node)+' '+node.getAttribute('name'));
		var result = new Array();
		//alert(node.attributes.getNamedItem('name').childNodes.item(0).nodeValue);
		while(node.getAttribute('name') != 'root'){
			result.unshift(node.getAttribute('name'));
			node = node.parentNode;
		}
		return result;
	};
	
	ITask.prototype.getFoldersPath = function(node){
		var stack = this.getFoldersStack(node);
		var result = '';
		for(var i = 0; i < stack.length; i++){
			result += '/' + stack[i];
		}
		return result;
	};
	
	ITask.prototype.findNode = function(root, names){
		var node = root;
		while(names.length > 0){
			var lookFor = names.shift();
			var founded = false;
			for(var i = 0; i < node.childNodes.length; i++){
				var childNode = node.childNodes.item(i);
				if(childNode.getAttribute('name') == lookFor){
					node = childNode;
					founded = true;
					break;
				}
			}
			if(founded === false){
				return null;
			}
		}
		return node;
	};
	
	ITask.prototype.generateListOfFiles = function(response){
		if(response == undefined){
			if(this.installation.type == undefined){
				var params = {
						'action'	:	'get_list_of_installed_files',
						'ftc'		:	this.installation.filesToCheck,
						'pid'		:	this.installation.productToInstall.getPid()
					};
			}else if(this.installation.type == 'upgrade'){
				var params = {
						'action'	:	'get_list_of_installed_files',
						'type'		:	'upgrade',
						'ftc'		:	this.installation.filesToCheck,
						'pid'		:	this.installation.product.getPid(),
						'topid'		:	this.installation.productToInstall.getPid()
					};
			}
			installer.sendApiRequest(params, function(response){
				this.generateListOfFiles(response);
			}.bind(this));
			
		}else if(response == installer.REQUEST_ERROR){
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.setError('Can not connect to the server. Please check your Internet connection and try again.');
			this.finish(this.ERROR);
		}
		else{//alert(response); this.finish(this.ERROR); return;
			var localFiles = installer.parseXml(response);
			var localFilesNodes = localFiles.getElementsByTagName('file');
			
			var filesToDelete = new Array();
			
			//creatin list of useles files
			for(var i = 0; i < localFilesNodes.length; i++){
				var localFileNode = localFilesNodes.item(i);
				var namesStack = this.getFoldersStack(localFileNode);
				var rootDir = this.installation.files.documentElement.childNodes.item(0);
				var fileNode = this.findNode(rootDir, namesStack);
				if(fileNode == null){
					filesToDelete.push(localFileNode);
				}
			}
			
			//create list of files to download
			var fileNodes = this.installation.files.getElementsByTagName('file');
			var filesToDownload = new Array();
			for(var i = 0; i < fileNodes.length; i++){
				var fileNode = fileNodes.item(i);
				var namesStack = this.getFoldersStack(fileNode);
				var rootDir = localFiles.documentElement.childNodes.item(0);
				var localFileNode = this.findNode(rootDir, namesStack);
				if(localFileNode == null){
					filesToDownload.push(fileNode);
				}else{
					var md5local = localFileNode.getAttribute('md5');
					var md5remote = fileNode.getAttribute('md5');
					if ( md5local != md5remote ){
						filesToDownload.push(fileNode);
					} 
				}
			}
			
			this.installation.filesToDelete = filesToDelete;
			this.installation.filesToDownload = filesToDownload;
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}
	};
	
	ITask.prototype.downloadFiles = function(response){
		if(response == undefined){
			var joblist = new Array();
			var f_cnt = this.installation.filesToDownload.length;
			//for(var i = 0; i < this.installation.filesToDownload.length; i++){
			var i = 0;
			var jobID = 0;
			while( i<f_cnt ){
				var job = {};
				job.downloadAttempts = 0;
				job.fileNode = [];
				job.size = 0;
				while( i < f_cnt )
				{
					var file = this.installation.filesToDownload[ i ];
					var file_size = file.getAttribute('size');
					if( job.fileNode.length == 0 || file_size < (150*1024-job.size) )
					{
						job.fileNode.push( this.installation.filesToDownload[i] );
						job.size += file_size - 0 ;
						i++;
					}
					else
					{
						break;
					}
				}
				job.status = 'created';
				job.id = jobID++;
				job.task = this;
				job.msg = '';
				joblist.push(job);
			}
			this.joblist = joblist;
			this.downloadThreds = 0;
			this.progressbarInitialPosition = this.installation.progressbar.get();
			
			installer.sendApiRequest({
				'action'	:	'prepare_downloading',
				'pid'		:	this.installation.productToInstall.getPid()
			}, function(response){
				this.downloadFiles(response);
			}.bind(this));
			
		}else if(response != 'someresponse'){
			
			window.setTimeout(function(){this.downloadFiles('someresponse');}.bind(this),100);
		}
		else{
			
			for(var i = 0; i < this.joblist.length; i++){
				var jobData = this.joblist[i];
				if(jobData.status == 'error' && jobData.downloadAttempts >= 3){
					//var filename = this.getFoldersPath();
					var filenames = '<br/>';
					jobData.fileNode.each(function(item){
						filenames += this.getFoldersPath(item) + '<br/>';
					},this);
					this.setError('Unable to download files: ' + filenames);
					this.finish(this.ERROR);
					dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
					return;
				}
			}
			
			var threadsToStart = 7 - this.downloadThreds;
			
			if(threadsToStart > 0){
				for(var i = 0; i < this.joblist.length; i++){
					var jobData = this.joblist[i];
					if(jobData.status == 'created' || ( jobData.status == 'error' &&  jobData.downloadAttempts < 3)){
						var job = new DownloadJob(i, this);
						job.execute();
						break;
					}
				}
			}
			
			var allSize = 0;
			var downloadedSize = 0;
			for(var i = 0; i < this.joblist.length; i++){
				var jobData = this.joblist[i];
				
				var curSize = jobData.size;//parseInt(jobData.fileNode.attributes.getNamedItem('size').childNodes.item(0).nodeValue ,10);
				allSize += curSize;
				if(jobData.status == 'success'){
					downloadedSize += curSize;
				}
			}
			this.setCaption('Downloading files (' + this.formatSize(downloadedSize) + ' / ' + this.formatSize(allSize) +')' );
			var percentsCompleted = Math.floor(downloadedSize/allSize * this.percents);
			this.installation.progressbar.set(this.progressbarInitialPosition + percentsCompleted);
			
			var isAllSuccessed = true;
			for(var i = 0; i < this.joblist.length; i++){
				var jobData = this.joblist[i];
				if(jobData.status != 'success'){
					isAllSuccessed = false;
					break;
				}				
			}
			
			if(isAllSuccessed == true){
				this.finish(this.SUCCESS);
				return;
			}else{
				window.setTimeout(function(){this.downloadFiles('someresponse');}.bind(this),30);
			}
		}
	};
	
	ITask.prototype.formatSize = function(size){
		if(size > 2048){
			return Math.round(size/1024*100)/100 + ' KB';
		}else{
			return size + ' B';
		}
	};
	
	function DownloadJob(n, task){
		this.n = n; 
		this.task = task;
		this.task.downloadThreds++;
		this.task.joblist[this.n].status = 'started';
	}
	
	DownloadJob.prototype.execute = function(response){
		if(response == undefined){
			var patches = [];
			var md5s = [];
			var p_cnt = this.task.joblist[ this.n ].fileNode.length;
			for( var i=0; i<p_cnt; i++ )
			{
				patches.push( this.task.getFoldersPath(this.task.joblist[ this.n ].fileNode[ i ] ) );
				md5s.push( this.task.joblist[this.n].fileNode[ i ].getAttribute('md5') );
			}
			installer.sendApiRequest({
				'action'	:	'download_file',
				'pid'		:	this.task.installation.productToInstall.getPid(),
				'path'		:	patches.join('|').replace(/\//g, ' '),
				'md5'		:	md5s.join( '|' )
			}, function(response){
				this.execute(response);
			}.bind(this));
			
		}else{
			if(response == 'success'){
				this.task.joblist[this.n].status = 'success';
			}else{
				this.task.joblist[this.n].status = 'error';
				this.task.joblist[this.n].msg = response;
				this.task.joblist[this.n].downloadAttempts++;
			}
			this.task.downloadThreds--;
		}
	};
	
	ITask.prototype.createDirectoryStructure = function(response){
		if (response == undefined){
			installer.sendApiRequest({
				'action'	:	'create_directory_structure',
				'ftc'		:	this.installation.filesToCheck,
				'files_to_delete' : this.installation.filesToDelete,
				'pid'		:	this.installation.productToInstall.getPid()
			},function(response){ this.createDirectoryStructure(response); }.bind(this));
		}else if(response == 'success'){
			this.installation.progressbar.add(this.percents);
			this.finish(this.SUCCESS);
		}else{
			this.setError(response);
			dialog.addButton('Close', function(){dialog.hide();}, {'class':'scalable'}, this);
			this.finish(this.ERROR);
		}
	};
	
	
	ITask.prototype.runSelfInstallationScript = function(response){
		if(response == undefined){
			installer.sendApiRequest({
				'action'	:	'run_self_installation_script',
				'pid'		:	this.installation.productToInstall.getPid()
			}, function(response){
				this.runSelfInstallationScript(response);
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
	
	ITask.prototype.finishingInstallation = function(response){
		if(response == undefined){
			installer.sendApiRequest({
				'action'	:	'finish_installation',
				'pid'		:	this.installation.productToInstall.getPid(),
				'ftc'		:	this.installation.filesToCheck,
				'version'	:	this.installation.productToInstall.getNewVersion(),
				'alias'		: 	this.installation.productToInstall.getAlias()
			}, function(response){
				this.finishingInstallation(response);
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
	
	ITask.prototype.isFinished = function(){
		if(this.finishStatus == null){
			return false;
		}else{
			return true;
		}
	};
	
	ITask.prototype.isSuccessfulyFinished = function(){
		if(this.finishStatus == this.SUCCESS){
			return true;
		}else{
			return false;
		}
	};
	
	ITask.prototype.setError = function(msg){
		if( msg == IInstaller.REQUEST_ERROR || msg.indexOf( IInstaller.REQUEST_ERROR ) > -1 )
		{
			msg = 'Can not connect to the server. Please check your Internet connection and try again.';
		}
		this._dom_error.update(msg);
		installer.sendApiRequest({
			'action'	:	'send_error',
			'pid'		:	this.installation.productToInstall.getPid(),
			'msg'		:	dialog.getCaption() + ' | ' + this.taskName + ' | ' + msg
		}, function(){});
		
	};
	
	var installation = null;
	function InstallationManager(){
		if(installation.currentTask == null){
			installation.currentTask = 0;
			installation.tasksList[installation.currentTask].execute();
			window.setTimeout(InstallationManager, 100);
		}else{
			if(installation.tasksList[installation.currentTask].isFinished() === true){
				if(installation.tasksList[installation.currentTask].isSuccessfulyFinished() === true){
					var currentTask = installation.tasksList[installation.currentTask + 1];
					if(currentTask != null){
						installation.currentTask++;
						installation.tasksList[installation.currentTask].execute();
						window.setTimeout(InstallationManager, 100);
					}else{
						installation.finished();
					}
				}
			}else{
				window.setTimeout(InstallationManager, 100);
			}
		}
	}