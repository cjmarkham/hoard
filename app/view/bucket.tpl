<div class="container">
	<div class="row">

		<div class="col col-lg-6">
			<h5>Information</h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td>Name</td>
					<td class="align-right"><?= $app['name'] ?></td>
				</tr>
				<tr>
					<td>BucketID</td>
					<td class="align-right"><?= $app['appkey'] ?></td>
				</tr>
				<tr>
					<td>Secret</td>
					<td class="align-right"><?= $app['secret'] ?></td>
				</tr>
				<tr>
					<td>User(s)</td>
					<td class="align-right">
<?php foreach ($app['roles'] as $uid => $role): $user = App::$mongo->selectCollection('user')->findOne(array('_id' => new MongoId($uid))); ?>
						<?= $user['email'] ?> 
<?php endforeach; ?>
					</td>
				</tr>
			</table>

			<h5><a href="/viewer/#bucket=<?=$app['appkey']?>">Events</a></h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td>Past Minute</td>
					<td class="align-right"><?= number_format($app['records_1minute']) ?></td>
				</tr>
				<tr>
					<td>Past Hour</td>
					<td class="align-right"><?= number_format($app['records_1hour']) ?></td>
				</tr>
				<tr>
					<td>Past Day</td>
					<td class="align-right"><?= number_format($app['records_1day']) ?></td>
				</tr>
				<tr>
					<td>All</td>
					<td class="align-right"><?= number_format($app['records_all']) ?></td>
				</tr>
			</table>

			<h5>Admin</h5>
			<table class="table table-striped table-condensed">
				<tr>
					<td>Empty</td>
					<td class="align-right"><a href="/bucket/<?= $app['appkey'] ?>/empty" onclick="if ( ! confirm('Are you sure?')) return false">clear</a></td>
				<tr>
					<td>Delete</td>
					<td class="align-right"><a href="/bucket/<?= $app['appkey'] ?>/delete" onclick="if ( ! confirm('Are you sure?')) return false">destroy</a></td>
				</tr>
			</table>

		</div>

		<div class="col col-lg-6">

			<script>
			  chart_bucket = '<?= $app['appkey'] ?>';
			</script>
			<div id="dashboard_chart" style="width:100%;height:300px;text-align:center;line-height:300px"></div>
			<div class="row">
				<div class="col col-offset-2 col-lg-8">
					<div class="btn-group btn-group-justified" data-toggle="buttons-radio">
						<a class="btn btn-default" onclick="chart_getData('second')">Second</a>
						<a class="btn btn-default active" onclick="chart_getData('minute')">Minute</a>
						<a class="btn btn-default" onclick="chart_getData('hour')">Hour</a>
						<a class="btn btn-default" onclick="chart_getData('day')">Day</a>
					</div>
				</div>
			</div>
			<br/>

		</div>

	</div>
</div>
