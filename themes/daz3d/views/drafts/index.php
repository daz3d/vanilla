<?php if (!defined('APPLICATION')) exit();
echo '<span class="page-title"><h1 class="H HomepageTitle MyDraftsTitle">'.$this->Data('Title').'</h1></span>';
include($this->FetchViewLocation('helper_functions', 'discussions', 'vanilla'));
$Session = Gdn::Session();
$ShowOptions = FALSE;
$Alt = '';
$ViewLocation = $this->FetchViewLocation('drafts', 'drafts');
if ($this->DraftData->NumRows() > 0) {
   echo $this->Pager->ToString('less');
?>
<ul class="DataList Drafts">
   <?php
   include($ViewLocation);
   ?>
</ul>
   <?php
   echo $this->Pager->ToString('more');
} else {
   ?>
   <div class="Empty"><?php echo T('You do not have any drafts.'); ?></div>
   <?php
}
