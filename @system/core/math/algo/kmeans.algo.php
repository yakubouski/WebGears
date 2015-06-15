<?php

namespace Math\Algo;
class KMeansSampleData {
    
    public $Data;
    public $K;
    public $cPositions;
    public $cClusters;
    public function __construct(&$Data,$K) {
	$this->Data = [];
	$this->K = 0;
	$this->cPositions = [];
	$this->cClusters = [];
    }
}
class KMeans {
    /**
     * Расчитать кластеры
     * @param type $Data
     * @param type $K
     * @return \Math\Algo\KMeansSampleData
     */
    public function Calculate($Data, $K) {
	$Sample = new KMeansSampleData($Data,$K);
	$this->initial_positions($Sample);
	while (true) {
	    $changes = $this->clustering($Sample);
	    if (!$changes) {
		return $this->get_cluster_values($Sample);
	    }
	    $this->recalculate_cpositions($Sample);
	}
	return $Sample;
    }

    private function clustering(KMeansSampleData &$Sample) {
	$nChanges = 0;
	foreach ($Sample->Data as $dataKey => $value) {
	    $minDistance = null;
	    $cluster = null;
	    foreach ($Sample->cPositions as $k => $position) {
		$distance = $this->distance($value, $position);
		if (is_null($minDistance) || $minDistance > $distance) {
		    $minDistance = $distance;
		    $cluster = $k;
		}
	    }
	    if (!isset($Sample->cClusters[$dataKey]) || $Sample->cClusters[$dataKey] != $cluster) {
		$nChanges++;
	    }
	    $Sample->cClusters[$dataKey] = $cluster;
	}
	return $nChanges;
    }

    function recalculate_cpositions(KMeansSampleData &$Sample,$cPositions, $data, $clusters) {
	$kValues = $this->get_cluster_values($clusters, $data);
	foreach ($Sample->cPositions as $k => $position) {
	    $Sample->cPositions[$k] = empty($kValues[$k]) ? 0 : $this->avg($kValues[$k]);
	}
    }

    function get_cluster_values(KMeansSampleData &$Sample) {
	$values = array();
	foreach ($Sample->cClusters as $dataKey => $cluster) {
	    $values[$cluster][] = $Sample->Data[$dataKey];
	}
	return $values;
    }

    private function avg($values) {
	$n = count($values);
	$sum = array_sum($values);
	return ($n == 0) ? 0 : $sum / $n;
    }

    private function distance($v1, $v2) {
	return abs($v1 - $v2);
    }

    private function initial_positions(KMeansSampleData &$Sample) {
	$min = min($Sample->Data);
	$max = max($Sample->Data);
	$int = ceil(abs($max - $min) / $this->K);
	$k = $Sample->K;
	while ($k-- > 0) {
	    $Sample->cPositions[$k] = $min + $int * $k;
	}
    }
}

/*
 function kmeans($data, $k)
{
        $cPositions = assign_initial_positions($data, $k);
        $clusters = array();
        while(true)
        {
                $changes = kmeans_clustering($data, $cPositions, $clusters);
                if(!$changes)
                {
                        return kmeans_get_cluster_values($clusters, $data);
                }
                $cPositions = kmeans_recalculate_cpositions($cPositions, $data, $clusters);
        }
}
function kmeans_clustering($data, $cPositions, &$clusters)
{
        $nChanges = 0;
        foreach($data as $dataKey => $value)
        {
                $minDistance = null;
                $cluster = null;
                foreach($cPositions as $k => $position)
                {
                        $distance = distance($value, $position);
                        if(is_null($minDistance) || $minDistance > $distance)
                        {
                                $minDistance = $distance;
                                $cluster = $k;
                        }
                }
                if(!isset($clusters[$dataKey]) || $clusters[$dataKey] != $cluster)
                {
                        $nChanges++;
                }
                $clusters[$dataKey] = $cluster;
        }
        return $nChanges;
}
function kmeans_recalculate_cpositions($cPositions, $data, $clusters)
{
        $kValues = kmeans_get_cluster_values($clusters, $data);
        foreach($cPositions as $k => $position)
        {
                $cPositions[$k] = empty($kValues[$k]) ? 0 : kmeans_avg($kValues[$k]);
        }
        return $cPositions;
}
function kmeans_get_cluster_values($clusters, $data)
{
        $values = array();
        foreach($clusters as $dataKey => $cluster)
        {
                $values[$cluster][] = $data[$dataKey];
        }
        return $values;
}
function kmeans_avg($values)
{
        $n = count($values);
        $sum = array_sum($values);
        return ($n == 0) ? 0 : $sum / $n;
}
function distance($v1, $v2)
{
  return abs($v1-$v2);
}
function assign_initial_positions($data, $k)
{
        $min = min($data);
        $max = max($data);
        $int = ceil(abs($max - $min) / $k);
        while($k-- > 0)
        {
                $cPositions[$k] = $min + $int * $k;
        }
        return $cPositions;
}
 */