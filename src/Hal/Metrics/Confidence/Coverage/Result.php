<?php

/*
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hal\Metrics\Confidence\Coverage;

use Hal\Component\Result\ExportableInterface;

/**
 * Representation of Coverage  result
 *
 * @author Loïc Doubinine <ztec@riper.fr>
 */
class Result implements ExportableInterface
{
    protected $coveredElements = 0;
    protected $coveredElementsPercent = 100;
    protected $coveredStatement = 0;
    protected $coveredStatementPercent = 100;
    protected $coveredMethod = 0;
    protected $coveredMethodPercent = 100;
    protected $noCoverageData = false;
    protected $CRAP = 0;


    /**
     * Represents result as array
     *
     * @return array
     */
    public function asArray()
    {

        return array(
            'coveredElements'         => $this->coveredElements,
            'coveredElementsPercent'  => $this->coveredElementsPercent,
            'coveredMethods'          => $this->coveredMethod,
            'coveredMethodsPercent'   => $this->coveredMethodPercent,
            'coveredStatement'        => $this->coveredStatement,
            'coveredStatementPercent' => $this->coveredStatementPercent,
            'CRAP'                    => $this->CRAP,
        );

    }


    public function setCoveredElements($value)
    {
        $this->coveredElements = $value;
    }

    public function setCoveredElementsPercent($value)
    {
        $this->coveredElementsPercent = $value;
    }

    public function setCoveredStatement($value)
    {
        $this->coveredStatement = $value;
    }

    public function setCoveredStatementPercent($value)
    {
        $this->coveredStatementPercent = $value;
    }

    public function setCoveredMethod($value)
    {
        $this->coveredMethod = $value;
    }

    public function setCoveredMethodPercent($value)
    {
        $this->coveredMethodPercent = $value;
    }

    public function setNoCoverageData($bool)
    {
        $this->noCoverageData = $bool;
    }

    /**
     * @return int
     */
    public function getCoveredElements()
    {
        return $this->coveredElements;
    }

    /**
     * @return int
     */
    public function getCoveredElementsPercent()
    {
        return $this->coveredElementsPercent;
    }

    /**
     * @return int
     */
    public function getCoveredMethod()
    {
        return $this->coveredMethod;
    }

    /**
     * @return int
     */
    public function getCoveredMethodPercent()
    {
        return $this->coveredMethodPercent;
    }

    /**
     * @return int
     */
    public function getCoveredStatement()
    {
        return $this->coveredStatement;
    }

    /**
     * @return int
     */
    public function getCoveredStatementPercent()
    {
        return $this->coveredStatementPercent;
    }

    /**
     * @return boolean
     */
    public function isNoCoverageData()
    {
        return $this->noCoverageData;
    }

    /**
     * @return int
     */
    public function getCRAP()
    {
        return $this->CRAP;
    }

    /**
     * @param int $CRAP
     */
    public function setCRAP($CRAP)
    {
        $this->CRAP = $CRAP;
    }


}