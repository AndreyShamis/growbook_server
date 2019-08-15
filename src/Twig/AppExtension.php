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
            new TwigFilter('ExecutionTimeWithDays', array($this, 'ExecutionTimeWithDays')),
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
            new TwigFilter('humidityToBadge', [$this, 'humidityToBadge']),
            new TwigFilter('humidityToInfoBox', [$this, 'humidityToInfoBox']),
            new TwigFilter('hydrometerToInfoBox', [$this, 'hydrometerToInfoBox']),
            new TwigFilter('temperatureToBadge', [$this, 'temperatureToBadge']),
            new TwigFilter('temperatureToInfoBox', [$this, 'temperatureToInfoBox']),
            new TwigFilter('hydrometerToBadge', [$this, 'hydrometerToBadge']),
            new TwigFilter('lightToBadge', [$this, 'lightToBadge']),
            new TwigFilter('SensorTypeShort', [$this, 'SensorTypeShort']),
            new TwigFilter('relativeTime', [$this, 'relativeTime']),
            new TwigFilter('hoursInRelativeTime', [$this, 'hoursInRelativeTime']),
        );
    }

    /**
     * Define TWIG function
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ExecutionTimeWithDays', array($this, 'ExecutionTimeWithDays')),
            new TwigFunction('ExecutionTimeGeneric', array($this, 'ExecutionTimeGeneric')),
            new TwigFunction('executionTimeGenericShort', array($this, 'executionTimeGenericShort')),
            new TwigFunction('relativeTime', array($this, 'relativeTime')),
            new TwigFunction('shortString', [$this, 'shortString']),
            new TwigFunction('isUrl', [$this, 'isUrl']),
            new TwigFunction('formatBytes', [$this, 'formatBytes']),
            new TwigFunction('getPercentage', [$this, 'getPercentage']),
            new TwigFunction('inarray', array($this, 'inArray')),
            new TwigFunction('humidityToBadge', [$this, 'humidityToBadge']),
            new TwigFunction('humidityToInfoBox', [$this, 'humidityToInfoBox']),
            new TwigFunction('hydrometerToInfoBox', [$this, 'hydrometerToInfoBox']),
            new TwigFunction('temperatureToBadge', [$this, 'temperatureToBadge']),
            new TwigFunction('temperatureToInfoBox', [$this, 'temperatureToInfoBox']),
            new TwigFunction('hydrometerToBadge', [$this, 'hydrometerToBadge']),
            new TwigFunction('lightToBadge', [$this, 'lightToBadge']),
            new TwigFunction('SensorTypeShort', [$this, 'SensorTypeShort']),
            new TwigFunction('relativeTime', [$this, 'relativeTime']),
            new TwigFunction('hoursInRelativeTime', [$this, 'hoursInRelativeTime']),
        ];
    }

    /**
     * @param null $input
     * @return string
     */
    public function SensorTypeShort($input=null): string
    {
        $ret = 'Unknown';
        if ($input === 'Humidity') {
            $ret = 'Hum';
        } elseif ($input === 'Temperature') {
            $ret = 'Temp';
        } elseif ($input === 'SoilHydrometer') {
            $ret = 'Hyd';
        } elseif ($input === null) {
            $ret = 'None';
        }
        return $ret;
    }

    /**
     * @param int $value
     * @param int $period
     * @return string
     */
    public function humidityToBadge(int $value=50, $period=1): string
    {
        $ret = '';
        if ($value <= 0 || $value > 100) {
            $ret = 'badge-secondary';
        } else if($value < 10 || $value > 90) {
            $ret = 'badge-danger';
        } else {
            if ($period === 1) {
                if ($value < 30) {
                    $ret = 'badge-danger';
                } elseif ($value < 40 || $value > 85) {
                    $ret = 'badge-warning';
                } elseif ($value < 50 || $value > 80) {
                    $ret = 'badge-info';
                } elseif ($value >= 50 && $value <= 80) {
                    $ret = 'badge-success';
                } else {
                    $ret = 'badge-danger';
                }
            } elseif ($period === 2) {
                if ($value > 70) {
                    $ret = 'badge-danger';
                } elseif ($value < 25 || $value > 65) {
                    $ret = 'badge-warning';
                } elseif ($value < 30 || $value > 50) {
                    $ret = 'badge-info';
                } elseif ($value >= 30 && $value <= 50) {
                    $ret = 'badge-success';
                } else {
                    $ret = 'badge-danger';
                }
            }
        }

        return $ret;
    }


    /**
     * @param int $value
     * @param int $period
     * @return string
     */
    public function humidityToInfoBox(int $value=50, $period=1): string
    {
        $ret = '';
        if ($value <= 0 || $value > 100) {
            $ret = 'infobox-black';
        } else if($value < 10 || $value > 90) {
            $ret = 'infobox-red';
        } else {
            if ($period === 1) {
                if ($value < 30) {
                    $ret = 'infobox-red';
                } elseif ($value < 40 || $value > 85) {
                    $ret = 'infobox-orange';
                } elseif ($value < 50 || $value > 80) {
                    $ret = 'infobox-blue';
                } elseif ($value >= 50 && $value <= 80) {
                    $ret = 'infobox-green';
                } else {
                    $ret = 'infobox-red';
                }
            } elseif ($period === 2) {
                if ($value > 70) {
                    $ret = 'infobox-red';
                } elseif ($value < 25 || $value > 65) {
                    $ret = 'infobox-orange';
                } elseif ($value < 30 || $value > 50) {
                    $ret = 'infobox-blue';
                } elseif ($value >= 30 && $value <= 50) {
                    $ret = 'infobox-green';
                } else {
                    $ret = 'infobox-red';
                }
            }
        }

        return $ret;
    }

    /**
     * @param int $value
     * @param int $period
     * @return string
     */
    public function temperatureToBadge(int $value=25, $period=1): string
    {
        $ret = '';
        if ($value <= 0) {
            $ret = 'badge-secondary';
        } else {
            if ($value < 20 || $value > 33) {
                $ret = 'badge-danger';
            } elseif ($value < 22 || $value > 31) {
                $ret = 'badge-warning';
            } elseif ($value < 24 || $value > 28) {
                $ret = 'badge-info';
            } elseif ($value >= 24 && $value <= 28) {
                $ret = 'badge-success';
            } else {
                $ret = 'badge-danger';
            }
        }

        return $ret;
    }

    /**
     * @param int $value
     * @param int $period
     * @return string
     */
    public function temperatureToInfoBox(int $value=25, $period=1): string
    {
        $ret = '';
        if ($value <= 0) {
            $ret = 'infobox-black';
        } else {
            if ($value < 20 || $value > 33) {
                $ret = 'infobox-red';
            } elseif ($value < 22 || $value > 31) {
                $ret = 'infobox-orange';
            } elseif ($value < 24 || $value > 28) {
                $ret = 'infobox-blue';
            } elseif ($value >= 24 && $value <= 28) {
                $ret = 'infobox-green';
            } else {
                $ret = 'infobox-red';
            }
        }

        return $ret;
    }

    /**
     * @param int $value
     * @return string
     */
    public function hydrometerToInfoBox(int $value=50): string
    {
        $ret = '';
        if ($value < 0) {
            $ret = 'infobox-black';
        } else {
            if ($value < 7 || $value > 60) {
                $ret = 'infobox-red';
            } elseif ($value < 16 || $value > 55) {
                $ret = 'infobox-orange';
            } elseif ($value < 20 || $value > 45) {
                $ret = 'infobox-blue';
            } elseif ($value >= 20 && $value <= 45) {
                $ret = 'infobox-green';
            } else {
                $ret = 'infobox-red';
            }
        }

        return $ret;
    }

    /**
     * @param int $value
     * @return string
     */
    public function hydrometerToBadge(int $value=50): string
    {
        $ret = '';
        if ($value < 0) {
            $ret = 'badge-secondary';
        } else {
            if ($value < 7 || $value > 60) {
                $ret = 'badge-danger';
            } elseif ($value < 16 || $value > 55) {
                $ret = 'badge-warning';
            } elseif ($value < 20 || $value > 45) {
                $ret = 'badge-info';
            } elseif ($value >= 20 && $value <= 45) {
                $ret = 'badge-success';
            } else {
                $ret = 'badge-danger';
            }
        }
        return $ret;
    }
    /**
     * @param bool $value
     * @return string
     */
    public function lightToBadge(bool $value=false): string
    {
        $ret = 'badge-dark';
        if ($value) {
            $ret = 'badge-warning';
        }
        return $ret;
    }

    public function parseType(string $typeFull=null): string
    {
        return str_replace(array('App\Entity\Events\Event', 'App\Entity\\'), '', $typeFull);
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


    public function hoursInRelativeTime(\DateTime $timeStart, \DateTime $current): string
    {
        $time = abs($timeStart->getTimestamp() - $current->getTimestamp());
        $hours = number_format (round($time/60/60, 1), 1);
        return $hours;
    }

    /**
     * @param $time
     * @return string
     */
    public function executionTimeGenericShort(int $time): string
    {
        $seconds = $time%60;
        $minutes = ($time/60)%60;
        $hours = number_format (floor($time/60/60));
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
     * @return string
     */
    public function ExecutionTimeWithDays(int $time): string
    {
        $seconds = $time%60;
        $minutes = ($time/60)%60;
        $hours = floor($time/60/60);
        $hour_print = sprintf('%dh', $hours);
        $min_print = sprintf('%02dm', $minutes);
        $sec_print = sprintf('%02ds', $seconds);
        if ($hours > 0) {
            if ($hours > 24) {
                $days = floor($time/60/60/24);
                $hours = floor(($time/60/60)%24);
                $hour_print = sprintf('%dh', $hours);
                $days_print = sprintf('%dd', $days);
                $ret = sprintf('%s %s %s %s', $days_print, $hour_print, $min_print, $sec_print);
            } else {
                $ret = sprintf('%s %s %s', $hour_print, $min_print, $sec_print);
            }
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
