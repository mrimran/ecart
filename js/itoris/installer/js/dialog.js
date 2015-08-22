
	
	function IDialog(){
		this.content = new Element('div');
		this.buttons = new Element('div');
		this.buttons.addClassName('buttons');
		this._caption = null;
		this._cover = null;
		this._outerWindow = null;
		this._contentWindow = null;
		this.winHeight  = 327;
		this.winWidth = 545;
	}
	
	IDialog.prototype.setCaption = function(caption){
		this._caption.update(caption);
	};
	IDialog.prototype.getCaption = function(){
		return this._caption.innerHTML;
	};
	
	IDialog.prototype.initialize = function(){
		var body = document.getElementsByTagName('body')[0];
		var cover = new Element('div',{'id':'idialog','style':'width:'
			+body.clientWidth+'px;height:'+body.clientHeight+'px;'}).update('&nbsp;');
		body.appendChild(cover);
		cover.style.visibility = 'hidden';
		this._cover = cover;
		
		var winY = this.getWindowOffsets().y + this.getWindowSizes().height / 2 
			- this.winHeight / 2;
		var winX = this.getWindowSizes().width/2 - this.winWidth/2;
		
		var outerWindow = new Element('div', {'style':'position:absolute; top:'
			+winY+'px; left:'+winX+'px; width:'+this.winWidth+'px; height:'+this.winHeight+'px; padding:10px;'});
		outerWindow.addClassName('outer-window');
		this._outerWindow = outerWindow;
		cover.appendChild(outerWindow);
		
		var innerWindow = new Element('div');
		innerWindow.addClassName('idialog-inner');
		outerWindow.appendChild(innerWindow);	
		
		var caption = new Element('div');
		caption.addClassName('caption');
		innerWindow.appendChild(caption);
		this._caption = caption;
		
		var buttonsWindow = new Element('div',{'style':'height:30px;'});
		buttonsWindow.appendChild(this.buttons);
		
		var contentHeight = innerWindow.clientHeight 
			- 20 /*height of the caption*/ 
			- 30 /*height of the buttons */
			-6  	/*  */
			- 10 /*padding of the content window*/; 
		var contentWindow = new Element('div',{'style':'height:'+contentHeight+'px;'});
		contentWindow.addClassName('content-window');
		contentWindow.appendChild(this.content);
		
		innerWindow.appendChild(contentWindow);
		innerWindow.appendChild(buttonsWindow);
		
		this._contentWindow = contentWindow;
	};
	
	IDialog.prototype.setContent = function(content){
		this.content = content;
		
		if (this._contentWindow.hasChildNodes()){
		    while (this._contentWindow.childNodes.length >= 1){
		    	this._contentWindow.removeChild( this._contentWindow.firstChild );
		    }
		}
		
		this._contentWindow.appendChild(content);
	};

	IDialog.prototype.addButton = function(title, onclick, attributes,attachment){
		var button = new Element('button',attributes).update(title);
		button._dialog = this;
		
		if(typeof(attachment) != 'undefined'){
			button._attch = attachment;
		}
		
		button.observe('click',onclick);
		if(title == 'Close'){
			button.addClassName('button-right');
			
		}
		this.buttons.appendChild(button);
	};
	
	IDialog.prototype.clearButtons = function(){
		if (this.buttons.hasChildNodes()){
		    while (this.buttons.childNodes.length >= 1){
		    	this.buttons.removeChild( this.buttons.firstChild );       
		    } 
		}
	};
	
	IDialog.prototype.getWindowSizes = function(){
		var myWidth = 0, myHeight = 0;
		if( typeof( window.innerWidth ) == 'number' ) {
		    //Non-IE
		    myWidth = window.innerWidth;
		    myHeight = window.innerHeight;
		} else{
		    //IE 6+ in 'standards compliant mode'
		    myWidth = document.documentElement.clientWidth;
		    myHeight = document.documentElement.clientHeight;
		}
		var result = {};
		result.width = myWidth;
		result.height = myHeight;
		return result;
	};
	
	IDialog.prototype.getWindowOffsets = function(){
		var scrOfX = 0, scrOfY = 0;
		if( typeof( window.pageYOffset ) == 'number' ) {
		    //Netscape compliant
		    scrOfY = window.pageYOffset;
		    scrOfX = window.pageXOffset;
		} else {
		    //DOM compliant
		    scrOfY = document.body.scrollTop;
		    scrOfX = document.body.scrollLeft;
		} 
		
		var result = {};
		result.x = scrOfX; 
		result.y = scrOfY; 
		return result;
	};

	IDialog.prototype.showModal = function(){
		//this._outerWindow.style.visibility = '';
		this._cover.style.visibility = '';
	};

	IDialog.prototype.hide = function(){
		//this._outerWindow.style.visibility = 'hidden';
		this._cover.style.visibility = 'hidden';
	};