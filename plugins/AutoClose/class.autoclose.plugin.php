<?php

if ( ! defined('APPLICATION')) exit( );

$PluginInfo['AutoClose'] = array(
    'Name' => 'Auto Close',
    'Description' => 'Automatically close discussion threads after a given number of days',
    'Version' => '0.1',
    'Author' => 'Benjam Welker',
    'AuthorEmail' => 'bwelker@daz3d.com',
    'SettingsUrl' => '/settings/autoclose',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'MobileFriendly' => TRUE,
);

class AutoClose extends Gdn_Plugin
{

    public function Setup( ) {
        if (false === Gdn::Config('Plugins.AutoClose.Days', false)) {
            SaveToConfig('Plugins.AutoClose.Days', 0);
        }
    }

    public function SettingsController_Autoclose_Create($Sender) {
        $Sender->Permission('Garden.Settings.Manage');

        $Conf = new ConfigurationModule($Sender);
        $Conf->Initialize(array(
            'Plugins.AutoClose.Days' => array('Control' => 'TextBox', 'LabelCode' => 'Days until closed', 'Description' => 'The number of inactive days before the discussion is automatically closed'),
        ));

        $Sender->AddSideMenu( );
        $Sender->SetData('Title', sprintf(T('%s Settings'), T('Auto Close')));
        $Sender->ConfigurationModule = $Conf;
        $Conf->RenderAll( );
    }

    // an event that should be run at least once a day...
    // not necessarily a hook that is important for the plugin
    public function PostController_BeforeDiscussionRender_Handler($Sender) {
        $this->updateConfig( );
    }

    protected function updateConfig( ) {
        // Make sure the Archive Date is up-to-date
        $ArchiveSpan = Gdn::Config('Plugins.AutoClose.Days', 0);
        if ($ArchiveSpan && (0 < $ArchiveSpan)) {
            Gdn::Config()->SaveToConfig('Vanilla.Archive.Date', date('Y-m-d', strtotime($ArchiveSpan . ' days ago')));
        }
    }

}
