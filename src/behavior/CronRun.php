<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace yiqiniu\behavior;
use think\facade\Log;
use yiqiniu\library\Date;

/**
 * 自动执行任务
 */
class CronRun
{
    public function run()
    {
        if (config('yqnapi.cron_on')) {
            $this->checkTime();
        }
    }

    function checkTime()
    {
        // 锁定自动执行
        $lockfile = app()->getRuntimePath() . 'cron.lock';
        if (is_file($lockfile) && filemtime($lockfile) > ($_SERVER['REQUEST_TIME'] - config('yqnapi.cron_time'))) {
            return;
        }

        //重制文件时间
        touch($lockfile);
        set_time_limit(1000);
        //忽略用户主动断开
        ignore_user_abort(true);

        //缓存执行文件
        if (cache('cron_config')) {
            $crons = cache('cron_config');
        } else if (config('yqnapi.cron_config')) {
            $crons = config('yqnapi.cron_config');
        }
        //执行文件
        if (!empty($crons) && is_array($crons)) {
            $update = false;
            $log = array();
            foreach ($crons as $key => $cron) {
                $prevtime = is_string($cron[2]) ? strtotime($cron[2]) : $cron[2];
                $systime = $_SERVER['REQUEST_TIME'];
                if ($systime >= $prevtime) {
                    // 到达时间 执行cron文件
                    debug('cronStart');
                    include app()->getAppPath() . 'behavior/cron/' . $cron[0] . '.php';
                    debug('cronEnd');

                    $_useTime = debug('cronStart', 'cronEnd', 6);
                    // 更新cron记录
                    $cron[2] = $this->getNextTime($cron, $prevtime, $systime);
                    $cron[3] = date('Y-m-d H:i:s');
                    $crons[$key] = $cron;
                    $log[] = "Cron:$key Runat " . date('Y-m-d H:i:s') . " Use $_useTime s\n";
                    $update = true;
                }
            }
            if ($update) {
                // 记录Cron执行日志
                 Log::write(implode('', $log));
                 cache('cron_config', $crons);
            }
        }
        return;
    }

    /**
     * 获取下次执行时间
     * @param $cron
     * @param $prevtime
     * @param $systime
     * @return false|float|int
     */
    function getNextTime($cron, $prevtime, $systime)
    {
        list($num, $ins) = explode(' ', $cron[1]);
        $nexttime = 0;
        if ($ins == "y" || $ins == "m") {
            $date = new Date($prevtime);
            $nexttime = strtotime($date->Add($num, $ins));
            if($nexttime<=$systime){
                while ($nexttime < $systime) {
                    $date = new Date($nexttime);
                    $nexttime = strtotime($date->Add($num, $ins));
                }
            }
        } else {
            $sec = 0;
            switch ($ins) {
                case "d":
                    $sec = $num * 86400;
                    break;
                case "h":
                    $sec = $num * 3600;
                    break;
                case "M":
                    $sec = $num * 60;
                    break;
                case "s":
                    $sec = $num;
                    break;
            }
            $nexttime = $prevtime + (floor(($systime - $prevtime) / $sec) + 1) * $sec;
        }
        return $nexttime;

    }
}