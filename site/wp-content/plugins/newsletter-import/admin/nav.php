<?php
?>
<ul class="tnp-nav">
    <li class="<?php echo $_GET['page'] === 'newsletter_import_csv'?'active':''?>"><a href="?page=newsletter_import_csv"><?php _e('CSV', 'newsletter')?></a></li>
    <li class="<?php echo $_GET['page'] === 'newsletter_import_clipboard'?'active':''?>"><a href="?page=newsletter_import_clipboard"><?php _e('Copy and Paste', 'newsletter')?></a></li>
    <li class="<?php echo $_GET['page'] === 'newsletter_import_bounce'?'active':''?>"><a href="?page=newsletter_import_bounce"><?php _e('Bounces', 'newsletter')?></a></li>
</ul>
