<?php
/**
 * 图片文件上传类 ImgUpload
 *
 * @author zilong<zilong@heimilink.com>
 * @version 1.0
 * @date 2016-1-10
 */
class ImageService
{
    // 图片切割单位大小（128k）
    const BLOCK = 130072;
    const IS_OVERWRITE = true;
    const MAX_FILE_SIZE = 2097152; // 2M
    // 支持的图片类型
    private $_randomArr = array('a', 'b', 'c', 'd', 'e', 'f', 'A', 'B', 'C', 'D', 'E', 'F', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0);
    private $_temp_dir  = '';
    private $_image_dir  = '';
    private $_load_path  = '';

    /**
     * 处理上传图片
     *  @author zilong
     */
    public function uploadImg($filename) {
        //图片服务器
        $config = Yaf_Registry::get('config');
        $this->_load_path = $config->upload->imageserver->host;
        $this->_temp_dir = $config->upload->imageserver->tmp;
        $this->_image_dir = $config->upload->imageserver->path;

        $up = TZ_Loader::service('ImgUpload', 'Base');
        $up -> set("path", $this->_temp_dir);
	    $up -> set("maxsize", self::MAX_FILE_SIZE);
	    $up -> set("allowtype", array("gif", "png", "jpg", "jpeg", 'bmp'));
        // 设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
	    $up -> set("israndname", true);

        // 使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
	    if($up -> upload($filename)) {
			$imgPath = $up->getFileName();
            // 分割图片
            return $this->_cutFile($imgPath);

		} else {
            //获取上传失败以后的错误提示
            TZ_Response::error('801', $up->getErrorMsg());
            exit;
		}
	}
    /**
     * 分割图片
     * @param $fileName
     *
     * @return array|bool|string
     */
    private function _cutFile($fileName) {
        //分割
        if (empty($fileName)) {
            return false;
        }
        if (is_array($fileName)) {
            $datas = array();
            foreach ($fileName as $filetmp) {
                $filetmp = $this->_temp_dir . $filetmp;
                if (!file_exists($filetmp)) {
                    return false;
                }
                // 获取分割存储数据
                $imgInfoArr = $this->_getName();
                $num = 1;
                $file = fopen($filetmp, 'rb');
                while ($content = fread($file, self::BLOCK)) {
                    $dir = $this->_image_dir . $imgInfoArr['first'] . '/' . $imgInfoArr['second'] . '/' . $imgInfoArr['third'] . '/'
                        . $imgInfoArr['self'] . '/';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    $cacheFile =  $dir . $imgInfoArr['cutname'] . $imgInfoArr['randStr'] . $num++ . '.fpz';
                    $cfile = fopen($cacheFile, 'wb');
                    fwrite($cfile, $content . 'don`t do this' . rand(10, 99));
                    fclose($cfile);
                }
                fclose($file);
                // echo $filetmp;
                unlink($filetmp);
                $datas[] = $this->_load_path .  $imgInfoArr['filename'];
            }
            return $datas;
        } else {
            $datas = '';
            $fileName = $this->_temp_dir . $fileName;
            if (!file_exists($fileName)) {
                return false;
            }
            // 获取分割存储数据
            $imgInfoArr = $this->_getName();
            $num = 1;
            $file = fopen($fileName, 'rb');
            while ($content = fread($file, self::BLOCK)) {
                $dir = $this->_image_dir . $imgInfoArr['first'] . '/' . $imgInfoArr['second'] . '/' . $imgInfoArr['third']. '/'
                    . $imgInfoArr['self'] . '/';
                // echo $dir, '<br>';
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $cacheFile =  $dir . $imgInfoArr['cutname'] . $imgInfoArr['randStr'] . $num++ . '.fpz';
                // echo $cacheFile, '<br>';
                $cfile = fopen($cacheFile, 'wb');
                fwrite($cfile, $content . 'don`t do this' . rand(10, 99));
                fclose($cfile);
            }
            fclose($file);
            // echo $fileName;
            unlink($fileName);
            $datas = $this->_load_path .  $imgInfoArr['filename'];
            return $datas;
        }

    }

    /**
     *
     */
    private function _getName($time = 0) {
        $dateline = ($time === 0) ? time() : $time;
        $nameArr = array();
        $nameArr['first'] = date("Y", $dateline);
        $nameArr['second'] = date("m", $dateline);
        $nameArr['third'] = date("d", $dateline);
        $nameArr['self'] = dechex(date('YmdHis', $dateline));
        $nameArr['randStr'] = $this->_randomArr[array_rand($this->_randomArr)] . $this->_randomArr[array_rand($this->_randomArr)];
        $nameArr['cutname'] = substr($nameArr['self'], -6);
        if ($time == 0) {
            $nameArr['filename'] = $this->_randomArr[array_rand($this->_randomArr)] . $this->_randomArr[array_rand($this->_randomArr)] .
                dechex(date('Ymd', $dateline)) .
                $this->_randomArr[array_rand($this->_randomArr)] . $this->_randomArr[array_rand($this->_randomArr)]
                . str_pad(dechex(date("His", $dateline)), 5, '0',STR_PAD_LEFT) . $nameArr['randStr'];
        }
        return $nameArr;
    }
}
