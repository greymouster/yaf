var API_CONFIG = 
{
	"login":{
		"name":"用户登陆",
		"url":"/2/user/login/hello.json",
		"method":"post",
		"params":{
			"telephone":"18600622921",
			"password":"ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE"
		}
	},
		
	"change_password":{
		"name":"修改密码",
		"url":"/2/user/change_password/hello.json",
		"method":"post",
		"params":{
			"session_id":"18600622921",
			"password":"ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE",
			"old_password":"ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE"
		}
	},
	
	"reset_password":{
		"name":"重置密码",
		"url":"/2/user/reset_password/hello.json",
		"method":"post",
		"params":{
			"telephone":"18600622921",
			"verify_code":"1024",
			"password":"ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE"
		}
	},
	
	"verify_code":{
		"name":"验证验证码",
		"url":"/2/user/verify_code/hello.json",
		"method":"post",
		"params":{
			"telephone":"18600622921",
			"verify_code":"1024"
		}
	},
	
	"get_code":{
		"name":"获取验证码",
		"url":"/2/user/get_code/hello.json",
		"method":"post",
		"params":{
			"telephone":"18600622921",
			"reset":"1"
		}
	},
	
	"modify":{
		"name":"修改用户信息",
		"url":"/2/user/modify/hello.json",
		"method":"post",
		"params":{
			"session_id":"18600622921",
			"name":"88liang",
			"gender":"1",
			"city":"beijing",
			"area":"chaoyangqu"
		}
	},
	
	"details":{
		"name":"查询用户信息",
		"url":"/2/user/details/hello.json",
		"method":"post",
		"params":{
			"session_id":"18600622921",
			"user_id":"88liang"
		}
	},
	
	"register":{
		"name":"用户注册",
		"url":"/2/user/register/hello.json",
		"method":"post",
		"params":{
			"telephone":"18600622921",
			"password":"ADSF124124ASDF234R2FASDF23RFASWR23FAWSDFAWDE",
			"verify_code":"1024",
			"name":"88liang"
		}
	},
	

	
	"logout":{
		"name":"用户注销",
		"url":"/2/user/logout/hello.json",
		"method":"post",
		"params":{
			"session_id":"18600622921"
		}
	},
	
	
	"upgrade":{
		"name":"应用升级",
		"url":"/2/upgrade/app/hello.json",
		"method":"get",
		"params":"os=andriod&app_name=howiewolf"
	},
	"broadcasts":{
		"name":"创建广播",
		"url":"/2/broadcast/create/hello.json",
		"method":"post",
		"params": {
				//必选
				'session_id':'dfdefe',
				'title':'dfdsf',
				'wifi_id':23343,
				//可选
				'content':'fdfdsf',
				'business':1,
				'picture_url':'dfdsfsdfds',
				'jump_url':'dfsdfsdfsd',
				'status':1
		}
	},
	
	"broadcasts2":{
		"name":"更新广播",
		"url":"/2/broadcasts/update/howie.json",
		"method":"post",
		"params": {
				//必选
				'session_id':'dfdefe',
				'title':'dfdsf',
				'wifi_id':23343,
				//可选
				'content':'fdfdsf',
				'business':1,
				'picture_url':'dfdsfsdfds',
				'jump_url':'dfsdfsdfsd',
				'status':1
		}
	},
	
	"chat":{
		"name":"创建留言",
		"url":"/2/chat/create/howie.json",
		"method":"post",
		"params": {
			'content':'i ai wo men',
			'is_picture':0,
			'wifi_id':43434,
			'session_id':'dfdsfsdfds'
		}
	},
	
	"chat2":{
		"name":"查看一个热点上的留言",
		"url":"/2/chat/message/howie.json",
		"method":"get",
		"params": "wifi_id=43434"
	},
	
	"data":{
		"name":"给定经纬度的点查询",
		"url":"/2/data/gpspoint/howie.json",
		"method":"get",
		"params":"l_lng=116287468&r_lng=116339673&l_lat=39804564&r_lat=40164009&c_lng=112412020&c_lat=35492289&pt_id=6&isp_id=0&page=1&count=20&is_sort=1"
		//必选参数：l_lng=116287468&r_lng=116339673&l_lat=39804564&r_lat=40164009&c_lng=112412020&c_lat=35492289"
		//可选的参数：pt_id=6&isp_id=0&page=1&count=20&is_sort=1	
	},
	
	"data2":{
		"name":"给定经纬度的范围查询",
		"url":"/2/data/gpsrange/howie.json",
		"method":"get",
		"params":"c_lng=112412020&c_lat=35492289&pt_id=6&isp_id=0&page=1&count=20&is_sort=1&range=10"
		//必选参数：c_lng=112412020&c_lat=35492289
		//可选参数：pt_id=6&isp_id=0&page=1&count=20&is_sort=1&range=100	
	},
	
	"device":{
		"name":"设备创建",
		"url":"/2/device/create/howie.json",
		"method":"post",
		"params": [
			{
				//必选
				'session_id':'365623ae4928e55de0442232d1817717',
				'udid':'4354',
				'ssid':'CMCC',
				'bssid':'00:23:cd:ec:52:e6',
				'password':'123456',
				//可选
				'auth_type':0,
				//'wifi_id'=>162875,
				'owner_id':-1,
				'share':1,
				'status':1
			},
			{
				//必选
				'session_id':'365623ae4928e55de0442232d1817717',
				'udid':'4354',
				'ssid':'CMCC',
				'bssid':'00:23:cd:ec:52:e6',
				'password':'123456',
				//可选
				'auth_type':0,
				//'wifi_id'=>162875,
				'owner_id':-1,
				'share':1,
				'status':1
			}
		]
	},
	
	"device2":{
		"name":"设备更新",
		"url":"/2/device/update/howie.json",
		"method":"post",
		"params": [
			{
				//必选
				'session_id':'365623ae4928e55de0442232d1817717',
				'udid':'4354',
				'wifi_device_id':162922,  
				'ssid':'CMCC',
				'bssid':'00:23:cd:ec:52:e6',
				'password':'123456',
				//可选
				'auth_type':0,
				'wifi_id':162875,
				'owner_id':-1,
				'share':1,
				'status':1
			},
			{
				//必选
				'session_id':'365623ae4928e55de0442232d1817717',
				'udid':'4354',
				'wifi_device_id':162923, 
				'ssid':'CMCC',
				'bssid':'00:23:cd:ec:52:e6',
				'password':'123456',
				//可选
				'auth_type':0,
				'wifi_id':162875,
				'owner_id':-1,
				'share':1,
				'status':1
			}
		]
	},
	
	"feedback":{
		"name":"创建反馈",
		"url":"/2/feedback/create/howie.json",
		"method":"post",
		"params":
			{
			//必选	
			'udid':'343434',
			'session_id':'dfdsfefdsf',
			'app_version':'3.4.5',
			'os':'apple',
			'os_version':'4.5.6',
			'maker':'htm',
			'size':'45*34',
			'content':'fdfdfe',
			//可选
			'contact':'34354654'
			}
	},
	
	"create":{
		"name":"创建wifi热点",
		"url":"/2/wifi/create/howie.json",
		"method":"post",
		"params":
			{
			//必选
			'session_id':'dfdsfefrgd',
		    'wifi_device_id':162926,
		    'name':'dfsldf',
		    'address':'dfsdf',
		    'lng':34343,
		    'lat':33232,
		    'city_id':3434,
			//可选
			'isp_id':4,
			'wifi_type':1,
			'status':1,
			'telephone':'4343', 
			'cover_range':'34343',
			'place_type_id':2,
			'area_id':123
			}
	},
	
	"update":{
		"name":"更新wifi热点",
		"url":"/2/wifi/update/howie.json",
		"method":"post",
		"params":
			{
			//必选
			'session_id':'dfdsfefrgd',
		    'wifi_device_id':162926,
		    'wifi_info_id':257175,
		    'name':'dfsldf',
		    'address':'dfsdf',
		    'lng':34343,
		    'lat':33232,
		    'city_id':3434,
			//可选
			'isp_id':4,
			'wifi_type':1,
			'status':0,
			'telephone':'4343', 
			'cover_range':'34343',
			'place_type_id':2,
			'area_id':123
			}
	},
	
	"createAll":{
		"name":"创建热点和设备",
		"url":"/2/wifi/createAll/howie.json",
		"method":"post",
		"params":{
		    //必选
			'session_id':'d1d3040ce79d8c809fb6b32c0e4dae05',
		    'name':'dfsldf',
		    'address':'dfsdf',
		    'city_id':3434,
		    'lng':34343,
		    'lat':33232,
			//可选
		    'isp_id':4, //isp_id 还没有定下来，再议
			'wifi_type':1, //wifi_type 还没有定下来，再议
			'area_id':123,
			'place_type_id':2,
			'cover_range':'34343',
			'telephone':'4343', 
			'status':0,
			'wifi_devices':[
				{
					//必选
					'ssid':'434343',
					'bssid':'323232',
					'udid':'23232',
					'password':'3432432dfd',
					//可选
					'share':1,
					'auth_type':1,
					//wifi_id,
					//owner_id,
					'status':1
				},
				{
					//必选
					'ssid':'434343',
					'bssid':'323232',
					'udid':'23232',
					'password':'3432432dfd',
					//可选
					'share':1,
					'auth_type':1,
					//wifi_id,
					//owner_id,
					'status':1
				}
			]
		}
	},
	
	"updateAll":{
		"name":"更新热点和设备",
		"url":"/2/wifi/updateAll/howie.json",
		"method":"post",
		"params":{
			//必选
		    'wifi_info_id':257215,
			'session_id':'d1d3040ce79d8c809fb6b32c0e4dae05',
		    'name':'a',
		    'address':'dfsdf',
		    'city_id':3434,
		    'lng':34343,
		    'lat':33232,
			//可选
		    'isp_id':4,
			'wifi_type':1,
			'area_id':123,
			'place_type_id':2,
			'cover_range':'34343',
			'telephone':'4343', 
			'status':1,
			'wifi_devices':[
				{
					//必选
					'wifi_device_id':162960,
					'ssid':'434343',
					'bssid':'323232',
					'udid':'23232',
					'password':'11111',
					//可选
					'share':1,
					'auth_type':1,
					//owner_id,
					'status':1
				},
				{
					'wifi_device_id':162961,
					'ssid':'434343',
					'bssid':'323232',
					'udid':'23232',
					'password':'11111',
					//可选
					'share':1,
					'auth_type':1,
					//owner_id,
					'status':1
				}
			]
		}
	},
	
	"detail":{
		"name":"热点详情",
		"url":"/2/wifi/detail/howie.json",
		"method":"get",
		"params":"wifi_id=257245"
	},
	
	"sync":{
		"name":"同步",
		"url":"/2/wifi/sync/howie.json",
		"method":"get",
		"params":"city_id=3001&updated_at=1354855743"
	},
	
	"UserReported":{
		"name":"用户提交的热点信息 ",
		"url":"/2/wifi/UserReported/howie.json",
		"method":"get",
		"params":"session_id=d1d3040ce79d8c809fb6b32c0e4dae05"
	}
	
	
	
}
























