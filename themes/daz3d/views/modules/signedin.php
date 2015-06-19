<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();
if ($Session->IsValid() && C('Garden.Modules.ShowSignedInModule')) {
	$Name = $Session->User->Name;

   if (C('EnabledApplications.Conversations')) {
      $CountInbox = $Session->User->CountUnreadConversations;
      $CountInbox = (is_numeric($CountInbox) && $CountInbox > 0) ? $CountInbox : 0;
	}
	$CountNotifications = $Session->User->CountNotifications;
	$CountNotifications = (is_numeric($CountNotifications) && $CountNotifications > 0) ? $CountNotifications : 0;

?>
<div class="Box ProfileBox">
   <ul class="PanelInfo">
      <li><?php echo Anchor($Name, 'profile/'.$Session->User->UserID.'/'.Gdn_Format::Url($Name)); ?></li>
      <?php if (C('EnabledApplications.Conversations')) { ?>
      <li><?php echo Anchor(T('Inbox').' <span class="Aside"><span class="Count">'.$CountInbox.'</span></span>', '/messages/all'); ?></li>
      <?php } ?>
      <li><?php echo Anchor(T('Notifications').' <span class="Aside"><span class="Count">'.$CountNotifications.'</span></span>', '/profile/notifications'); ?></li>
      <?php if ($Session->CheckPermission('Garden.Settings.Manage')) { ?>
      <li><?php echo Anchor(T('Dashboard'), '/dashboard/settings'); ?></li>
      <?php } ?>
   </ul>
</div>
<?php
}