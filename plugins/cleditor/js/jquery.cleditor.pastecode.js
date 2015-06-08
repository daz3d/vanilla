// Just append `pastecode` to your controls
(function($) {
	$.cleditor.buttons.pastecode = {
		name: "pastecode",
		image: "page_code.png",
		title: "Code",
		command: "inserthtml",
		popupName: "pastecode",
		popupClass: "cleditorPrompt",
		popupContent: '<label>Paste your code here:<br /><textarea rows="3" style="width:200px"></textarea></label><br /><input type="button" value="Submit" />',
		buttonClick: pastecodeClick
	};

	// Handle the hello button click event
	function pastecodeClick(e, data) {
		// Wire up the submit button click event
		$(data.popup).children(":button")
			.unbind("click")
			.bind("click", function(e) {

				// Get the editor
				var editor = data.editor;

				// Get the entered name
				var codeblock = $(data.popup).find("textarea").val();
				var html = '<pre class="prettyprint"><code>' + codeblock + '</code></pre>';

				// Insert some html into the document
				editor.execCommand(data.command, html, null, data.button);

				// Hide the popup and set focus back to the editor
				editor.hidePopups();
				editor.focus();
			});
	}
})(jQuery);
