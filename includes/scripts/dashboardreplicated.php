<?php

global $AI;


$username = $AI->user->username;
$replcated_url = 'http://'.$username.'.vivacitygo.com';
?>


<div class="success_wrapper"><h4>
    Your Replicated URL is:
    <a href="<?php echo $replcated_url.'?ai_bypass=true';?>"><?php echo $replcated_url;?></a>
</h4>
</div>