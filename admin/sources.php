<?php
require_once(dirname(__FILE__) . '/admin.php');

$ADMIN_SECTION = 'sources';
require_once(dirname(__FILE__) . '/header.php');

$days = 30;

$sources = User::getReferers($days);
/*
 * uksort($sources, function($a, $b) {
  return count($a) - count($b);
  });
 */
?>
<div class="span9">
	<p>Sources that attracted the most registered users in the last <?php echo $days ?> days.</p>
	<table class="table">
		<?php foreach ($sources as $source => $users) { ?>
			<tr>
				<td><a href="<?php echo UserTools::escape($source) ?>" target="_blank"><?php echo UserTools::escape($source) ?></a></td>
				<td><span class="badge"><?php echo count($users) ?> users</span></td>
				<td>
					<?php foreach ($users as $user) { ?>
						<a style="margin-right: 0.5em" href="<?php echo UserConfig::$USERSROOTURL ?>/admin/user.php?id=<?php echo $user->getID(); ?>">
							<i class="icon-user"></i> <?php echo UserTools::escape($user->getName()) ?>
						</a>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
<?php
require_once(dirname(__FILE__) . '/footer.php');