<?php
namespace Hal\Metrics\Confidence\ConfidenceIndex ;

interface NormalizerInterface {


    /**
     * @param $metricValue
     *
     * @return mixed
     */
    public function normalize($metricValue);
}