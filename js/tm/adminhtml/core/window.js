TmcoreWindow = Class.create();
TmcoreWindow.prototype = {

    current           : $H({}),
    blockWindow       : null,
    blockTitle        : null,
    blockContent      : null,
    blockMask         : null,
    windowHeight      : null,
    confirmedCurrentId: null,

    initialize: function() {
        this._initWindowElements();
    },

    _initWindowElements: function() {
        this.blockWindow  = $('tmcore_popup');
        this.blockTitle   = $('tmcore_popup_title');
        this.blockContent = $('tmcore_popup_content');
        this.blockMask    = $('popup-window-mask');
        this.windowHeight = $('html-body').getHeight();
    },

    onCloseBtn: function() {
        this.hide();
        return this;
    },

    update: function(content, title) {
        this.blockContent.update(content);
        if (title) {
            this.blockTitle.update(title);
        }
        return this;
    },

    show: function() {
        toggleSelectsUnderBlock(this.blockMask, false);
        this.blockMask.setStyle({'height':this.windowHeight+'px'}).show();
        this.blockWindow.setStyle({'marginTop':-this.blockWindow.getHeight()/2 + "px", 'display':'block'});
    },

    hide: function() {
        toggleSelectsUnderBlock(this.blockMask, true);
        this.blockMask.style.display = 'none';
        this.blockWindow.style.display = 'none';
    },
}

Event.observe(window, 'load',  function() {
    tmcoreWindow = new TmcoreWindow();
});
