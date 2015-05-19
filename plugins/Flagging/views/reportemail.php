<?php if (!defined('APPLICATION')) exit(); ?>
<?php
$Flag = GetValue('Plugin.Flagging.Data', $this->Data);
$Report = GetValue('Plugin.Flagging.Report', $this->Data);
$DiscussionID = GetValue('Plugin.Flagging.DiscussionID', $this->Data);
$Reason = GetValue('Plugin.Flagging.Reason', $this->Data);

echo T('Discussion'); ?>: <?php if (isset($Report['DiscussionName'])) echo $Report['DiscussionName']; ?>

<?php echo ExternalUrl($Flag['URL']); ?>


<?php echo T('Reason') . ': ' . $Reason; ?>


<?php echo T('FlaggedBy', 'Reported by:') .' '. $Flag['UserName']; ?>

<?php if ($DiscussionID) echo T('FlagDiscuss', 'Discuss it') . ': ' . ExternalUrl('discussion/'.$DiscussionID); ?>