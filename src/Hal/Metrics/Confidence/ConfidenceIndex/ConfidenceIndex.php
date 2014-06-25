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
                300,
                10,
                self::METRIC_CONSTANT_HALSTEAD_VOLUME
            ) + 1;


        $metrics["cyclomaticComplexityConfidenceIndex"] = $this->calc(
                $rMcCabe->getCyclomaticComplexityNumber(),
                10,
                1,
                self::METRIC_CONSTANT_CYCLOMATIC_COMPLEXITY
            ) + 1;


        $metrics["logicalLocConfidenceIndex"] = $this->calc(
                $rLoc->getLogicalLoc(),
                3,
                1,
                self::METRIC_CONSTANT_LOGICAL_LOC
            ) + 1;

        //if (!$rCoverage->isNoCoverageData()) {
            $metrics["coverageConfidenceIndex"] = 0 - $this->calc(
                    $rCoverage->getCoveredElementsPercent(),
                    20,
                    1,
                    self::METRIC_CONSTANT_COVERAGE
                ) + 1;
            $result->setCoverageConfidenceIndex($metrics['coverageConfidenceIndex']);
        //}

        $max = count($metrics);

        $result->setLogicalLocConfidenceIndex($metrics['logicalLocConfidenceIndex']);
        $result->setCyclomaticComplexityConfidenceIndex($metrics['cyclomaticComplexityConfidenceIndex']);
        $result->setHalsteadVolumeConfidenceIndex($metrics['halsteadVolumeConfidenceIndex']);


        $confidenceIndex = array_sum($metrics) / $max;

        $result->setConfidenceIndex(round($confidenceIndex, 2));


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
    protected function calc($metricValue, $threshold, $variation, $metricConstant)
    {
        $result = 0 - tanh(($metricValue - $threshold) * ($metricConstant * $variation));

        //return $result;

        return round($result, 4);
    }
}