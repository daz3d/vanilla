<?php if (!defined('APPLICATION')) exit(); ?>
<div class="FormTitleWrapper ChangeAuthorForm">
   <?php
   $id = 'MC_'.substr(md5(uniqid(microtime(true), true)), 0, 10);

   echo Wrap($this->Data('Title'), 'h1', array('class' => 'H'));

   echo '<div class="FormWrapper">';
   echo $this->Form->Open();
   echo $this->Form->Errors();

   echo '<div class="P">';
   echo $this->Form->Label('New Author', 'Author');
   echo Wrap($this->Form->TextBox('Author', array('class' => 'MultiComplete', 'id' => $id)), 'div', array('class' => 'TextBoxWrapper'));
   echo '</div>';

   echo $this->Form->Close('Change Author', '', array('class' => 'button Button Primary'));
   echo '</div>';
   ?>
</div>
<script type="text/javascript">
   AuthorSelectorInit(<?= $id ?>);
</script>