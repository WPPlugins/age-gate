<script id="tmpl-wp-age-gate" type="text/twig">

<div class="age-gate">
  <form method="post" action="" class="age-gate-form"<?php echo (isset($this->settings['wp_age_gate_fail_link']) && !empty($this->settings['wp_age_gate_fail_link'])) ? ' data-redirect="' . $this->settings['wp_age_gate_fail_link'] . '"' : ''; ?>>
    <input type="hidden" name="age-submit" value="1" />
    <?php if (isset($this->settings['wp_age_gate_logo']) && $this->settings['wp_age_gate_logo']): ?>
    <h1><img src="<?php echo wp_get_attachment_url($this->settings['wp_age_gate_logo']); ?>" alt="<?php echo bloginfo('name'); ?>" /></h1>
    <?php else: ?>
    <h1><?php echo bloginfo('name'); ?></h1>
    <?php endif ?>
    {% if not under and nsc or not nsc %}
      {% if instruction %}
        <h2>{{ instruction }}</h2>
      {% endif %}
      {% if messaging %}
      <p>{{ messaging|format(a) }}</p>
      {% endif %}
    

      {% include "tmpl-wp-age-gate-form-" ~ t %}
    {% endif %}

    <div class="error{% if under and nsc %} under{% endif%}">
    {% if under and nsc %}
    <p><?php _e('You are not old enough to view this content', 'age-gate'); ?></p>
    {% endif %}
    </div>

    {% if not under and nsc or not nsc %}
    {% if remember %}
    <p><label class="remember"><input type="checkbox" value="1" name="remember"> <?php _e('Remember me', 'age-gate'); ?></label></p>
    {% endif %}

    {% if t != 'buttons' %}
    <button type="submit"><?php echo esc_html($this->settings['wp_age_gate_button_text']) ?></button>
    {% endif %}
    {% endif %}

    <?php if(isset($this->settings['wp_age_gate_additional'])): ?>
    <div class="additional-information">
      <?php echo wpautop(wp_specialchars_decode($this->settings['wp_age_gate_additional'])) ?>
    </div>
    <?php endif; ?>
  </form>
</div>

</script>

<script id="tmpl-wp-age-gate-form-inputs" type="text/twig">
  <ul>
    <?php if ($this->settings['wp_age_gate_date_format'] == 'mmddyyyy'){
      include AGE_GATE_DIR . 'public/partials/form/sections/input-month.php';
      echo "\r\n";
      include AGE_GATE_DIR . 'public/partials/form/sections/input-day.php';

    } else {
      include AGE_GATE_DIR . 'public/partials/form/sections/input-day.php';
      echo "\r\n";
      include AGE_GATE_DIR . 'public/partials/form/sections/input-month.php';
      
    }
    ?>
    <li>
      <label for="dob-year"><?php _e('Year', 'age-gate'); ?></label>
      <input type="text" id="dob-year" name="year" placeholder="<?php _e('YYYY', 'aget-gate'); ?>" required minlength="4" maxlength="4" pattern="\d+" autocomplete="off">
    </li>
  </ul>
</script>

<script id="tmpl-wp-age-gate-form-selects" type="text/twig">

<ul>
  <?php if ($this->settings['wp_age_gate_date_format'] == 'mmddyyyy'){
    include AGE_GATE_DIR . 'public/partials/form/sections/select-month.php';
    echo "\r\n";
    include AGE_GATE_DIR . 'public/partials/form/sections/select-day.php';

  } else {
    include AGE_GATE_DIR . 'public/partials/form/sections/select-day.php';
    echo "\r\n";
    include AGE_GATE_DIR . 'public/partials/form/sections/select-month.php';
    
  }
  ?>

  <li>
    <label for="dob-year"><?php _e('Year', 'age-gate'); ?></label>
    <select type="text" id="dob-year" name="year" required>
      <option value=""><?php _e('YYYY', 'aget-gate'); ?></option>
      <?php for ($i = date('Y'); $i >= 1900; $i--): ?>
      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
      <?php endfor; ?>
    </select>
    
  </li>
</ul>

</script>

<script id="tmpl-wp-age-gate-form-buttons" type="text/twig">

  <p><?php printf(__('Are you over %s years of age?', 'age-gate'), $this->settings['wp_age_gate_min_age']); ?></p>

  <button type="submit" value="yes" name="confirm"><?php _e('Yes', 'age-gate'); ?></button>
  <button type="submit" value="no" name="confirm"><?php _e('No', 'age-gate'); ?></button>

</script>

<script type="text/twig" id="tmpl-wp-age-gate-error">
  <p>{{ error }}</p>
</script>