
	function IRefresher(){
		this.items = new Array();
		window.setTimeout(function(){this.process();}.bind(this),100);
	}
	
	IRefresher.prototype.add = function(id, params, responseProcessor){
		var item = {	'id':id, 
						'params':params, 
						'responseProcessor' : responseProcessor, 
						'response':null, 
						'requestMade' : false,
						'finished' : false
					};
		this.items.push(item);
	};
	
	IRefresher.prototype.process = function(){
		for(var i = 0; i < this.items.length; i++){
			var item = this.items[i];
			if(item.finished) continue;
			
			if(!item.requestMade){
				var t = new IRefreshTask(this,i);
				installer.sendApiRequest(item.params, function(response){this.refresh(response);}.bind(t));
				item.requestMade = true;
				var el = document.getElementById(item.id);
				if(el){
					var span = new Element('img',{'style':'margin:0px 5px;',
						'src' : INSTALLER_BASE_URL + 'js/itoris/installer/img/loading2.gif'});
					el.innerHTML = '';//update('');
					el.appendChild(span);
				}
			}
			
			if(item.response == null){
			}else{
				item.finished = true;
			}
		}
		window.setTimeout(function(){this.process();}.bind(this),100);
	};
	
	function IRefreshTask(refresher,n){
		this.refresher = refresher;
		this.n = n;
	}
	
	IRefreshTask.prototype.refresh = function(response){
		if(response == undefined){
			response = this.refresher.items[this.n].response;
		}else{
			this.refresher.items[this.n].response = response;
		}
		var string = this.refresher.items[this.n].responseProcessor(response);
		//var string = response;
		var el = document.getElementById(this.refresher.items[this.n].id);
		if(el){
			el.style.display = '';
			el.innerHTML = string;
			//el.update(string);
			
		}else{
			window.setTimeout(function(){this.refresh();}.bind(this), 100);
		}
		
	};
	
	var refresher = new IRefresher();