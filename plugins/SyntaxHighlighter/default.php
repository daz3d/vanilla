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


    public function Base_AfterCommentFormat_Handler($Sender) {
        $body = $Sender->CurrentComment->FormatBody;
        $Dom = new DOMDocument( );
        $Dom->loadHTML($body);
        $Path = new DOMXPath($Dom);
        $elements = $Path->query('*/pre/code');

        foreach ($elements as $element) {
            $element = $element->parentNode; // back up to the <pre> node

            // convert the DOM object to an HTML string
            $newdoc = new DOMDocument( );
            $cloned = $element->cloneNode(true);
            $newdoc->appendChild($newdoc->importNode($cloned, true));
            $orig_code = $code = trim($newdoc->saveHTML( ));

            // clean the code
            $code = preg_replace('%<br\s*/?>%i', "\n", $code);
            $code = preg_replace('%<pre>\s*<code%i', '<pre><code', $code);
            $code = preg_replace('%</code>\s*</pre>%i', '</code></pre>', $code);
            $code = strip_tags($code, '<pre><code>'); // vanilla likes to add links to things
            $code = html_entity_decode($code);

            // replace the code back into the HTML
            $body = preg_replace('%<br\s*/?>%i', '<br>', $body);
            $body = str_replace('&quot;', '"', $body);
            $body = str_replace($orig_code, $code, $body);
        }

        $Sender->CurrentComment->FormatBody = $body;
    }

}
