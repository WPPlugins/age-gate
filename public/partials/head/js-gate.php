<?php include AGE_GATE_DIR . 'public/partials/head/js-template.php'; ?>
<script>
  var ag = {a: <?php echo $this->settings['wp_age_gate_min_age'] ?>,r: <?php echo ($this->_ageRestricted()) ? 1 : 0; ?>,t: '<?php echo $this->settings["wp_age_gate_input_type"] ?>',f: '<?php echo $this->settings["wp_age_gate_date_format"] ?>',instruction: "<?php echo $this->settings['wp_age_gate_instruction']; ?>",messaging: "<?php echo $this->settings['wp_age_gate_messaging']; ?>",remember: "<?php echo (isset($this->settings['wp_age_gate_remember']) && !empty($this->settings['wp_age_gate_remember'])) ? 1 : 0; ?>",errors: {1: "<?php printf(esc_html($this->settings['wp_age_gate_invalid_input_msg']), $this->settings['wp_age_gate_min_age']); ?>",2: "<?php printf(esc_html($this->settings['wp_age_gate_under_age_msg']), $this->settings['wp_age_gate_min_age']); ?>",3: "<?php printf(esc_html($this->settings['wp_age_gate_generic_error_msg']), $this->settings['wp_age_gate_min_age']); ?>"},nsc: <?php echo (isset($this->settings['wp_age_gate_no_second_chance']) && !empty($this->settings['wp_age_gate_no_second_chance'])) ? 1 : 0; ?>}
</script>
<script src="<?php echo $dir; ?>js/twig.min.js"></script> 
<script src="<?php echo $dir; ?>js/moment.min.js"></script> 
<script src="<?php echo $dir; ?>js/age-gate-public.js"></script>

