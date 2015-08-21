
	function IProgressBar(){
		this.bar = null;
		this.inner = null;
		this.init();
		this.position = 0;
	}
	
	IProgressBar.prototype.init = function(){
		this.bar = new Element('div');
		this.bar.addClassName('progressbar');
		this.outer = new Element('div');
		this.outer.addClassName('toddler-outer');
		this.inner = new Element('div');
		this.inner.addClassName('toddler');
		this.bar.appendChild(this.outer);
		this.outer.appendChild(this.inner);
	};
	
	IProgressBar.prototype.getBarDom = function(){
		return this.bar;
	};
	
	IProgressBar.prototype.set = function(percent){
		this.position = percent;
		this.inner.style.width = this.position + '%';
	};
	
	IProgressBar.prototype.get = function(){
		return parseInt(this.inner.style.width.substring(0, this.inner.style.width.length - 1));
	};
	
	IProgressBar.prototype.add = function(percent){
		this.position += percent;
		this.inner.style.width = this.position + '%';
	};