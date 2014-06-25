<?php

/*
 * (c) Jean-François Lépine <https://twitter.com/Halleck45>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hal\Application\Command;
use Hal\Application\Command\Job\CoverageAnalyze;
use Hal\Application\Formater\Violations\Xml;
use Hal\Component\Aggregator\DirectoryAggregatorFlat;
use Hal\Component\Bounds\Bounds;
use Hal\Application\Command\Job\DoAnalyze;
use Hal\Application\Command\Job\Queue;
use Hal\Application\Command\Job\ReportRenderer;
use Hal\Application\Command\Job\ReportWriter;
use Hal\Application\Command\Job\SearchBounds;
use Hal\Component\File\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Hal\Application\Formater\Summary;
use Hal\Application\Formater\Details;
use Hal\Application\Command\Job\DoAggregatedAnalyze;

/**
 * Command for run analysis
 *
 * @author Jean-François Lépine <https://twitter.com/Halleck45>
 */
class RunMetricsCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
                ->setName('metrics')
                ->setDescription('Run analysis')
                ->addArgument(
                    'path', InputArgument::REQUIRED, 'Path to explore'
                )
                ->addOption(
                    'report-html',null, InputOption::VALUE_REQUIRED, 'Path to save report in HTML format. Example: /tmp/report.html'
                )
                ->addOption(
                    'report-xml', null, InputOption::VALUE_REQUIRED, 'Path to save summary report in XML format. Example: /tmp/report.xml'
                )
                ->addOption(
                    'coverage-report-xml', null, InputOption::VALUE_REQUIRED, 'Path to a PhpUnitXml coverage report. If present, coverage related metrics will be generated  Example: /tmp/coverage.xml'
                )
                ->addOption(
                    'violations-xml', null, InputOption::VALUE_REQUIRED, 'Path to save violations in XML format. Example: /tmp/report.xml'
                )
                ->addOption(
                    'report-csv', null, InputOption::VALUE_REQUIRED, 'Path to save summary report in CSV format. Example: /tmp/report.csv'
                )
                ->addOption(
                    'level', null, InputOption::VALUE_REQUIRED, 'Depth of summary report', 0
                )
                ->addOption(
                    'extensions', null, InputOption::VALUE_REQUIRED, 'Regex of extensions to include', 'php'
                )
                ->addOption(
                    'excludedDirs', null, InputOption::VALUE_REQUIRED, 'Regex of subdirectories to exclude', 'Tests|Features'
                )
                ->addOption(
                    'without-oop', null, InputOption::VALUE_NONE, 'If provided, tool will not extract any informations about OOP model (faster)'
                )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('PHPMetrics by Jean-François Lépine <https://twitter.com/Halleck45>');
        $output->writeln('');

        $level = $input->getOption('level');

        // files
        $finder = new Finder(
            $input->getOption('extensions'),
            $input->getOption('excludedDirs')
        );

        // rules
        $rules = new \Hal\Application\Rule\RuleSet();
        $validator = new \Hal\Application\Rule\Validator($rules);

        // bounds
        $bounds = new Bounds;

        $analyzer = new DoAnalyze($output, $finder, $input->getArgument('path'), !$input->getOption('without-oop'));
        $analyzer->setCoverageReportFile($input->getOption('coverage-report-xml'));

        // jobs queue planning
        $queue = new Queue();
        $queue
            ->push($analyzer)
            ->push(new SearchBounds($output, $bounds))
            ->push(new DoAggregatedAnalyze($output, new DirectoryAggregatorFlat($level)))
            ->push(new ReportRenderer($output, new Summary\Cli($validator, $bounds)))
            ->push(new ReportWriter($input->getOption('report-html'), $output, new Summary\Html($validator, $bounds)))
            ->push(new ReportWriter($input->getOption('report-xml'), $output, new Summary\Xml($validator, $bounds)))
            ->push(new ReportWriter($input->getOption('report-csv'), $output, new Details\Csv($validator, $bounds)))
            ->push(new ReportWriter($input->getOption('violations-xml'), $output, new Xml($validator, $bounds)))
            ;

        // execute
        $collection = new \Hal\Component\Result\ResultCollection();
        $aggregatedResults = new \Hal\Component\Result\ResultCollection();
        $queue->execute($collection, $aggregatedResults);

        $output->writeln('<info>done</info>');

        return 0;
    }

}
