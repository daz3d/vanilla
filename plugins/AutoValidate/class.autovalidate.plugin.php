<?php

if ( ! defined('APPLICATION')) exit( );

$PluginInfo['AutoValidate'] = array(
    'Name' => 'Auto Validate',
    'Description' => 'Automatically validate users after a certain number of posts get approved',
    'Version' => '0.1',
    'Author' => 'Benjam Welker',
    'AuthorEmail' => 'bwelker@daz3d.com',
    'SettingsUrl' => '/settings/autovalidate',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'MobileFriendly' => TRUE,
);

class AutoValidate extends Gdn_Plugin
{

    public function Setup( ) {
        if ( ! c('Plugins.AutoValidate.DiscussionCount', 0)) {
            SaveToConfig('Plugins.AutoValidate.DiscussionCount', 5);
        }

        if ( ! c('Plugins.AutoValidate.CommentCount', 0)) {
            SaveToConfig('Plugins.AutoValidate.CommentCount', 10);
        }
    }

    public function SettingsController_Autovalidate_Create($Sender) {
        $Sender->Permission('Garden.Settings.Manage');

        $Conf = new ConfigurationModule($Sender);
        $Conf->Initialize(array(
            'Plugins.AutoValidate.DiscussionCount' => array('Control' => 'TextBox', 'LabelCode' => 'Approved Discussions', 'Description' => 'The number of approved posts required before the user is automatically validated'),
            'Plugins.AutoValidate.CommentCount' => array('Control' => 'TextBox', 'LabelCode' => 'Approved Comments'),
        ));

        $Sender->AddSideMenu( );
        $Sender->SetData('Title', sprintf(T('%s Settings'), T('Auto Validate')));
        $Sender->ConfigurationModule = $Conf;
        $Conf->RenderAll( );
    }

    public function LogController_Restore_Before($Sender) {
        $this->Run($Sender, +1);
    }

    public function LogController_Delete_Before($Sender) {
        $this->Run($Sender, -1);
    }

    public function UserController_Verify_Before($Sender) {
        // if a user gets their verification status changed
        // set their counts back to 0
        $UserID = $Sender->ReflectArgs['UserID'];
        $this->updateCount($UserID, 'Discussion', 0);
        $this->updateCount($UserID, 'Comment', 0);
    }

    public function UserModel_AfterSave_Handler($Sender) {
        // if a user gets their verification status changed
        // set their counts back to 0
        $EA = $Sender->EventArguments;
        if (array_key_exists('Verified', $EA['FormPostValues']) && ((bool) $EA['FormPostValues']['Verified'] !== (bool) $EA['LoadedUser']['Verified'])) {
            $UserID = $Sender->EventArguments['UserID'];
            $this->updateCount($UserID, 'Discussion', 0);
            $this->updateCount($UserID, 'Comment', 0);
        }
    }

    protected function Run($Sender, $Change = +1) {
        $LogIDs = $Sender->ReflectArgs['LogIDs'];

        $LogModel = new LogModel( );
        $Logs = $LogModel->GetIDs($LogIDs);

        foreach ($Logs as $Log) {
            if (('Pending' !== $Log['Operation']) || ! in_array($Log['RecordType'], array('Discussion', 'Comment'))) {
                continue;
            }

            $UserID = $Log['RecordUserID'];
            $Attribute = $Log['RecordType'];
            $this->updateCount($UserID, $Attribute, $Change);
        }
    }

    protected function updateCount($UserID, $Attribute, $Change = +1) {
        if (0 === $Change) {
            Gdn::UserModel()->SaveAttribute($UserID, 'AV' . $Attribute . 'Count', 0);
            return;
        }

        $Value = Gdn::UserModel( )->GetAttribute($UserID, 'AV'. $Attribute .'Count', 0);
        $Value += $Change;
        Gdn::UserModel( )->SaveAttribute($UserID, 'AV'. $Attribute .'Count', $Value);

        $Check = c('Plugins.AutoValidate.'. $Attribute .'Count', 0);
        if ($Check && ($Check <= $Value)) {
            Gdn::UserModel( )->SetField($UserID, 'Verified', 1);
        }
        elseif ($Check && ($Change < 0) && ($Value < $Check)) {
            Gdn::UserModel( )->SetField($UserID, 'Verified', 0);
        }
// TODO: more observers are needed to watch for deleted posts
// and/or posts that have been moved to the wasteland
    }

}
