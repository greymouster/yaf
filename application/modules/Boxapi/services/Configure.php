<?php
/**
 * author: octopus<zhangguipo@heimilink.com>.
 * Time: 2016-09-23
 * Info:盒子配置
 */
class ConfigureService
{
    //得到主屏配置
    public function getMainConfigure($imei,$version)
    {
    	$data=array();
    	
		//根据imei得到所属组信息
		$groupInfo=TZ_Loader::model('DeviceGroup','Boxapi')->select(array('imei:eq'=>$imei),'*','ROW');
		if(count($groupInfo)==0){
			return true;
		}
		$groupList=array();
		$groupList[]=$groupInfo['gid_1'];
		$groupList[]=$groupInfo['gid_2'];
		$groupList[]=$groupInfo['gid_3'];
		$groupList[]=$groupInfo['gid_4'];
		$groupList[]=$groupInfo['gid_5'];
		//根据组id得到布局信息
		$condition=array();
		//$condition['version:eq']=$version;
		$condition['gid:in']=$groupList;
		$condition['status:eq']=1;
		$condition['is_temp:eq']=0;
		$condition['page_type:eq']='home';
		$condition['order']='gid desc';
		$row=TZ_Loader::model('Layout','Boxapi')->select($condition,'*','ROW');
   		 if(count($row)==0){
			return true;
		}
		$data['version']=$row['version'];
		//foreach ($layList as $row){
		$id=$row['id'];
		$layDetail=TZ_Loader::model('Layoutlist','Boxapi')->select(array('layout_id:eq'=>$id),'*','ALL');
		 if(count($layDetail)==0){
			return ;
		}
		foreach($layDetail as $detail){
			$page=intval($detail['page']);
			$contentId=$detail['content_id'];
			$contentInfo=TZ_Loader::model('Content','Boxapi')->select(array('id:eq'=>$contentId),'*','ROW');
			$modelRow=array_merge($detail,$contentInfo);
			$setData=array();
			$setData['no']=1;
			$setData['widget_name']=$modelRow['cont_name'];
			$setData['x']=intval($modelRow['position_x']);
			$setData['y']=intval($modelRow['position_y']);
			$setData['length']=intval($modelRow['position_h']);
			$setData['width']=intval($modelRow['position_w']);
			$setData['type']=intval($modelRow['cont_type']);
			$setData['title']=$modelRow['cont_title'];
			if($modelRow['cont_type']==0){
				$setData['image']=$modelRow['image'];
				$setData['main_url']=$modelRow['main_url'];
			}elseif ($modelRow['cont_type']==1){
				$setData['image']=$modelRow['image'];
				$setData['main_url']=$modelRow['main_url'];
				$setData['url']=$modelRow['url'];
			}elseif ($modelRow['cont_type']==2){
				$setData['image']=$modelRow['image'];
				$setData['app_name']=$modelRow['app_name'];
				$setData['app_page']=$modelRow['app_page'];
				$setData['main_url']=$modelRow['main_url'];
			}elseif ($modelRow['cont_type']==3){
                $setData['image']=$modelRow['image'];
                $setData['app_name']=$modelRow['app_name'];
                $setData['app_page']=$modelRow['app_page'];
                $setData['main_url']=$modelRow['main_url'];
                $setData['url']=$modelRow['url'];
            }
			$isExist=false;
			$num=0;
			if(count($data['home'])>0){
				foreach ($data['home'] as &$dataRow){
					if($page==$dataRow['page']){
						$isExist=true;
						$num=count($dataRow['widget']);
						$setData['no']=$num+1;
						$dataRow['widget']=array_merge($dataRow['widget'],array($setData));
						break;
					}
				}
			}
			if(!$isExist){
				$contentData=array();
				$contentData['page']=intval($page);
				$contentData['widget'][]=$setData;
				$data['home'][]=$contentData;
			}
		}
		//}
		return $data;
    }
  //得到次屏配置
    public function getSecondaryConfigure($imei,$version)
    {
    	$data=array();
    	//$data['version']=$version;
		//根据imei得到所属组信息
		$groupInfo=TZ_Loader::model('DeviceGroup','Boxapi')->select(array('imei:eq'=>$imei),'*','ROW');
		if(count($groupInfo)==0){
			return true;
		}
		$groupList=array();
		$groupList[]=$groupInfo['gid_1'];
		$groupList[]=$groupInfo['gid_2'];
		$groupList[]=$groupInfo['gid_3'];
		$groupList[]=$groupInfo['gid_4'];
		$groupList[]=$groupInfo['gid_5'];
		//根据组id得到布局信息
		$condition=array();
		//$condition['version:eq']=$version;
		$condition['gid:in']=$groupList;
		$condition['status:eq']=1;
		$condition['page_type:neq']='home';
		$condition['order']='gid desc';
		$condition['is_temp:eq']=0;
		$row=TZ_Loader::model('Layout','Boxapi')->select($condition,'*','ROW');
   		 if(count($row)==0){
			return true;
		}
		$data['version']=$row['version'];
		//foreach ($layList as $row){
		$id=$row['id'];
		$layDetail=TZ_Loader::model('Layoutlist','Boxapi')->select(array('layout_id:eq'=>$id),'*','ALL');
		 if(count($layDetail)==0){
			return ;
		}
		foreach($layDetail as $detail){
			$page=intval($detail['page']);
			$contentId=$detail['content_id'];
			$contentInfo=TZ_Loader::model('Content','Boxapi')->select(array('id:eq'=>$contentId),'*','ROW');
			$contentData=array();
			$contentData['page']=intval($page);
			$contentData['url']=$contentInfo['main_url'];
			$data['left_right'][]=$contentData;

		}
		//}
		return $data;
    }
   

}