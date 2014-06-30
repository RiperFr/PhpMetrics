<?php

namespace Hal\Metrics\Confidence\ConfidenceIndex;


use Hal\Component\OOP\Extractor\ClassMap;

class ConfidenceIndex
{

    protected $coverageReport;
    protected $classMap;


    const METRIC_CONSTANT_COVERAGE = 0.025;
    const METRIC_CONSTANT_LOGICAL_LOC = 0.01;
    const METRIC_CONSTANT_CYCLOMATIC_COMPLEXITY = 0.07;
    const METRIC_CONSTANT_HALSTEAD_VOLUME = 0.00005;

    public function calculate(
        \Hal\Metrics\Confidence\Coverage\Result $rCoverage,
        \Hal\Metrics\Complexity\Text\Halstead\Result $rHalstead,
        \Hal\Metrics\Complexity\Text\Length\Result $rLoc,
        \Hal\Metrics\Complexity\Component\McCabe\Result $rMcCabe
    ) {
        $result = new Result;

        $metrics                                  = array();
        $metrics["halsteadVolumeConfidenceIndex"] = $this->calc(
            $rHalstead->getVolume(),
            array(0, 300, 3000),
            1,
            self::METRIC_CONSTANT_HALSTEAD_VOLUME
        );


        $metrics["cyclomaticComplexityConfidenceIndex"] = $this->calc(
            $rMcCabe->getCyclomaticComplexityNumber(),
            array(1, 10, 60),
            8,
            self::METRIC_CONSTANT_CYCLOMATIC_COMPLEXITY
        );


        $metrics["logicalLocConfidenceIndex"] = $this->calc(
            $rLoc->getLogicalLoc(),
            array(1, 50, 500),
            6,
            self::METRIC_CONSTANT_LOGICAL_LOC
        );

        //if (!$rCoverage->isNoCoverageData()) {
        $metrics["coverageConfidenceIndex"] = 150-$this->calc(
            $rCoverage->getCoveredElementsPercent(),
            array(-50, 85, 100),
            8,
            self::METRIC_CONSTANT_COVERAGE
        );
        $result->setCoverageConfidenceIndex($metrics['coverageConfidenceIndex']);
        //}

        $max = count($metrics);

        $result->setLogicalLocConfidenceIndex($metrics['logicalLocConfidenceIndex']);
        $result->setCyclomaticComplexityConfidenceIndex($metrics['cyclomaticComplexityConfidenceIndex']);
        $result->setHalsteadVolumeConfidenceIndex($metrics['halsteadVolumeConfidenceIndex']);


        $confidenceIndex = array_sum($metrics) / $max;
        $confidenceIndex = min(100,$confidenceIndex);

        $result->setConfidenceIndex(round($confidenceIndex, 0));


        return $result;
    }


    /**
     * calculate : -tanh( (x-u) * (c*a) )
     *
     * @param int   $metricValue    The value you make the calc for
     * @param int   $threshold      The threshold where the value has no impact (return 0)
     * @param float $variation      The Decrease/increase rate (Between 0 & 100)
     *                              below 1 Will reduce the impact of the metric (Will not start & end at 1/-1)
     *                              More that 1 will make the change quick.
     * @param float $metricConstant The Constant depending of the Rang of you metrics
     *                              Each metrics should have it's own
     *                              This constant is adjusted to have a result of -1 for the worst value of the metrics
     *                              we should find. Value above this one will stay at 1
     *
     * @return float    The result of the calc between -1 & 1
     *
     */
    protected function calc($metricValue, array $threshold, $variation = 1)
    {
        //Normalisation of the metrics value
        $const  = ($variation);
        $max       = $threshold[2];
        $min       = $threshold[0];
        $threshold = $threshold[1];


        //To normalize, we use bounds to determine constant factor and adjustment.
        //According to the threshold and variation speed

        $normalizedMetric = ($metricValue-$min) /($max-$min);
        $normalizedThreshold = ($threshold-$min) /($max-$min);

        $normalizedMetric = min(1,$normalizedMetric);
        $normalizedThreshold = min(1,$normalizedThreshold);


        $x = 1 /
            (
                tanh($const * (1- $normalizedThreshold))
                + tanh($const*$normalizedThreshold)
            );
        $y = tanh($const * $normalizedThreshold)
            /
            (
                tanh($const *(1-$normalizedThreshold))
                + tanh($const* $normalizedThreshold)
            );


        $result =  (tanh(($normalizedMetric-0.5 - ($normalizedThreshold-0.5)) * $const)) * $x + $y;
        $result = 100- $result * 100 ;



        return round($result, 0);
    }
}