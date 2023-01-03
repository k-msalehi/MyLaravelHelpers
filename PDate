<?php

/**
 * @package PDate
 * @author Mohammad Salehi Koleti <mohammadsk97@yahoo.com>
 * @see https://github.com/pars0097/PDate
 * @license https://opensource.org/licenses/lgpl-3.0.html LGPL 3
 * @description Persian Date Helper
 * valid outFormat values are documented at https://unicode-org.github.io/icu/userguide/format_parse/datetime/ .
 * valid inFormat values are documented at https://www.php.net/manual/en/datetime.format.php .
 */

namespace App\Helpers\PDate;

class PDate
{
    private $config =
    [
        'dateTime' => null,
        'y' => null,
        'm' => null,
        'd' => null,
        'h' => 0,
        'i' => 0,
        's' => 0,
        'inFormat' => 'Y/m/d H:i:s',
        'outFormat' => 'y-MM-dd HH:mm:ss',
        'local' => null,
        'calendar' => 'persian',
        'outTimeZone' => 'Asia/Tehran',
        'inTimeZone' => 'Asia/Tehran',
    ];

    public function getConfig()
    {
        return $this->config;
    }
    public function __construct($config = [])
    {
        if (empty($this->config['inTimeZone'])) {
            $this->config['inTimeZone'] = date_default_timezone_get();
        }
        $this->config = array_merge($this->config, $config);
    }
    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
    }
    private function _checkDate($config, $baseCalType)
    {
        $config = array_merge($this->config, $config);
        if (isset($config['dateTime'])) {
            if ($baseCalType == 'gregorian') {
                $time = \DateTime::createFromFormat($config['inFormat'], $config['dateTime'], new \DateTimeZone($config['inTimeZone']));
            } else {
                $this->str2date($config['dateTime']);
                $config = $this->config;
                $config['m']--;
                $time = \IntlCalendar::createInstance($config['inTimeZone'], "$config[local]@calendar=$baseCalType");
                $time->set((int) $config['y'], (int) $config['m'], (int) $config['d'], (int) $config['h'], (int) $config['i'], (int) $config['s']);
            }
        } else {
            if (empty($config['y']) || empty($config['m']) || empty($config['d'])) {
                return false;
                //throw new \Exception('if you left `$config[\'dateTime\']` null then y, m and d MUST has value');
            }
            //months num starts from 0
            $config['m']--;
            $time = \IntlCalendar::createInstance($config['inTimeZone'], "$config[local]@calendar=$baseCalType");
            $time->set((int) $config['y'], (int) $config['m'], (int) $config['d'], (int) $config['h'], (int) $config['i'], (int) $config['s']);
        }
        if ($time) {
            return $time;
        }
        throw new \Exception('invalide date/time entered!');
    }
    public function g2p($date = false, $outFormat = false, $local = false)
    {
        if (empty($date)) {
            return null;
        }

        if ($date) {
            $config = $this->str2date($date);
        } else {
            $config = $this->config;
        }
        if ($outFormat) {
            $config['outFormat'] = $outFormat;
        }
        if ($local) {
            $config['local'] = $local;
        }
        $time = $this->_checkDate($config, 'gregorian');
        $formatter = new \IntlDateFormatter($config['local'] . "@calendar={$config['calendar']}", \IntlDateFormatter::SHORT, \IntlDateFormatter::LONG, $config['outTimeZone'], \IntlDateFormatter::TRADITIONAL, $config['outFormat']);
        // $formatter->setPattern($config['outFormat']);
        $time = $formatter->format($time);
        return $time;
    }
    public function p2g($date = false, $outFormat = false, $local = false)
    {
        if (empty($date)) {
            return null;
        }
        if ($date) {
            $config = $this->str2date($date);
        } else {
            $config = $this->config;
        }
        if ($outFormat) {
            $config['outFormat'] = $outFormat;
        }
        if ($local) {
            $config['local'] = $local;
        }
        $time = $this->_checkDate($config, 'persian');
        $formatter = new \IntlDateFormatter($config['local'] . "@calendar={$config['calendar']}", \IntlDateFormatter::FULL, \IntlDateFormatter::FULL, $config['outTimeZone'], \IntlDateFormatter::GREGORIAN, $config['outFormat']);
        //$formatter->setPattern($config['outFormat']);
        $time = $formatter->format($time);
        return $time;
    }
    public function now($outFormat = false, $local = false)
    {
        $outFormat = $outFormat ?: $this->config['outFormat'];
        $config = $this->config;
        if ($local) {
            $config['local'] = $local;
        }
        $time = \IntlCalendar::createInstance($config['outTimeZone'], 'fa_IR');
        $time = \IntlDateFormatter::formatObject($time, $outFormat, $config['local']);
        return $time;
    }
    /**
     * @link vtwo.org/1688952
     */
    public function gLeapYear($year)
    {
        if (($year % 4 == 0) and (($year % 100 != 0) or ($year % 400 == 0))) {
            return true;
        }
        return false;
    }
    /**
     * @link vtwo.org/1688952
     */
    public function pLeapYear($year)
    {
        $ary = array(1, 5, 9, 13, 17, 22, 26, 30);
        $b = $year % 33;
        if (in_array($b, $ary)) {
            return true;
        }
        return false;
    }
    /**
     * return start and end of each jalali month in gregorian date
     *
     * date can get set in `$config['dateTime']`.
     * if `$tillNow` parameters set to null archive starts from current date.
     *<NOTE>when use $this->p2g it works only if you set $config['y/d/m'] and set $config['dateTime'] to null (its automatic), at the end of method $config['dateTime'] set to previews value</NOTE>
     *
     * @param int $monthNum number of dates to return
     * @param bool $tillNow if `$tillNow = true` last date of archive set to day of current month, esle last day set to the end of current month.
     * for changing last date of archive you MUST set `$tillNow = false` AND set value of $date to last date of archive.
     * default: true
     * @param string|array|boolean $date last date of archive [yyyy,m,d]
     */
    public function pArchive($monthNum, $tillNow = true, $date = false)
    {
        if ($date) {
            $config = $this->str2date($date);
        } else {
            $config = $this->config;
        }
        $dates = [];
        if ($tillNow) {
            $pY = $this->now('Y');
            $pM = $this->now('M');
            $pD = $this->now('d');
            $startDate = $this->p2g([$pY, $pM, 1]);
            $endDate = $this->p2g([$pY, $pM, $pD]);
            $dates[] = [$startDate, $endDate, 'year' => (int) $pY, 'month' => (int) $pM];
        } elseif (isset($config['y']) && isset($config['m']) && isset($config['d'])) {
            //$this->setConfig($config);
            $pY = $config['y'];
            $pM = $config['m'];
            $pD = $config['d'];
            $startDate = $this->p2g([$pY, $pM, 1]);
            $endDate = $this->p2g([$pY, $pM, $pD]);
            $dates[] = [$startDate, $endDate, 'year' => (int) $pY, 'month' => (int) $pM];
        } else {
            $pY = $this->now('Y');
            $pM = $this->now('M');
            $pM++;
            $monthNum++;
        }
        $monthNum--;
        for ($i = 0; $i < $monthNum; $i++) {
            $pM--;
            if ($pM == 0) {
                $pM = 12;
                $pY--;
            }
            if ($pM >= 1 && $pM <= 6) {
                $pD = 31;
            } elseif ($pM >= 7 && $pM <= 11) {
                $pD = 30;
            } elseif ($pM == 12) {
                $pD = ($this->pLeapYear($pY)) ? 30 : 29;
            }
            $startDate = $this->p2g([$pY, $pM, 1]);
            $endDate = $this->p2g([$pY, $pM, $pD]);
            $dates[] = [$startDate, $endDate, 'year' => (int) $pY, 'month' => (int) $pM];
        }
        return $dates;
    }
    private function str2date($date)
    {
        $config['dateTime'] = null;
        if (!is_array($date)) {
            $date = preg_split("/(-|\/| |:)/", $date);
        }
        $config['y'] = isset($date[0]) ? $date[0] : null;
        $config['m'] = isset($date[1]) ? $date[1] : null;
        $config['d'] = isset($date[2]) ? $date[2] : null;
        $config['h'] = isset($date[3]) ? $date[3] : 0;
        $config['i'] = isset($date[4]) ? $date[4] : 0;
        $config['s'] = isset($date[5]) ? $date[5] : 0;
        return array_merge($this->config, $config);
    }
}
