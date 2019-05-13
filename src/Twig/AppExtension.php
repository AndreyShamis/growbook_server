<?php

namespace App\Twig;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $parser;

    /**
     * Define twig filters
     * @return array|\Twig_Filter[]
     */
    public function getFilters(): array
    {
        return array(
            new TwigFilter('parseType', array($this, 'parseType')),
            new TwigFilter('ExecutionTimeInHours', array($this, 'ExecutionTimeInHours')),
            new TwigFilter('ExecutionTimeGeneric', array($this, 'ExecutionTimeGeneric')),
            new TwigFilter('executionTimeGenericShort', array($this, 'executionTimeGenericShort')),
            new TwigFilter('TimeToHour', array($this, 'TimeToHour')),
            new TwigFilter('getPercentage', array($this, 'getPercentage')),
            new TwigFilter('cast_to_array', array($this, 'cast_to_array')),
            new TwigFilter('pre_print_r', array($this, 'pre_print_r'), array('is_safe' => array('html'))),
            new TwigFilter('md2html', array($this, 'markdownToHtml'), array('is_safe' => array('html'))),
            new TwigFilter('time_ago', function ($time) { return $this->ExecutionTimeInHours(time() - $time);}),
            new TwigFilter('filter_name', [$this, 'doSomething'], ['is_safe' => ['html']]),
        );
    }

    /**
     * Define TWIG function
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ExecutionTimeGeneric', array($this, 'ExecutionTimeGeneric')),
            new TwigFunction('executionTimeGenericShort', array($this, 'executionTimeGenericShort')),
            new TwigFunction('relativeTime', array($this, 'relativeTime')),
            new TwigFunction('shortString', [$this, 'shortString']),
            new TwigFunction('isUrl', [$this, 'isUrl']),
            new TwigFunction('formatBytes', [$this, 'formatBytes']),
            new TwigFunction('getPercentage', [$this, 'getPercentage']),
            new TwigFunction('inarray', array($this, 'inArray')),
        ];
    }

    public function parseType(string $typeFull): string
    {
        return str_replace('App\Entity\Events\Event', '', $typeFull);
    }

    /**
     * @param int $size
     * @param int $precision
     * @return string
     */
    public function formatBytes($size = 0, $precision = 2): string
    {
        $base = log($size, 1024);
        $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');
        try {
            $suffix = $suffixes[(int)floor($base)];
        } catch (\Exception $ex) {
            $suffix = 'b';
        }
        try {
            if ($size > 0) {
                $value = 1024 ** ($base - floor($base));
            } else {
                $value = 0;
            }
        } catch (\Exception $ex) {
            $value = $size;
        }

        return round($value, $precision) . ' ' . $suffix;
    }

    /**
     * Receive input, if string len > len, cut the string to the len and return with postfix
     * @param string $input
     * @param int $len
     * @param string $postFix
     * @return string
     */
    public function shortString(string $input = null, int $len = 20, string $postFix = '...'): string
    {
        if ($input === null) {
            return '';
        }
        if (\strlen($input) > $len) {
            return substr($input, 0, $len) . $postFix;
        }
        return $input;
    }

    public function isUrl($string): bool
    {
        if (filter_var($string, FILTER_VALIDATE_URL)) {
            return true;
        }
        return false;
    }

    /**
     * @param mixed $variable
     * @param array $arr
     *
     * @return bool
     */
    public function inArray($variable, $arr): bool
    {
        return \in_array($variable, $arr, true);
    }

    /**
     * @param $obj
     * @return string
     */
    public function pre_print_r($obj): string
    {
        return '<pre>' . print_r($obj, true) . '</pre>';
    }

    /**
     * @param $stdClassObject
     * @return array
     * @throws ReflectionException
     */
    public function cast_to_array($stdClassObject): array
    {
        $array = array();
        try {
            $reflectionClass = new ReflectionClass(\get_class($stdClassObject));

            foreach ($reflectionClass->getProperties() as $property) {
                $property->setAccessible(true);
                $array[$property->getName()] = $property->getValue($stdClassObject);
                $property->setAccessible(false);
            }
        } catch (Exception  $ex) {
        }
        return $array;
    }

//
//    public function __construct(Markdown $parser = null)
//    {
//        $this->parser = $parser;
//    }

    /**
     * @param $valueOf
     * @param $valueFrom
     * @param int $precision
     * @return float|int
     */
    public function getPercentage($valueOf, $valueFrom, $precision = 2)
    {
        $ret = 0;
        try {
            if ($valueFrom>0) {
                $ret = ($valueOf*100)/$valueFrom;
            }
            $ret = round($ret,$precision);
        }
        catch (Exception $ex) {
        }
        return $ret;
    }

    /**
     * @param $time
     * @return string
     */
    public function ExecutionTimeInHours($time): string
    {
        $seconds  =   $time%60;
        $minutes  =   ($time/60)%60;
        $hours    =   number_format (floor($time/60/60));
        $min_print = sprintf('%02d', $minutes);
        if ($min_print === '00') {
            return ($hours . 'h');
        }
        return ($hours . 'h ' . sprintf('%02d', $minutes) . 'm');
    }

    /**
     * Print time diff for two datetimes
     * @param \DateTime $timeStart
     * @param \DateTime $current
     * @return string
     */
    public function relativeTime(\DateTime $timeStart, \DateTime $current): string
    {
        $diff = abs($timeStart->getTimestamp() - $current->getTimestamp());
        return $this->executionTimeGenericShort($diff);
    }

    /**
     * @param $time
     * @return string
     */
    public function executionTimeGenericShort(int $time): string
    {
        $seconds  =   $time%60;
        $minutes  =   ($time/60)%60;
        $hours    =   number_format (floor($time/60/60));
        $hour_print = sprintf('%d',$hours);
        $min_print = sprintf('%02d',$minutes);
        $sec_print = sprintf('%02d',$seconds);
        if ($hours > 0) {
            $ret = sprintf('%s:%s:%s', $hour_print, $min_print, $sec_print);
        } else {
            $ret = sprintf('%s:%s', $min_print, $sec_print);
        }
        return $ret;
    }

    /**
     * @param $time
     * @return string
     */
    public function ExecutionTimeGeneric(int $time): string
    {
        $seconds = $time%60;
        $minutes = ($time/60)%60;
        $hours = floor($time/60/60);
        $hour_print = sprintf('%dh', $hours);
        $min_print = sprintf('%02dm', $minutes);
        $sec_print = sprintf('%02ds', $seconds);
        if ($hours > 0) {
            $ret = sprintf('%s %s %s', $hour_print, $min_print, $sec_print);
        } else {
            $ret = sprintf('%s %s', $min_print, $sec_print);
        }
        return $ret;
    }

    /**
     * @param $time
     * @return integer
     */
    public function TimeToHour($time): int
    {
        $minutes  =   ($time/60)%60;
        $hours    =   floor($time/60/60);
//        if($minutes > 30){
//            $hours += 1;
//        }
        return $time;
    }

    public function markdownToHtml($content)
    {
        if ($this->parser === null) {
            $this->parser = new Markdown();
        }
        return $this->parser->toHtml($content);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName(): string
    {
        return 'twig_common';
    }
}
