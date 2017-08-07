<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://philsbury.uk
 * @since      1.0.0
 *
 * @package    Age_Gate
 * @subpackage Age_Gate/public/partials
 */

global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));

$settings = get_option('wp_age_gate_general');
$error = '';

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php if(isset($settings['wp_age_gate_device_width']) && !empty($settings['wp_age_gate_device_width'])): ?>
  <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
  <?php endif; ?>
  <?php wp_head(); ?>
</head>
<body class="age-restriction">
  <div class="age-gate-wrapper">
    <div class="age-gate">
      <form method="post" action="<?php echo $current_url; ?>" class="age-gate-form">
        <?php if (!isset($_SESSION['under']) && isset($settings['wp_age_gate_no_second_chance']) || !isset($settings['wp_age_gate_no_second_chance'])): ?>
          
        
        <input type="hidden" name="age-submit" value="1" />
        <?php wp_nonce_field( 'age_verification', '_agenonce' ); ?>


        <?php endif ?>
      <?php if (isset($settings['wp_age_gate_logo']) && $settings['wp_age_gate_logo']): ?>
        <h1><img src="<?php echo wp_get_attachment_url($settings['wp_age_gate_logo']); ?>" alt="<?php echo bloginfo('name'); ?>" /></h1>
      <?php else: ?>
        <h1><?php echo bloginfo('name'); ?></h1>
      <?php endif ?>


      <?php if (!isset($_SESSION['under']) && isset($settings['wp_age_gate_no_second_chance']) || !isset($settings['wp_age_gate_no_second_chance'])): ?>
      <?php if (isset($settings['wp_age_gate_instruction']) && $settings['wp_age_gate_instruction']): ?>
      <h2><?php printf(esc_html($settings['wp_age_gate_instruction']), $settings['wp_age_gate_min_age']); ?></h2>
      <?php endif ?>

      <?php if (isset($settings['wp_age_gate_messaging']) && $settings['wp_age_gate_messaging']): ?>
      <p><?php printf(esc_html($settings['wp_age_gate_messaging']), $settings['wp_age_gate_min_age']); ?></p>
      <?php endif ?>

      
      <?php include 'form/'. $settings['wp_age_gate_input_type'] .'.php'; ?>
      <?php endif ?>

    <?php if(isset($_REQUEST['error'])): $error = $_REQUEST['error'] ?>
      <div class="error">
        <p><?php switch($error){
          case 1:
          
          if(isset($settings['wp_age_gate_invalid_input_msg']) && !empty($settings['wp_age_gate_invalid_input_msg'])){

            printf(esc_html($settings['wp_age_gate_invalid_input_msg']), $settings['wp_age_gate_min_age']);
          } else {
            _e('Your input was invalid', 'age-gate');
          }
          break;
          case 2:
          
          if(isset($settings['wp_age_gate_under_age_msg']) && !empty($settings['wp_age_gate_under_age_msg'])){
            printf(esc_html($settings['wp_age_gate_under_age_msg']), $settings['wp_age_gate_min_age']);
          } else {
            _e('You are not old enough to view this content', 'age-gate');
          }
          break;
          case 3: 
          _e('Nonce failure, please try again', 'age-gate');
          break;
          default: 
          
          if(isset($settings['wp_age_gate_generic_error_msg']) && !empty($settings['wp_age_gate_generic_error_msg'])){
            printf(esc_html($settings['wp_age_gate_generic_error_msg']), $settings['wp_age_gate_min_age']);
          } else {
            _e('An error occured, please try again', 'age-gate');
          }
          break;
        }?></p>
        
      </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['under']) && isset($settings['wp_age_gate_no_second_chance']) && !$error): ?>
    <div class="error">
      <p><?php _e('You are not old enough to view this content', 'age-gate'); ?></p>
    </div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['under']) && isset($settings['wp_age_gate_no_second_chance']) || !isset($settings['wp_age_gate_no_second_chance'])): ?>
    <?php if (isset($settings['wp_age_gate_remember'])): ?>
      <p><label class="remember"><input type="checkbox" value="1" name="remember"> <?php _e('Remember me', 'age-gate'); ?></label></p>
    <?php endif ?>

    <?php if ($settings['wp_age_gate_input_type'] !== 'buttons'): ?>
    <button type="submit"><?php echo esc_html($settings['wp_age_gate_button_text']) ?></button>  
    <?php endif ?>

    <?php endif; ?>  

    <?php if(isset($settings['wp_age_gate_additional'])): ?>
    <div class="additional-information">
      <?php echo wpautop(wp_specialchars_decode($settings['wp_age_gate_additional'])) ?>
    </div>
    <?php endif; ?>  


  </form>
</div>
</div>
<?php wp_footer(); ?>
</body>
</html>