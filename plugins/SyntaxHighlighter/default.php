<?php

$PluginInfo['SyntaxHighlighter'] = array(
    'Name' => 'Syntax Highlighter',
    'Description' => 'Provides syntax highlighting for embedded code.',
    'Version' => '0.1',
    'Author' => "DAZ3D",
    'AuthorEmail' => 'info@daz3d.com',
    'AuthorUrl' => 'http://www.daz3d.com/'
);

class SyntaxHighlighterPlugin extends Gdn_Plugin
{

    public function Base_Render_Before($Sender)
    {
        $Sender->AddJsFile('highlight.pack.js', 'plugins/SyntaxHighlighter');
        $Sender->AddCssFile('hybrid.css', 'plugins/SyntaxHighlighter');
        $Sender->Head->AddString('<script type="text/javascript">hljs.initHighlightingOnLoad();</script>');
    }

    public function DiscussionController_AfterCommentFormat_Handler($Sender) {
        $this->formatAll($Sender);
    }

    public function PostController_AfterCommentFormat_Handler($Sender) {
		$this->formatAll($Sender);
    }

	protected function formatAll($Sender) {
		if ( ! empty($Sender->Discussion) && ! empty($Sender->Discussion->FormatBody)) {
			$Sender->Discussion->FormatBody = $this->cleanCommentCode($Sender->Discussion->FormatBody);
		}

		if ( ! empty($Sender->CurrentComment) && ! empty($Sender->CurrentComment->FormatBody)) {
			$Sender->CurrentComment->FormatBody = $this->cleanCommentCode($Sender->CurrentComment->FormatBody);
		}
	}

    protected function cleanCommentCode($body) {
        try {
			$Dom = new DOMDocument();
			$Dom->recover = true;
			$Dom->loadHTML($body);
			$Path = new DOMXPath($Dom);
			$elements = $Path->query('*/pre/code');
		}
		catch (Exception $e) {
			return $body;
		}

		$replacements = array( );
        foreach ($elements as $element) {
            $element = $element->parentNode; // traverse back up to the <pre> node

            // convert the DOM object to an HTML string
            $newdoc = new DOMDocument();
            $cloned = $element->cloneNode(true);
            $newdoc->appendChild($newdoc->importNode($cloned, true));
            $orig_code = $code = trim($newdoc->saveHTML());
			$orig_code = preg_replace('%[\r\n]+%i', '', $orig_code);

            // clean the code
            $code = preg_replace('%<br\s*/?>\n*%i', "\n", $code);
            $code = preg_replace('%<pre>\s*<code%i', '<pre><code', $code);
            $code = preg_replace('%</code>\s*</pre>%i', '</code></pre>', $code);
            $code = strip_tags($code, '<pre><code>'); // vanilla likes to add links to things
            $code = html_entity_decode($code);

			$replacements[$orig_code] = $code;
        }

		// replace the code back into the HTML
		if ( ! empty($replacements)) {
			$body = preg_replace('%<br\s*/?>%i', '<br>', $body);
			$body = str_replace('&quot;', '"', $body);
			$body = preg_replace('%[\r\n]+%i', '', $body);
			$body = str_replace(array_keys($replacements), array_values($replacements), $body);
		}

        return $body;
    }

}
