<?php if (!defined('APPLICATION')) exit();

$PluginInfo['FlairBadges'] = array(
    'Name' => 'Flair Badge',
    'Description' => "Adds flair under User's name.",
    'Version' => '1.02',
    'RequiredApplications' => array('Vanilla' => '2.1.11'),
    'MobileFriendly' => TRUE,
    'RegisterPermissions' => FALSE,
    'SettingsUrl' => 'dashboard/plugin/FlairBadges',
    'SettingsPermission' => 'Garden.AdminUser.Only',
);

class FlairBadgesPlugin extends Gdn_Plugin
{

    public function PluginController_FlairBadges_Create($Sender)
    {
        $Sender->Title('Badges');
        $Sender->Permission('Garden.Moderation.Manage');
        $Sender->Form = new Gdn_Form();
        $this->Dispatch($Sender, $Sender->RequestArgs);
    }

    public function Controller_Index($Sender)
    {
        $Sender->SetData('PluginDescription', $this->GetPluginKey('Description'));
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField('Plugins.FlairBadges.BadgeLocation', '1');
        $Sender->Form->SetModel($ConfigurationModel);

        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $ConfigurationModel->Validation->ApplyRule('Plugins.FlairBadges.BadgeLocation', 'Required');
            $Saved = $Sender->Form->Save();

            if ($Saved) $Sender->StatusMessage = T('Huzzah! The changes have been saved!');
        }

        $Sender->Render($this->GetView('FlairBadges.php'));
    }

    /**
     * Used to submit a poll vote via form
     * @param VanillaController $Sender ProfileController
     * @return bool|void
     */
    public function ProfileController_SetFlair_Create($Sender)
    {
        $post = file_get_contents("php://input");
        $request = json_decode($post);
        if (empty($request)
            || empty($request->email)
            || !filter_var($request->email, FILTER_VALIDATE_EMAIL)
        ) {
            return $Sender->RenderData(["Success" => false]);
        }

        if (!$this->passCheck($request->email, filter_var($request->hash, FILTER_SANITIZE_STRING))){
            return $Sender->RenderData(["Success" => false]);
        }

        $email = filter_var($request->email, FILTER_SANITIZE_EMAIL);
        $userData = Gdn::UserModel()->GetByEmail($email);
        if (empty($email)) {
            return $Sender->RenderData(["Success" => false]);
        }
        $metaInsert = [];
        foreach ($request->flairs as $flair) {
            $key = filter_var($flair->key, FILTER_SANITIZE_STRING);
            $value = filter_var($flair->value, FILTER_SANITIZE_STRING);
            if (!empty($key) && !empty($value)) {
                $metaInsert[$key] = $value;
            }
        }

        if (count($metaInsert)) {
            Gdn::UserModel()->SetMeta($userData->UserID, $metaInsert, 'Flair.');
            return $Sender->RenderData(["Success" => true]);
        }
        return $Sender->RenderData(["Success" => false]);
    }

    private function passCheck($email, $hash)
    {
        $Provider = Gdn_AuthenticationProviderModel::GetProviderByKey(1044711398);

        if (!$Provider) {
            return false;
        }
        $created = md5($email . $Provider['AssociationSecret']);
        if ($hash != $created) {
            return false;
        }

        return true;
    }


    public function Setup()
    {
        SaveToConfig('Plugins.FlairBadges.BadgeLocation', '1');
    }

    public function OnDisable()
    {
        RemoveFromConfig('Plugins.FlairBadges.BadgeLocation');
    }

    public function Base_GetAppSettingsMenuItems_Handler($Sender)
    {
        $Menu = &$Sender->EventArguments['SideMenu'];
        $Menu->AddLink('Add-ons', 'Role Badges', $this->GetPluginKey('SettingsUrl'), 'Garden.AdminUser.Only');
    }

    public function Base_Render_Before($Sender)
    {
        $Sender->AddCssFile($this->GetResource('design/FlairBadges.css', FALSE, FALSE));
    }

    public function DiscussionController_DiscussionInfo_Handler($Sender)
    {
        if ($this->badgeLocation() == 1) $this->attachBadge($Sender);
    }

    public function DiscussionController_CommentInfo_Handler($Sender)
    {
        if ($this->badgeLocation() == 1) $this->attachBadge($Sender);
    }

    public function PostController_CommentInfo_Handler($Sender)
    {
        if ($this->badgeLocation() == 1) $this->attachBadge($Sender);
    }

    public function DiscussionController_AuthorPhoto_Handler($Sender)
    {
        if ($this->badgeLocation() == 2) $this->attachBadge($Sender);
    }

    protected function badgeLocation()
    {
        return Gdn::Config('Plugins.FlairBadges.BadgeLocation');
    }

    protected function attachBadge($Sender)
    {
        $userID = $Sender->EventArguments['Author']->UserID;
        $flairData = Gdn::UserModel()->GetMeta($userID, 'Flair.%');

        if (!empty($flairData)) {
            echo '<div  class="FlairBadges">';
            foreach ($flairData as $Field => $Value) {
                $title = ltrim($Field, 'Flair.');
                echo '<span title="' . $title . '" class="FlairBadges ' . $Value . 'Badge"></span>';
            }
            echo '</div>';
        }
    }
}