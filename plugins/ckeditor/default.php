<?php

if ( ! defined('APPLICATION')) {
    die();
}

$PluginInfo['ckeditor'] = array(
    'Name' => 'CKEditor WYSIWYG',
    'Description' => 'Adds a <a href="http://en.wikipedia.org/wiki/WYSIWYG">WYSIWYG</a> editor to your forum so that your users can enter rich text comments.',
    'Version' => '1.0',
    'Author' => "DAZ3D",
    'AuthorEmail' => 'info@daz3d.com',
    'AuthorUrl' => 'http://www.daz3d.com',
    'RequiredApplications' => array('Vanilla' => '>=2'),
    'RequiredTheme' => false,
    'RequiredPlugins' => false,
    'HasLocale' => false,
    'RegisterPermissions' => false,
    'SettingsUrl' => false,
    'SettingsPermission' => false
);

class ckeditorPlugin extends Gdn_Plugin
{

    public function Gdn_Dispatcher_AppStartup_Handler($Sender, $Args)
    {
        // Save in memory only so it does not persist after plugin is gone.
        SaveToConfig('Garden.Html.SafeStyles', false, false);
    }

    /**
     * @param Gdn_Form $Sender
     */
    public function Gdn_Form_BeforeBodyBox_Handler($Sender, $Args)
    {
        $Column = GetValue('Column', $Args, 'Body');
        $this->_AddCKEditor(Gdn::Controller(), $Column);

        $Format = $Sender->GetValue('Format');

        if ($Format) {
            $Formatter = Gdn::Factory($Format . 'Formatter');

            if ($Formatter && method_exists($Formatter, 'FormatForWysiwyg')) {
                $Body = $Formatter->FormatForWysiwyg($Sender->GetValue($Column));
                $Sender->SetValue($Column, $Body);
            }
            elseif ( ! in_array($Format, array('Html', 'Wysiwyg'))) {
                $Sender->SetValue($Column, Gdn_Format::To($Sender->GetValue($Column), $Format));
            }
        }
        $Sender->SetValue('Format', 'Wysiwyg');
    }

    public function AddCKEditor()
    {
        $this->_AddCKEditor(Gdn::Controller());
    }

    private function _AddCKEditor($Sender, $Column = 'Body')
    {
        static $Added = false;
        if ($Added) {
            return;
        }

        // Add the CKEditor to the form
        $Options = array(
            'ie' => 'gt IE 6',
            'notie' => true
        ); // Exclude IE6
        $Sender->RemoveJsFile('jquery.autogrow.js');
        $Sender->AddJsFile('plugins/ckeditor/js/ckeditor/ckeditor.js', 'plugins/ckeditor', $Options);
        $Sender->AddJsFile('plugins/ckeditor/js/ckeditor/adapters/jquery.js', 'plugins/ckeditor', $Options);

        $Sender->Head->AddString(
            <<<EOT
<style type="text/css">
    a.PreviewButton {
        display: none !important;
    }
</style>
<script type="text/javascript">
	jQuery(document).ready( function($) {
			$("textarea.BodyBox").ckeditor({
				extraAllowedContent: 'blockquote[rel](Quote)'
			});
	});
</script>
EOT
        );

        $Added = true;
    }

    public function PostController_Quote_Before($Sender, $Args)
    {
        // Make sure quotes know that we are hijacking the format to wysiwyg.
        if ( ! C('Garden.ForceInputFormatter')) {
            SaveToConfig('Garden.InputFormatter', 'Wysiwyg', false);
        }
    }

}
