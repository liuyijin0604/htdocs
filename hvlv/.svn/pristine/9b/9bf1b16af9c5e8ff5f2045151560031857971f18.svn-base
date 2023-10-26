<?php foreach($files as $file) { ?>
<div style="text-align: center; display: inline-block;">
	<img src="<?php echo $file->getUrl(); ?>" id="file_<?php echo $file->id; ?>" width="360px" height="240px" onclick="returnFileUrl('<?=$file->getUrl()?>')" style="cursor: hand;" />
</div>
<?php } ?>

<script type="text/javascript">
// Helper function to get parameters from the query string.
function getUrlParam(paramName) {
	var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
	var match = window.location.search.match(reParam);

	return (match && match.length > 1) ? match[1] : null;
}

// Simulate user action of selecting a file to be returned to CKEditor.
function returnFileUrl(url) {
	var funcNum = getUrlParam('CKEditorFuncNum');
	var fileUrl = url;

	window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl, function() {
		// Get the reference to a dialog window.
		var dialog = this.getDialog();
		// Check if this is the Image Properties dialog window.
		if (dialog.getName() == 'image') {
			// Get the reference to a text field that stores the "alt" attribute.
			var element = dialog.getContentElement('info', 'txtAlt');
			// Assign the new value.
			if (element)
				element.setValue('alt text');
		}
		// Return "false" to stop further execution. In such case CKEditor will ignore the second argument ("fileUrl")
		// and the "onSelect" function assigned to the button that called the file manager (if defined).
		// return false;
	} );
	window.close();
}
</script>