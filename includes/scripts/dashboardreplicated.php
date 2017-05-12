<?php

global $AI;


$username = $AI->user->username;
$replcated_url = 'http://'.$username.'.vivacitygo.com/';
?>


<div class="success_wrapper"><h4>
        Your Pre-launch Replicated URL is:
    <a href="<?php echo $replcated_url?>"  target="_blank"><?php echo $replcated_url;?></a>
</h4>
</div>