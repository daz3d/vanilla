<?php if (!defined('APPLICATION')) exit();

// Define the plugin:
$PluginInfo['Gravatar'] = array(
   'Name' => 'Gravatar',
   'Description' => 'Implements Gravatar avatars for all users who have not uploaded their own custom profile picture & icon.',
   'Version' => '1.4.3',
   'Author' => "Mark O'Sullivan",
   'AuthorEmail' => 'mark@vanillaforums.com',
   'AuthorUrl' => 'http://vanillaforums.com',
	'MobileFriendly' => TRUE
);

// 1.1 Fixes - Used GetValue to retrieve array props instead of direct references
// 1.2 Fixes - Make Gravatar work with the mobile theme
// 1.3 Fixes - Changed UserBuilder override to also accept an array of user info
// 1.4 Change - Lets you chain Vanillicon as the default by setting Plugins.Gravatar.UseVanillicon in config.

class GravatarPlugin extends Gdn_Plugin {
   public function ProfileController_AfterAddSideMenu_Handler($Sender, $Args) {
      if (!$Sender->User->Photo) {
         $Sender->User->Photo = UserPhotoDefaultUrl($Sender->User, C('Garden.Profile.MaxWidth', 200));
      }
   }
}

if (!function_exists('UserPhotoDefaultUrl')) {
   function UserPhotoDefaultUrl($User, $Size = null) {
      if (is_null($Size)) {
         $Size = C('Garden.Thumbnail.Size', 50);
      }

      $Size = (int) $Size;

      $Email = md5(strtolower(trim(GetValue('Email', $User))));
      $Protocol = (('https' === Gdn::Request()->Scheme()) ? 'https://secure.' : 'http://www.');

      $Url = $Protocol.'gravatar.com/avatar/'
          .$Email.'?'
          .'&amp;r=pg'
          .'&amp;s='.$Size;

      if (C('Plugins.Gravatar.UseVanillicon', TRUE))
         $Url .= '&amp;d='.urlencode(Gdn::Request()->Scheme().'://vanillicon.com/'.$Email.'_'.min($Size, 200).'.png');
      else
         $Url .= '&amp;d='.urlencode(Asset(C('Plugins.Gravatar.DefaultAvatar', 'identicon'), TRUE));

      return $Url;
   }
}
