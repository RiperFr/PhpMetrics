<?php

namespace Hal\Metrics\Confidence\Coverage;


use Hal\Component\OOP\Extractor\ClassMap;

class Coverage
{

    protected $coverageReport;
    protected $classMap;

    /**
     * @param $coverageReport
     */
    public function __construct(PhpunitXmlReport $coverageReport)
    {
        $this->coverageReport = $coverageReport;
    }


    public function calculate($filename)
    {
        $filename = realpath($filename);

        $stats = $this->coverageReport->calculate($filename);


        $Result = new Result();

        if (isset($stats['metrics'])) {
            $Result->setCoveredElements($stats['metrics']['coveredElements']);
            $Result->setCoveredElementsPercent(round($stats['metrics']['coveredElementsPercent'],2));
            $Result->setCoveredStatement($stats['metrics']['coveredStatements']);
            $Result->setCoveredStatementPercent(round($stats['metrics']['coveredStatementsPercent'],2));
            $Result->setCoveredMethod($stats['metrics']['coveredMethods']);
            $Result->setCoveredMethodPercent(round($stats['metrics']['coveredMethodsPercent'],2));
            $Result->setCRAP($stats['metrics']['crap']);
        } else {
            $Result->setNoCoverageData(true);
        }

        return $Result;
    }
}