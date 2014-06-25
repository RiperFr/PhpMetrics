<?php

/*
 * (c) Jean-François Lépine <https://twitter.com/Halleck45>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hal\Metrics\Confidence\ConfidenceIndex;

use Hal\Component\Result\ExportableInterface;

/**
 * Representation of LCOM result
 *
 * @author Jean-François Lépine <https://twitter.com/Halleck45>
 */
class Result implements ExportableInterface
{
    protected $confidenceIndex;
    protected $logicalLocConfidenceIndex;
    protected $cyclomaticComplexityConfidenceIndex;
    protected $coverageConfidenceIndex = 'n/a';
    protected $halsteadVolumeConfidenceIndex;

    /**
     * Represents result as array
     *
     * @return array
     */
    public function asArray()
    {
        return array(
            'confidenceIndex'                     => $this->confidenceIndex,
            'coverageConfidenceIndex'             => $this->coverageConfidenceIndex,
            'cyclomaticComplexityConfidenceIndex' => $this->cyclomaticComplexityConfidenceIndex,
            'halsteadVolumeConfidenceIndex'       => $this->halsteadVolumeConfidenceIndex,
            'logicalLocConfidenceIndex'           => $this->logicalLocConfidenceIndex,
        );
    }

    /**
     * @return mixed
     */
    public function getConfidenceIndex()
    {
        return $this->confidenceIndex;
    }

    /**
     * @param mixed $confidenceIndex
     */
    public function setConfidenceIndex($confidenceIndex)
    {
        $this->confidenceIndex = $confidenceIndex;
    }

    /**
     * @return mixed
     */
    public function getCoverageConfidenceIndex()
    {
        return $this->coverageConfidenceIndex;
    }

    /**
     * @param mixed $coverageConfidenceIndex
     */
    public function setCoverageConfidenceIndex($coverageConfidenceIndex)
    {
        $this->coverageConfidenceIndex = $coverageConfidenceIndex;
    }

    /**
     * @return mixed
     */
    public function getCyclomaticComplexityConfidenceIndex()
    {
        return $this->cyclomaticComplexityConfidenceIndex;
    }

    /**
     * @param mixed $cyclomaticComplexityConfidenceIndex
     */
    public function setCyclomaticComplexityConfidenceIndex($cyclomaticComplexityConfidenceIndex)
    {
        $this->cyclomaticComplexityConfidenceIndex = $cyclomaticComplexityConfidenceIndex;
    }

    /**
     * @return mixed
     */
    public function getHalsteadVolumeConfidenceIndex()
    {
        return $this->halsteadVolumeConfidenceIndex;
    }

    /**
     * @param mixed $halsteadVolumeConfidenceIndex
     */
    public function setHalsteadVolumeConfidenceIndex($halsteadVolumeConfidenceIndex)
    {
        $this->halsteadVolumeConfidenceIndex = $halsteadVolumeConfidenceIndex;
    }

    /**
     * @return mixed
     */
    public function getLogicalLocConfidenceIndex()
    {
        return $this->logicalLocConfidenceIndex;
    }

    /**
     * @param mixed $logicalLocConfidenceIndex
     */
    public function setLogicalLocConfidenceIndex($logicalLocConfidenceIndex)
    {
        $this->logicalLocConfidenceIndex = $logicalLocConfidenceIndex;
    }

}