 /*!
 * Deletable helper for fancyBox
 * version: 0.9
 * @requires fancyBox v2.0 or later
 *
 * Usage: 
 *     $(".fancybox").fancybox({
 *         delete: {
 *             position : 'top'
 *         }
 *     });
 * 
 * Options:
 *     tpl - HTML template
 *     position - 'top' or 'bottom'
 * 
 */
(function ($) {
	//Shortcut for fancyBox object
	var F = $.fancybox;
	//Add helper object
	F.helpers.deletable = {
		tpl: '<div id="fancybox-buttons"><a id="fancybox-delete-btn" title="Delete" href="javascript:;"></a></div>',
		list: null,
		
		del: function (opts) {
			if(confirm(opts.confirmMessage || 'Do you want to delete this photo?')){
				var img = $(F.group[F.current.index]);
				$.get(opts.deleteUrl+img.attr('rid'),function(){
					img.remove();
					F.close();
					if(typeof opts.afterDelete == 'function') opts.afterDelete();
				});
			}
		},

		beforeLoad: function (opts) {
			F.coming.margin[ opts.position === 'bottom' ? 2 : 0 ] += 30;
		},

		afterShow: function (opts) {
			if (!this.list) {
				var that = this;
				this.list = $(opts.tpl || this.tpl).addClass(opts.position || 'top').appendTo('body');
				this.list.find('#fancybox-delete-btn').click(function(){
					that.del(opts);
				});
			}
		},

		beforeClose: function () {
			if (this.list) {
				this.list.remove();
			}

			this.list = null;
		}
	};

}(jQuery));