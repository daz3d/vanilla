<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();

if ( ! function_exists('FormatBody')) {
	include_once $this->FetchViewLocation('helper_functions', 'discussion');
}

$Alt = FALSE;
$CurrentOffset = $this->Offset;
foreach ($this->MessageData->Result() as $Message) {
   $CurrentOffset++;
   $Alt = $Alt == TRUE ? FALSE : TRUE;
   $Class = 'self-clearing Item';
   $Class .= $Alt ? ' Alt' : '';
   if ($this->Conversation->DateLastViewed < $Message->DateInserted)
      $Class .= ' New';

   if ($Message->InsertUserID == $Session->UserID)
      $Class .= ' Mine';

   if ($Message->InsertPhoto != '')
      $Class .= ' HasPhoto';

   $Message->Format = empty($Message->Format) ? 'Display' : $Message->Format;
   $Author = UserBuilder($Message, 'Insert');

   $this->EventArguments['Message'] = &$Message;
   $this->EventArguments['Class'] = &$Class;
   $this->FireEvent('BeforeConversationMessageItem');
   $Class = trim($Class);
?>
<li id="Message_<?php echo $Message->MessageID; ?>"<?php echo $Class == '' ? '' : ' class="'.$Class.'"'; ?>>
   <div id="Item_<?php echo $CurrentOffset ?>" class="ConversationMessage">
      <div class="Meta">
         <span class="Author">
            <?php echo UserPhoto($Author, 'Photo'); ?>
         </span>
		 <span class="MItem Name"><?php echo UserAnchor($Author, 'Name'); ?></span>
		 <span class="MItem DateCreated"><?php echo Gdn_Format::Date($Message->DateInserted); ?></span>
      </div>
      <div class="Message">
         <?php
			$this->FireEvent('BeforeConversationMessageBody');
		 	$Message->FormatBody = Gdn_Format::To($Message->Body, $Message->Format);
			$this->FireEvent('AfterMessageFormat');

			echo $Message->FormatBody;
		 ?>
      </div>
   </div>
</li>
<?php }
