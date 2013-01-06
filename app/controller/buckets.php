<?php

class BucketsController extends PageController
{
	
	public function before ()
	{
		if ( ! Auth::$id)
		{
			header('Location: /login/');
			exit;
		}
		$this->set('title', 'Hoard - My Apps');
	}
		
	public function req_post ()
	{
		
		if ($this->uri[1] === 'new')
		{
			$name = preg_replace('/[^a-zA-Z0-9\.\-_ \(\)\'"]/', '', $_POST['app-name']);
			if ($name)
			{
				$appkey = uniqid();
				$secret = sha1($appkey . uniqid() . $_SERVER['REMOTE_ADDR'] . rand(0, 999999));
				MongoX::selectCollection('app')->insert(array(
					'name' => $name,
					'appkey' => $appkey,
					'secret' => $secret,
					'roles' => array(
						Auth::$id => 'owner'
					),
					'created' => new MongoDate(),
					'updated' => new MongoDate()
				));
				$this->alert('Your app was created');
			}
			else
			{
				$this->alert('You need to specify a name');
			}
		}
		
		// Fallback to get
		$cursor = MongoX::selectCollection('app')->find(array('roles.' . Auth::$id => array('$exists' => 1)));
		Auth::$apps = iterator_to_array($cursor);
		return $this->req_get();
		
	}
	
	public function req_get ()
	{
		
		$collection = MongoX::selectCollection('app');
		
		$apps = Auth::$apps;
		$totals = array('records' => 0, 'rps' => 0, 'storage' => 0);
		foreach ($apps as &$app)
		{

			// Get stats from raw collection data
			$stats_raw = MongoX::command(array(
				'collStats' => 'event_' . $app['appkey']
			));
			$stats = array();
			$app['records'] = (int) $stats_raw['count'];
			$app['rps'] = 0;
			$app['rps'] = (float) MongoX::selectCollection('event_' . $app['appkey'])
				->find(
					array('t' => array('$gte' => new MongoDate(time() - 60)))
				,	array('_id' => 0, 't' => 1)
					)
				->count() / 60;
			$app['storage'] = (int) $stats_raw['size'];
			$app['storage_avg'] = (int) $stats_raw['avgObjSize']; 

			// Calculate totals
			$totals['records'] += $app['records'];
			$totals['rps'] += $app['rps'];
			$totals['storage'] += $app['storage'];
		}
		array_sort($apps, '!records');
		$this->set('totals', $totals);
		$this->set('apps', $apps);
		
	}
	
}