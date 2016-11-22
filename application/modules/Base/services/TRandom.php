<?php
/*
 * TRandom service class
 *
 * @author zilong <songyang@747.cn>
 * @final 2014-9-9
 */
class TRandomService {
    /*
     * @param array $arr 概率数组
     * @return boolean
     * ＊＊＊＊＊＊＊＊＊＊  示例  ＊＊＊＊＊＊＊＊＊＊＊＊
     * $params = array('a'=> 0.01, 'b'=> 7.5, 'c' => 30);
     * $rest = getChance($params);
     * $rest: a|b|c|null
     */
    public function getChance($arr) {
        // 定义随机边界
        $base_rand = 10000000;
        /* 获得概率数据组 */    
        $chanceArr = array();
        if (is_array($arr)) {
            $chanceArr = $arr;
            // 计算未命中的概率
            $otherChange = 100 - array_sum($arr);
            $chanceArr['NONE'] = floatval($otherChange) > 0 ? $otherChange : 0 ; 
        } else {
            $chanceArr['TRUE'] = $arr;
            $otherChange = 100 - floatval($arr);
            $chanceArr['NONE'] = floatval($otherChange) > 0 ? $otherChange : 0 ; 
        }   
        /* 定义随机区间 */
        $rand_arr = array();
        $min = 1;
        foreach ($chanceArr as $key => $val){
            $rand_arr[$key]['min'] = $min;
            $rand_arr[$key]['max'] = ($min -1) + $val * 100;
            $min = $rand_arr[$key]['max'] + 1;
        }   
        /* 获取随机数 */
        $rand_num = mt_rand(1, $base_rand);
        $rand = $rand_num % 10000 === 0  ? 10000 : ($rand_num % 10000) ;
        $result = ''; 
        foreach ($rand_arr as $key => $val) {
            if ($rand >= $val['min'] && $rand <= $val['max']) {
               $result = $key; 
               break;
            }   
        }   
        if($result === 'NONE') {
            $result = null;
        } else if($result === 'TRUE') {
            $result = true;
        }   
        unset($chanceArr); 
        unset($rand_arr);
        return $result;
    }
    //得到微秒
	public function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}
	//得到订单id
	public function getOrderId(){
		$id=$this->getMillisecond();
		return rand(1000, 9999).$id.rand(1000, 9999);
	}
	
}