<?php if (!defined('APPLICATION')) exit(); ?>

<h1><?php echo T($this->Data['Title']); ?></h1>
<div class="Info">
   <?php echo T($this->Data['PluginDescription']); ?>
</div>
<h3><?php echo T('Settings'); ?></h3>
<?php
   echo $this->Form->Open();
   echo $this->Form->Errors();
?>
<div class="Info">
    Right now, badges will only show up before user names in 2.1+.
</div>
<ul>
   <li><?php
      echo $this->Form->Label('Show badges:', 'Plugins.FlairBadges.BadgeLocation');
      echo $this->Form->DropDown('Plugins.FlairBadges.BadgeLocation',array(
         '1'     => 'below',
         '2'     => 'before',
      ));
      echo ' usernames.';
   ?></li>
</ul>
<?php
   echo $this->Form->Close('Save');
?>
