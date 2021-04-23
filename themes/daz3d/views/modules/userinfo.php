<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::Session();
if (Gdn::Config('Garden.Profile.ShowAbout')) {
   require_once Gdn::Controller()->FetchViewLocation('helper_functions', 'Profile', 'Dashboard');

?>
<div class="About P">
   <h2 class="H self-clearing"><?php echo T('About'); ?></h2>
   <dl class="About self-clearing">
      <?php
      if ($this->User->Banned && $Session->CheckPermission('Garden.Moderation.Manage')) {
         echo '<dd class="Value"><span class="Tag Tag-Banned">'.T('Banned').'</span></dd>';
      }
      ?>

      <div class="UserDetail">
         <dt class="Name"><?php echo T('Username'); ?></dt>
         <dd class="Name" itemprop="name"><?php echo $this->User->Name; ?></dd>
      </div>

      <div class="UserDetail">
         <?php
         if ($this->User->Email && ($this->User->ShowEmail || $Session->CheckPermission('Garden.Moderation.Manage'))) {
            echo '<dt class="Email">'.T('Email').'</dt>
            <dd class="Email" itemprop="email">'.Gdn_Format::Email($this->User->Email).'</dd>';
         }
         ?>
      </div>

      <div class="UserDetail">
         <dt class="Joined"><?php echo T('Joined'); ?></dt>
         <dd class="Joined"><?php echo Gdn_Format::Date($this->User->DateFirstVisit, 'html'); ?></dd>
      </div>

      <div class="UserDetail">
         <dt class="Visits"><?php echo T('Visits'); ?></dt>
         <dd class="Visits"><?php echo number_format($this->User->CountVisits); ?></dd>
      </div>

      <div class="UserDetail">
         <dt class="LastActive"><?php echo T('Last Active'); ?></dt>
         <dd class="LastActive"><?php echo Gdn_Format::Date($this->User->DateLastActive, 'html'); ?></dd>
      </div>

      <div class="UserDetail">
         <dt class="Roles"><?php echo T('Roles'); ?></dt>
         <dd class="Roles"><?php
            if (Gdn::Session()->CheckPermission('Garden.Moderation.Manage')) {
               echo UserVerified($this->User) . ', ';
            }

            $Roles = $this->Roles;

            foreach ($Roles as $key => & $Role) {
               $Role['Name'] = trim($Role['Name']);

               // hide any roles with an underscore prefix from regular people
               if ((0 === strpos($Role['Name'], '_')) && ($this->User->UserID != Gdn::Session()->UserID) && ! CheckPermission('Garden.Moderation.Manage')) {
                  unset($Roles[$key]);
               }
            }
            unset($Role);

            if (empty($Roles)) {
               echo T('No Roles');
            }
            else {
               echo htmlspecialchars(implode(', ', ConsolidateArrayValuesByKey($Roles, 'Name')));
            }

         ?></dd>
      </div>

      <?php if ($Points = GetValueR('User.Points', $this, 0)) : // Only show positive point totals  ?>
      <div class="UserDetail">
         <dt class="Points"><?php echo T('Points'); ?></dt>
         <dd class="Points"><?php echo number_format($Points); ?></dd>
      </div>
      <?php endif; ?>

      <?php if ($Session->CheckPermission('Garden.Moderation.Manage')) : ?>
      <div class="UserDetail">
         <dt class="IP"><?php echo T('Register IP'); ?></dt>
         <dd class="IP"><?php
            $IP = IPAnchor($this->User->InsertIPAddress);
            echo $IP ? $IP : T('n/a');
         ?></dd>
      </div>

      <div class="UserDetail">
         <dt class="IP"><?php echo T('Last IP'); ?></dt>
         <dd class="IP"><?php
            $IP = IPAnchor($this->User->LastIPAddress);
            echo $IP ? $IP : T('n/a');
         ?></dd>
      </div>
      <?php endif; ?>

      <div class="UserDetail">
         <?php $this->FireEvent('OnBasicInfo'); ?>
      </div>

   </dl>
</div>
<?php
}
