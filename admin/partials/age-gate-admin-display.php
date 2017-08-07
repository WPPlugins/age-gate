<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php $activeTab = 'general'; ?>

<div class="wrap">
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>


  <?php settings_errors(); ?>

  <form action="<?php echo admin_url() ?>options.php" method="post">

    <?php 
      settings_fields($this->plugin_name . '_' .  $activeTab );
      do_settings_sections( $this->plugin_name . '_' .  $activeTab );


      submit_button();
     ?>
  </form>

</div>