<?php

namespace Hal\Metrics\Confidence\Coverage;

class PhpunitXmlReport
{

    /**
     * @var string
     */
    protected $reportFile;
    /**
     * @var \DOMDocument
     */
    protected $reportDom;
    /**
     * @var \DOMXPath
     */
    protected $reportXpath;

    public function __construct($xmlReportFile)
    {
        $this->reportFile = $xmlReportFile;
    }


    protected function loadReportDom()
    {
        if ($this->reportDom === null) {
            $this->reportDom = new \DOMDocument();
            $this->reportDom->load($this->reportFile);
            $this->reportXpath = new \DOMXPath($this->reportDom);
        }
    }

    public function calculate($fileName)
    {
        $fileName = realpath($fileName);
        $this->loadReportDom();

        $xpath   = $this->reportXpath;
        $classes = $xpath->query("//file[@name='" . $fileName . "']/class"); //Search for all class nodes in the file
        $return  = array();
        foreach ($classes as $class) {
            $fileNode               = $class->parentNode; //The fileNode is parent of a class as a class is in a file
            $classData              = array();
            $classData['className'] = $class->getAttribute('name');
            $classData['namespace'] = $class->getAttribute('namespace');
            $classData['file']      = $fileNode->getAttribute('name');

            //Get all metrics of the class
            $metrics = $class->getElementsByTagName('metrics');

            $classData['metrics'] = array();
            foreach ($metrics as $metric) {
                $classData['metrics']['methods']           = $metric->getAttribute('methods');
                $classData['metrics']['coveredMethods']    = $metric->getAttribute('coveredmethods');
                $classData['metrics']['statements']        = $metric->getAttribute('statements');
                $classData['metrics']['coveredStatements'] = $metric->getAttribute('coveredstatements');
                $classData['metrics']['elements']          = $metric->getAttribute('elements');
                $classData['metrics']['coveredElements']   = $metric->getAttribute('coveredelements');
                $classData['metrics']['coveredElementsPercent']
                                                           =
                    ($classData['metrics']['coveredElements'] / $classData['metrics']['elements']) * 100;
                $classData['metrics']['coveredMethodsPercent']
                                                           =
                    ($classData['metrics']['coveredMethods'] / $classData['metrics']['methods']) * 100;
                $classData['metrics']['coveredStatementsPercent']
                                                           =
                    ($classData['metrics']['coveredStatements'] / $classData['metrics']['statements']) * 100;
            }
            $classData['metrics']['crap'] = 0;
            $methods                      = $xpath->query(".//line[@type='method']", $fileNode);
            $classData['methods']         = array();
            foreach ($methods as $method) {
                $classData['methods'][$method->getAttribute('name')] = array(
                    'name'  => $method->getAttribute('name'),
                    'crap'  => $method->getAttribute('crap'),
                    'count' => $method->getAttribute('count'),
                    'line'  => $method->getAttribute('num'),
                );
                $classData['metrics']['crap']
                                                                     =
                    $classData['metrics']['crap'] + $method->getAttribute('crap');
            }
            if (count($classData['methods']) > 0) {
                $classData['metrics']['crap'] = $classData['metrics']['crap'] / count($classData['methods']);
            }

            $return = $classData;
        }

        return $return;
    }
}