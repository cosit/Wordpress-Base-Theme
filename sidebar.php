<?php disallow_direct_load('sidebar.php');?>

<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Sidebar')):?>
<ul>
	<li>Default Sidebar</li>
	<li>WHOA!</li>
</ul>
<?php endif;?>