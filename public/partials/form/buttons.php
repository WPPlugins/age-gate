<p><?php printf(__('Are you over %s years of age?', 'age-gate'), $settings['wp_age_gate_min_age']); ?></p>

<button type="submit" value="yes" name="confirm"><?php _e('Yes', 'age-gate'); ?></button>
<button type="submit" value="no" name="confirm"><?php _e('No', 'age-gate'); ?></button>

