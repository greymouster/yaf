<?php

/**
 * 执行时间跟踪工具类
 * Class RuntimeService
 */
class TZ_Runtime {

    const DEFAULT_EXEC_TIME = 0.05; // 微秒

    // 日记名称
    private static $_log_file_name = '';
    // 时间标记
    private static $_timestamps = array();
    // tag标记码
    private static $_index_num = 1;

    /**
     * 开启时间跟踪,标识时间点
     */
    public static function start($name) {
        if (!isset($name)) {
            $name = 'time_stamp_log-' . date("Ymd");
        }
        self::$_log_file_name = $name;
        self::$_timestamps[] = array('key' => 'RUNTIME_START', 'value' => self::get_millisecond());
    }

    /**
     * 记录本次时间间隔结点, 并标记下次时间标识
     * @param $tag // 记录名称
     */
    public static function addTag($tag) {
        if (!isset($tag)) {
            $tag = 'tag_' . self::$_index_num;
            self::$_index_num ++;
        }
        self::$_timestamps[] = array('key' => $tag, 'value' => self::get_millisecond());
    }

    /**
     * 结束本次时间跟踪记录
     */
    public static function close() {
        self::$_timestamps[] = array('key' => 'RUNTIME_END', 'value' => self::get_millisecond());
        return self::exec_result();
    }

    /**
     * 计算执行结果
     */
    private static function exec_result() {
        $resultArr = array();
        if (!empty(self::$_timestamps)) {
            $mark_timeStamps = 0;
            foreach (self::$_timestamps as $key => $stamp) {
                if ($key == 0) {
                    continue;
                } else {
                    $preKey = $key - 1;
                    $resultArr[] = array(
                        'title' => self::$_timestamps[$preKey]['key'] . ' -> ' . self::$_timestamps[$key]['key'],
                        'duration' => round((self::$_timestamps[$key]['value']
                            - self::$_timestamps[$preKey]['value'] - self::DEFAULT_EXEC_TIME), 3)
                    );
                }

            }
        }
        return self::writeResult($resultArr);
    }

    /**
     * 记录执行结果
     * @param $resultArr
     */
    private static function writeResult($resultArr) {
        $resStr = '';
        $resArr = array();
        if (!empty($resultArr)) {
            foreach ($resultArr as $result) {
                $resArr[]= ' ' . $result['title'] . ' 执行时间:' . $result['duration'] . "ms";
            }
            if(!empty($resArr)) {
                $resStr = implode("\n", $resArr);
            }
            self::writeLogs($resStr);
        }
        return true;
    }

    /**
     * 写入日志
     * @param $data
     */
    private static function writeLogs($data) {
        $path = Yaf_Registry::get('config')->logs->path;
        $dir_path = $path . '/' . 'exec_time_log' . '/';
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        $filepath = $dir_path . self::$_log_file_name . '.log';
        $delimiter_start = "\n+---------------------------- log start -----------------------------+\n记录时间:"
            . date("Y-m-d H:i:s") . "\n";
        $delimiter_end = "\n+---------------------------- log end -----------------------------+\n";
        file_put_contents($filepath, $delimiter_start . $data . $delimiter_end, FILE_APPEND);
    }

    /**
     * 获取毫秒数
     * @return mixed
     */
    private static function get_millisecond() {
        list($usec, $sec) = explode(" ", microtime());
        $msec = ($sec - 1478000000 + $usec) * 1000;
        return $msec;
    }
}