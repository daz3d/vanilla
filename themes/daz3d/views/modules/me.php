<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();
$User = $Session->User;
$CssClass = '';
if ($this->CssClass)
   $CssClass .= ' '.$this->CssClass;

$DashboardCount = 0;
// Spam & Moderation Queue
if ($Session->CheckPermission('Garden.Settings.Manage') || $Session->CheckPermission('Garden.Moderation.Manage')) {
   $LogModel = new LogModel();
   $ModerationCount = $LogModel->GetOperationCount('moderate');
   $DashboardCount += $ModerationCount;
}

if ($Session->IsValid()):
   echo '<div class="MeBox'.$CssClass.'">';
	   echo '<ul>';

	   if ($Session->CheckPermission('Garden.Settings.Manage') || $Session->CheckPermission('Garden.Moderation.Manage')) {
		  $CSpam = ''; //$SpamCount > 0 ? ' '.Wrap($SpamCount, 'span class="Alert"') : '';
		  $CModeration = $ModerationCount > 0 ? ' '.Wrap($ModerationCount, 'span class="Alert"') : '';
		  echo Wrap(Anchor(Sprite('SpApplicants').' '.T('Applicants').$CApplicant, '/dashboard/user/applicants'), 'li');
		  echo Wrap(Anchor(Sprite('SpSpam').' '.T('Spam Queue').$CSpam, '/dashboard/log/spam'), 'li');
		  echo Wrap(Anchor(Sprite('SpMod').' '.T('Moderation Queue').$CModeration, '/dashboard/log/moderation'), 'li');
	   }

	   $this->FireEvent('FlyoutMenu');

	   echo '</ul>';
   echo '</div>';
endif;
