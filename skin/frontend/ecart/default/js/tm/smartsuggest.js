var SmartSuggest = {};
SmartSuggest.Slider = Class.create();
SmartSuggest.Slider.prototype = {
    initialize: function(container, controlLeft, controlRight){
        this.animating    = false;
        this.container    = $(container);
        this.content      = $(container).down();
        this.items        = this.content.childElements();
        this.controlLeft  = $(controlLeft);
        this.controlRight = $(controlRight);
        this.initSize();
        this.initControls();
    },

    initSize: function() {
        var width = 0,
            self = this,
            containerWidth = this.container.getWidth();
        this.items.each(function(li) {
            if (li.getWidth() > containerWidth) {
                li.setStyle({
                    width: containerWidth
                        - parseFloat(li.getStyle('padding-left'))
                        - parseFloat(li.getStyle('padding-right'))
                        + 'px'
                });
                self.shift = containerWidth;
            } else {
                self.shift = Math.floor(containerWidth / li.getWidth()) * li.getWidth();
            }
            width += li.getWidth()
                + parseFloat(li.getStyle('margin-left'))
                + parseFloat(li.getStyle('margin-right'));
        });
        this.content.setStyle({
            width: width + 'px'
        });
    },

    initControls: function(){
        this.controlLeft.href = this.controlRight.href = 'javascript:void(0)';
        Event.observe(this.controlLeft,  'click', this.shiftLeft.bind(this));
        Event.observe(this.controlRight, 'click', this.shiftRight.bind(this));

        var lastItem = this.content.childElements().last();
        if ((lastItem.positionedOffset()[0] + lastItem.getWidth()) <= this.container.getWidth()) {
            this.updateControls(0, 0);
        } else {
            this.updateControls(1, 0);
        }
    },

    shiftRight: function(){
        if (this.animating) {
            return;
        }

        var left = isNaN(parseInt(this.content.style.left)) ? 0 : parseInt(this.content.style.left);
        if ((left + this.shift) < 0) {
            var shift = this.shift;
            this.updateControls(1, 1);
        } else {
            var shift = Math.abs(left);
            this.updateControls(1, 0);
        }
        this.moveTo(shift);
    },

    shiftLeft: function(){
        if (this.animating) {
            return;
        }

        var left          = isNaN(parseInt(this.content.style.left)) ? 0 : parseInt(this.content.style.left),
            lastItemLeft  = this.content.childElements().last().positionedOffset()[0],
            lastItemWidth = this.content.childElements().last().getWidth(),
            contentWidth  = lastItemLeft + lastItemWidth;

        if (contentWidth <= this.container.getWidth()) {
            this.updateControls(0, 0);
            return false;
        }

        if ((contentWidth + left - this.shift) > this.container.getWidth()) {
            var shift = this.shift;
            this.updateControls(1, 1);
        } else {
            var shift = contentWidth + left - this.container.getWidth();
            this.updateControls(0, 1);
        }
        this.moveTo(-shift);
    },

    moveTo: function(shift){
        var self = this;
        this.animating = true;
        new Effect.Move(this.content, {
            x          : shift,
            duration   : 0.3,
            delay      : 0,
            afterFinish: function(){
                self.animating = false;
            }
        });
    },

    updateControls: function(left, right){
        if (!left) {
            this.controlLeft.addClassName('disabled');
        } else {
            this.controlLeft.removeClassName('disabled');
        }

        if (!right) {
            this.controlRight.addClassName('disabled');
        } else {
            this.controlRight.removeClassName('disabled');
        }
    }
};
