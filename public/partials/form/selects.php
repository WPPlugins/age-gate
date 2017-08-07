<ul>
  <?php if ($settings['wp_age_gate_date_format'] == 'mmddyyyy'){
    include 'sections/select-month.php';
    echo "\r\n";
    include 'sections/select-day.php';

  } else {
    include 'sections/select-day.php';
    echo "\r\n";
    include 'sections/select-month.php';
    
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

