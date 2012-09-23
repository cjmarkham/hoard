<?php

class StatsController extends PageController {

	public $periods = array(
		'second'	=> array(1, 'i', 30),
		'minute'	=> array(60, 'H:i', 30),
		'hour'		=> array(3600, 'H', 6), 
		'day'		=> array(86400, 'm/d', 7)
	);
	public $period = 'minute';

	public function req_get ()
	{

		$track = MongoX::selectCollection('event');
		$stats['all'] = array();

		// Vars
		if ($_GET['period'] && array_key_exists($_GET['period'], $this->periods))
		{
			$this->period = $_GET['period'];
		}
		$interval = $this->periods[$this->period][0];
		$time = time();
		$now = $time - ($time % $interval) + $interval;

		// Build Data Array
		$inc = 0;
		$max = $this->periods[$this->period][2] ? $this->periods[$this->period][2] : 30;
		$decr = 1;
		$columns = array();
		foreach ($stats as $key => $data)
		{
			$inc++;
			for ($i = $max; $i >= 0; $i -= $decr)
			{
				$start = $now - ($interval * ($i + $decr));
				$end = $now - ($interval * $i);
				$columns[] = date($this->periods[$this->period][1], $start);
				$cache_key = 'data-' . $period . '-' . $key . '-' . $i . '-' . $start . '-' . $end;
				$count = false;
				if ($count === false)
				{
					$array = array_merge(
						array('date' => array('$gt' => new MongoDate($start), '$lte' => new MongoDate($end))),
						$data
						);
					$count = $track->find($array, array('_id' => 0))->count();
					// set cache
				}
				$csv[$key][] = $count;
			}
		}

		// Output
		header('Content-type: text/plain');
		foreach ($columns as $column)
		{
			echo ',' . $column;
		}
		foreach ($csv as $key => $data)
		{
			echo "\n";
			echo $key;
			foreach ($data as $count)
			{
				echo ',' . $count;
			}
		}
		exit;

	}

}