<?php
namespace App\Controller;

use App\Controller\AppController;

use Cake\ORM\TableRegistry;

class HairdressersController extends AppController{
     public function initialize() {
        $this->viewBuilder()->layout('Hairdresser');  
    }
	public function ad(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
	}
    public function adc(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function ade(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function al(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function ald(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function apc(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function apcc(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function index(){
        $this->set('img','img/coollogo_com-13563663.png');
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
        $this->Recruitments = TableRegistry::get('Recruitments');
        $this->Recruitment_menus = TableRegistry::get('Recruitment_menus');
        $this->User_menus = TableRegistry::get('User_menus');
        $this->User_profiles = TableRegistry::get('User_profiles');
        $this->Prices = TableRegistry::get('Prices');
        $this->Prefecture = TableRegistry::get('Prefecture');
        $this->Times = TableRegistry::get('Times');

        $data = $this->Recruitments->find('all');
//　ーーーーーーーーーーーーーーーーーーーーーーーメニュー配列ーーーーーーーーーーーーーーーーーーー
        $cnt = 1;
        foreach($data as $obj){
        	$menu = $this->User_menus->find('all');
        	foreach($menu as $obj2){
        		$array[$obj2->user_id.$cnt] = $obj2->menu_id;
        		$cnt++;
        	}
        	$cnt = 1;
        }
//　ーーーーーーーーーーーーーーーーーーーーーーー検索ーーーーーーーーーーーーーーーーーーーーーーーー
        $kensaku = $this->Recruitments->find('all');
        
        if($this->request->is('post')){
        	        	$menuflg=0;
        	$priceflg=0;
        	if(isset($_POST['menu_id'])){
        		$menu_id=$_POST["menu_id"];
				// print_r($menu_id);
				$menu=$this->User_menus->find();
				
				foreach ($menu as $key) {
					// print_r($key['recruitment_id']);
					// print_r($key['menu_id']);
				}

				for($i=0;$i<count($menu_id);$i++){

					if($i==0){
						$menu=$menu->where(['menu_id = '=>$menu_id[$i]]);
					}else{
						$menu=$menu->orWhere(['menu_id = '=>$menu_id[$i]]);
					}
				}

				$menu = $menu->select('user_id')->distinct('user_id');
				$count = $menu->find('all')->count('*');
				$menuflg = 1;
				//デバッグ
        		foreach ($menu as $key) {
        			// print_r($key['recruitment_id']);
        		}
        	}

        	$this->Recruitments = TableRegistry::get('Recruitments');
        	$data = $this->User_profiles->find('all')->join([
        	'table'=>'User_models',
        	'alias'=>'u',
        	'type'=>'LEFT',
        	'conditions'=>'u.user_id=User_profiles.user_id'
        ])->join([
        	'table'=>'Prices',
        	'alias'=>'pr',
        	'type'=>'LEFT',
        	'conditions'=>'pr.price_id=u.user_price_id'
        ])->join([
        	'table'=>'Users',
        	'alias'=>'us',
        	'type'=>'LEFT',
        	'conditions'=>'us.user_id=User_profiles.user_id'
        ])->join([
        	'table'=>'Prefecture',
        	'alias'=>'p',
        	'type'=>'LEFT',
        	'conditions'=>'User_profiles.prefecture_id=p.prefecture_id'
        ])->join([
        	'table'=>'Times',
        	'alias'=>'tf',
        	'type'=>'LEFT',
        	'conditions'=>'u.user_first_time_id=tf.time_id'
        ])->join([
        	'table'=>'Times',
        	'alias'=>'tl',
        	'type'=>'LEFT',
        	'conditions'=>'u.user_last_time_id=tl.time_id'
        ])->select([
        	'user_id'=>'User_profiles.user_id',
            'user_name'=>'User_profiles.user_name',
            'prefecture_name' => 'p.prefecture_name',
            'city_name'=>'User_profiles.city_name',
            'user_first_time'=>'tf.time',
        	'user_last_time'=>'tl.time',
        	'price_first'=>'pr.price_first',
        	'price_last'=>'pr.price_last',
        ])->where(['us.modelflg = '=>0]);


//メニュー検索
        	if($menuflg==1){
				if(!($count==0)){
					$cnt=1;
	        		foreach ($menu as $key){
	        			if($cnt == 1){
							$data=$data->where(['User_profiles.user_id = '=>$key['user_id']]);
						}else{
							$data=$data->orWhere(['User_profiles.user_id = '=>$key['user_id']]);
						}
	        			$cnt++;
	        		}

				}else{
					$data=$data->where(['User_profiles.user_id = '=>-1]);
	        	}
	        }

//エリア検索（県名）
	        if(isset($_POST['prefecture'])){
	        	$pref=$_POST['prefecture'];
	        	
	        	if(!($pref==-1)){
	        		$data=$data->andWhere(['User_profiles.prefecture_id = '=>$pref]);
	        		$prefflg=1;
	        	}
	        }

//価格検索	
        	if(isset($_POST['price'])){
        		$price = $_POST["price"];
        		
        		if(!($price==-1)){
	        		$data=$data->andWhere(['user_price_id = '=>$price+1]);
	        		$priceflg=1;
        		}		
        	}

//時刻検索

        	if(isset($_POST['zikoku'])){
        		if(!($_POST['zikoku']==-1)){
        			$zikoku = $_POST['zikoku'];
	        		$data=$data->andWhere(['user_first_time_id <='=>$zikoku]);
	        		$data=$data->andWhere(['user_last_time_id >='=>$zikoku]);
        		}

        	}



//　ーーーーーーーーーーーーーーーーーーーーーーー全検索ーーーーーーーーーーーーーーーーーーー

        }else if($this->request->is('get')){
        	$this->Recruitments = TableRegistry::get('Recruitments');
        	$this->User_profiles = TableRegistry::get('User_profiles');
        	$data = $this->User_profiles->find('all')->join([
        	'table'=>'User_models',
        	'alias'=>'u',
        	'type'=>'LEFT',
        	'conditions'=>'u.user_id=User_profiles.user_id'
        ])->join([
        	'table'=>'Prices',
        	'alias'=>'pr',
        	'type'=>'LEFT',
        	'conditions'=>'pr.price_id=u.user_price_id'
        ])->join([
        	'table'=>'Users',
        	'alias'=>'us',
        	'type'=>'LEFT',
        	'conditions'=>'us.user_id=User_profiles.user_id'
        ])->join([
        	'table'=>'Prefecture',
        	'alias'=>'p',
        	'type'=>'LEFT',
        	'conditions'=>'User_profiles.prefecture_id=p.prefecture_id'
        ])->join([
        	'table'=>'Times',
        	'alias'=>'tf',
        	'type'=>'LEFT',
        	'conditions'=>'u.user_first_time_id=tf.time_id'
        ])->join([
        	'table'=>'Times',
        	'alias'=>'tl',
        	'type'=>'LEFT',
        	'conditions'=>'u.user_last_time_id=tl.time_id'
        ])->select([
        	'user_id'=>'User_profiles.user_id',
            'user_name'=>'User_profiles.user_name',
            'prefecture_name' => 'p.prefecture_name',
            'city_name'=>'User_profiles.city_name',
            'user_first_time'=>'tf.time',
        	'user_last_time'=>'tl.time',
        	'price_first'=>'pr.price_first',
        	'price_last'=>'pr.price_last',
        ])->where(['us.modelflg = '=>0]);
        }

        
//　ーーーーーーーーーーーーーーーーーーーーーーーメニュー結合ーーーーーーーーーーーーーーーーーーー
        $this->Menus = TableRegistry::get('Menus');
        $array2 = array();
        $cnt2=0;
        foreach($data as $obj3){

        	$array_ketugou=[];
        	for ($i=1; $i < 10; $i++) { 

        		if(isset($array[$obj3->user_id*10+$i])){

        			$a=$array[$obj3->user_id*10+$i];
        			$d = $this->Menus->get($a);
        			
        			$array_ketugou[]=$d['menu_name'];
        			$d=implode(" ",$array_ketugou);


        		}
        	}
//　ーーーーーーーーーーーーーーーーーーーーーーー渡す内容ーーーーーーーーーーーーーーーーーーー
        	$array2[$obj3->recruitment_id.$cnt2] = array(
        		'user_id'=>$obj3['user_id'],
        		'user_name'=>$obj3['user_name'],
        		'prefecture_name'=>$obj3['prefecture_name'],
        		'city_name'=>$obj3['city_name'],
        		'user_first_time'=>$obj3['user_first_time'],
        		'user_last_time'=>$obj3['user_last_time'],
        		'price_first'=>$obj3['price_first'],
        		'price_last'=>$obj3['price_last'],
        		'menu_name'=>$d
        	);
        	
        	$cnt2++;
        }
        
        $menuname=$this->Menus->find();
        $pri=$this->Prices->find();
        $pre=$this->Prefecture->find();
        $tim=$this->Times->find();
        $this->set('time',$tim);
        $this->set('result', $array2);
        $this->set('youso',$menuname);
        $this->set('price',$pri);
        $this->set('pref',$pre);
    }
    public function mbd(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mbp(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mbpc(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mbpcr(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mbpcrc(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mbpd(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mbpdc(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
     public function mbph(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    } 
    public function mbphd(){
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
    }
    public function mpd(){
        $this->set('img','../img/coollogo_com-13563663.png');
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
        $this->users = TableRegistry::get('users');
        $this->user_profiles = TableRegistry::get('user_profiles');
        if(!(empty($this->request->data['complete']))){
            $data = $this->users->find()
                ->where(['mailaddress' => $_SESSION['mailaddress']])
                ->andwhere(['password' => $_SESSION['password']])
                ->all();
            foreach($data as $obj){
                $user_id = $obj->user_id;
            }
            $user_profile_data = array(
                "user_id" => $user_id,
                "user_name" => $this->request->data['user_name'],
                "prefecture_id" => $this->request->data['prefecture_id'],
                "city_name" => $this->request->data['city_name'],
                "age_id" => $this->request->data['age_id'],
                "sex_id" => $this->request->data['sex_id'],
                "profession_id" => $this->request->data['profession_id'],
                "user_comment" => $this->request->data['user_comment']
            );
            $user_profile = $this->user_profiles->newEntity($user_profile_data);
            $this->user_profiles->save($user_profile);
        }
        $this->prefecture = TableRegistry::get('prefecture');
        $this->age = TableRegistry::get('age');
        $this->sex = TableRegistry::get('sex');
        $this->profession = TableRegistry::get('profession');
        $data = $this->users->find()
            ->where(['mailaddress' => $_SESSION['mailaddress']])
            ->andwhere(['password' => $_SESSION['password']])
            ->all();
        foreach($data as $obj){
            $user_id = $obj->user_id;
        }
        $data2 = $this->user_profiles->find()
            ->where(['user_id' => $user_id])
            ->all();
        $this->set('data',$data2);
        foreach($data2 as $obj2){
            $prefecture_id = $obj2->prefecture_id;
            $age_id = $obj2->age_id;
            $sex_id = $obj2->sex_id;
            $profession_id = $obj2->profession_id;
        }
        $data3 = $this->prefecture->find()
            ->where(['prefecture_id' => $prefecture_id])
            ->all();
        foreach($data3 as $obj3){
            $this->set('prefecture',$obj3->prefecture_name);
        }
        $data4 = $this->age->find()
            ->where(['age_id' => $age_id])
            ->all();
        foreach($data4 as $obj4){
            $this->set('age',$obj4->age_name);
        }
        $data5 = $this->sex->find()
            ->where(['sex_id' => $sex_id])
            ->all();
        foreach($data5 as $obj5){
            $this->set('sex',$obj5->sex_name);
        }
        $data6 = $this->profession->find()
            ->where(['profession_id' => $profession_id])
            ->all();
        foreach($data6 as $obj6){
            $this->set('profession',$obj6->profession_name);
        }
    }
    public function mpe(){
        $this->set('img','../img/coollogo_com-13563663.png');
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
        $this->users = TableRegistry::get('users');
        $this->user_profiles = TableRegistry::get('user_profiles');
        $this->prefecture = TableRegistry::get('prefecture');
        $this->age = TableRegistry::get('age');
        $this->sex = TableRegistry::get('sex');
        $this->profession = TableRegistry::get('profession');
        $data = $this->users->find()
            ->where(['mailaddress' => $_SESSION['mailaddress']])
            ->andwhere(['password' => $_SESSION['password']])
            ->all();
        foreach($data as $obj){
            $user_id = $obj->user_id;
        }
        $data2 = $this->user_profiles->find()
            ->where(['user_id' => $user_id])
            ->all();
        $this->set('data',$data2);
        foreach($data2 as $obj2){
            $prefecture_id = $obj2->prefecture_id;
            $this->set('user_prefecture',$prefecture_id);
            $age_id = $obj2->age_id;
            $this->set('user_age',$age_id);
            $sex_id = $obj2->sex_id;
            $this->set('user_sex',$sex_id);
            $profession_id = $obj2->profession_id;
            $this->set('user_profession',$profession_id);
        }
        $data3 = $this->prefecture->find()->all();
        $this->set('prefecture',$data3);
        $data4 = $this->age->find()->all();
        $this->set('age',$data4);
        $data5 = $this->sex->find()->all();
        $this->set('sex',$data5);
        $data6 = $this->profession->find()->all();
        $this->set('profession',$data6);
    }
    public function signup(){
        $this->set('img','../img/coollogo_com-13563663.png');
        $this->prefectures = TableRegistry::get('prefecture');
        $this->sex = TableRegistry::get('sex');
        $this->age = TableRegistry::get('age');
        $prefecture = $this->prefectures->find('all');
        $this->set('prefecture',$prefecture);
        $age = $this->age->find('all');
        $this->set('age',$age);
        $sex = $this->sex->find('all');
        $this->set('sex',$sex);
        $this->profession = TableRegistry::get('profession');
        $profession = $this->profession->find('all');
        $this->set('profession',$profession);
        session_start();
        if($this->request->is('post')){
            $this->users = TableRegistry::get('users');
            $this->user_profiles = TableRegistry::get('user_profiles');
            $user_data = array(
                "mailaddress" => $this->request->data['mailaddress'],
                "password" => $this->request->data['password'],
                "modelflg" => 1
            );
            $user = $this->users->newEntity($user_data);
            $this->users->save($user);
            $data = $this->users->find('all',array('conditions' => array('mailaddress' => $this->request->data['mailaddress'],'password' => $this->request->data['password'])));
            foreach($data as $obj){
                $user_id = $obj->user_id;
            }
            $user_profile_data = array(
                "user_id" => $user_id,
                "user_name" => $this->request->data['user_name'],
                "prefecture_id" => $this->request->data['prefecture_id'],
                "city_name" => $this->request->data['city_name'],
                "age_id" => $this->request->data['age_id'],
                "sex_id" => $this->request->data['sex_id'],
                "profession_id" => $this->request->data['profession_id'],
                "user_comment" => $this->request->data['user_comment']
            );
            $user_profile = $this->user_profiles->newEntity($user_profile_data);
            $this->user_profiles->save($user_profile);
            return $this->redirect(['controller' => 'Hairdressers','action' => 'index']);
        }
    }
    public function cr(){
        $this->set('img','../img/coollogo_com-13563663.png');
        session_start();
        if(empty($_SESSION['mailaddress'])){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
        $this->users = TableRegistry::get('users');
        $this->chats = TableRegistry::get('chats');
        $data = $this->users->find()
            ->where(['mailaddress' => $_SESSION['mailaddress']])
            ->andwhere(['password' => $_SESSION['password']])
            ->all();
        foreach($data as $obj){
            $user_id = $obj->user_id;
        }
        $data = $this->chats->find()
                ->where(['user_id' => $user_id])
                ->count();
        if($data > 0){
            $this->chat_contents = TableRegistry::get('chat_contents');
            $data = $this->chats->find()
                ->where(['user_id' => $user_id])
                ->all();
            $this->set('chat',$data);
            foreach($data as $obj){
                $data2 = $this->chat_contents->find()
                ->where(['chat_id' => $obj->chat_id])
                ->all();
                foreach($data2 as $obj2){
                    $this->set($obj->chat_id,$obj2->content);
                }
            }
        }else{
            $this->set('message','誰もいません');
        }
    }
    public function crd(){
        $this->set('img','../img/coollogo_com-13563663.png');
        session_start();
        if(!(empty($_SESSION['mailaddress']))){
            return $this->redirect(['controller' => 'Commons','action' => 'login']);
        }
        $this->users = TableRegistry::get('users');
        $this->chats = TableRegistry::get('chats');
        $this->chat_contents = TableRegistry::get('chat_contents');
        if(!(empty($this->request->data['user_id']))){
            $data = $this->users->find('all',array('conditions' => array('mailaddress' => $this->request->data['mailaddress'],'password' => $this->request->data['password'])));
            foreach($data as $obj){
                $user_id = $obj->user_id;
            }
            $chat_count = $this->chats->find()
                ->where(['user_id' => $user_id,'opponent_user_id' => $this->request->data['user_id']])
                ->count();
            if($chat_count == 0){
                $chat_save = array(
                'user_id' => $user_id,
                'opponent_user_id' => $this->request->data['user_id']
                );
                $chat = $this->chats->newEntity($chat_save);
                $this->chats->save($chat,false);
            }
            $chat_data = $this->chats->find()
                ->where(['user_id' => $user_id,'opponent_user_id' => $this->request->data['user_id']])
                ->all();
            foreach($chat_data as $obj){
                $chat_id = $obj->chat_id;
            }
            $this->set('chat_id',$chat_id);
        }
        if(!(empty($this->request->data['chat_id']))){
            $chat_count = $this->chats->find()->where(['chat_id' => $this->request->data['chat_id']])->count();
            $cnt = $chat_count + 1;
            $chat_content_save = array(
                'chat_id' => $this->request->data['chat_id'],
                'content_id' => $cnt,
                'content' => $this->request->data['content']
                );
            $chat_content = $this->chat_contents->newEntity($chat_content_save);
            $this->chat_contents->save($chat_content,false);
        }
    }
}