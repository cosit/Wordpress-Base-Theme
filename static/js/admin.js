var __init__ = function($){
	// YAYAY
};


(function($){
	__init__($);


	// Issuu preview functionality
	(function() {

		// For some reason, this only works with the EMBED tag.
		// The OBJECT tag will not change documents IDs when appended
		// to the div.
		var issuu_embed_html = '		<embed ';
			issuu_embed_html += '			src="http://static.issuu.com/webembed/viewers/style1/v2/IssuuReader.swf" ';
			issuu_embed_html += '			type="application/x-shockwave-flash" ';
			issuu_embed_html += '			allowfullscreen="true" ';
			issuu_embed_html += '			menu="false" ';
			issuu_embed_html += '			wmode="transparent" ';
			issuu_embed_html += '			style="width:700px;height:500px" flashvars="mode=magazine&amp;backgroundColor=%23222222&amp;documentId=<DOCUMENT_ID>" />';
		

		$('#issuu #documents li')
			.each(function(index, li) {
				var more_details        = $(li).find('.more_details'),
					more_details_toggle = $(li).find('.more_details_toggle'),
					preview             = $(li).find('.prev');

				more_details.hide();
				more_details_toggle
					.click(function() {
						more_details.toggle();
					});
				
				preview
					.click(function() {
						var document_id = $(this).attr('data-document-id'),
							pop_div     = $('#issuu #preview-popup');
						
						var object_html = issuu_embed_html.replace('<DOCUMENT_ID>', document_id);
						pop_div.append(object_html);
						pop_div.show();
						//console.log(object_html);
					})
			});
		$('#issuu #preview-popup a.close')
			.click(function() {
				$('#issuu #preview-popup').hide();
				$('#issuu #preview-popup > embed').remove();
				
			});
	})();
})(jQuery);
