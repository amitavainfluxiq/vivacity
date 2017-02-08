<?php

global $AI;


$username = $AI->user->username;
$replcated_url = 'http://'.$username.'.vivacitygo.com/prelaunch';
?>


<div class="success_wrapper"><h4>
        Your Pre-launch Replicated URL is:
    <a href="<?php echo $replcated_url.'?ai_bypass=true';?>"  target="_blank"><?php echo $replcated_url;?>?ai_bypass=true</a>
</h4>
</div>