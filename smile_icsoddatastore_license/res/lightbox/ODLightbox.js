
(function( $ ) {

ODLightbox = function() {
    var self = this;
	// internal options
    this._options = {};
    var lightbox = this._lightbox = {
        width : 400,
        height : 400,
        initialized : false,
        init : function() {
            if ( lightbox.initialized ) {
                return;
            }
            lightbox.initialized = true;
			
			$('body').append('<div class="od-lightbox-overlay"></div>');
			$('body').append('<div class="od-lightbox-box"></div>');
			
			$('body > .od-lightbox-box').append('<div class="od-lightbox-shadow"></div>');
			$('body > .od-lightbox-box').append('<div class="od-lightbox-content"></div>');
			$('body > .od-lightbox-box').append('<div class="od-lightbox-close">x</div>');
			
			$( 'body > .od-lightbox-box > .od-lightbox-close').bind( 'click', lightbox.hide );
        },

        hide: function() {
			$('body > .od-lightbox-box > .od-lightbox-content').html('');
            $('body > .od-lightbox-box').hide();
            $('body > .od-lightbox-overlay').hide();
        },
        show: function() {
			var contentHtml = self._options.contentHtml;
			var content = '';
			if (contentHtml)
				content = contentHtml;
			$('body > .od-lightbox-box > .od-lightbox-content').html(content);
            $('body > .od-lightbox-overlay').show().css( 'visibility', 'visible' );
            $('body > .od-lightbox-box').show();
			lightbox.resize();
			
        },
		resize: function() {
			// calculate
             var width = Math.min( $( window ).width()-40, lightbox.width ),
                height = Math.min( $( window ).height()-60, lightbox.height ),
                ratio = Math.min( width / lightbox.width, height / lightbox.height ),
                // destWidth = Math.round( lightbox.width * ratio ) + 40,
                destWidth = Math.round( lightbox.width * ratio ) + 20,
                // destHeight = Math.round( lightbox.height * ratio ) + 60,
                destHeight = Math.round( lightbox.height * ratio ) + 20,
                to = {
                    width: destWidth,
                    height: destHeight,
                    'margin-top': Math.ceil( destHeight / 2 ) *- 1,
                    'margin-left': Math.ceil( destWidth / 2 ) *- 1
                };

            // if rescale event, don't animate
			event = false;
            if ( event ) {
                $('body > .od-lightbox-box').css( to );
            } else {
                $('body > .od-lightbox-box').animate( to, {
                    duration: 200
                });
            }
		}
    };

    return this;
};

// end ODLightbox constructor

ODLightbox.prototype = {
    constructor: ODLightbox,
    init: function( target, options ) {
		this._lightbox.init();
        return this;
    },
	run: function(target, options) {
		this._options.contentHtml = options.contentHtml;
		this._lightbox.show();
	}
};
// End of ODLightbox prototype


// the plugin initializer
$.fn.odlightbox = function( options ) {

    var selector = this.selector;

    // try domReady if element not found
    if ( !$(this).length ) {

        $(function() {
            $( selector ).odlightbox( options );
        });
        return this;
    }

    return this.each(function() {
		var obj = new ODLightbox();
        if ( !$.data(this, 'odlightbox') ) {
            $.data( this, 'odlightbox', obj.init( this, options ) );
        }
		obj.run(this, options);
    });

};

}( jQuery ) );
