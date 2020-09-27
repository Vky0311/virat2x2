<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Session;
use App\DB\UserMaster;
use App\DB\User;
use App\DB\Notification;

class TreeViewController extends Controller
{
    public $view                = '';    

    public function __construct() {
        $this->view             = 'admin.tree.';                  
    }
    
    public function parent($baseUser)
	{
	  $parent = array();
	  $parent[] = array(
	      'self_sponsor_key' => $baseUser->self_sponsor_key,
	      'sponser_unique_id' => $baseUser->sponser_unique_id,
	      'id' => $baseUser->id,
	      'child' => $this->childs($baseUser->self_sponsor_key),
	    );
	  return $parent;
	}
    
    public function childs($self_sponsor_key)
	{ 
		$result = DB::table('user_master')->whereNotNull('self_sponsor_key')->where('sponser_unique_id',$self_sponsor_key)->get()->toArray();
		$childs = array();
	  foreach ($result as $key => $value) {
	  	$childs[] = array(
	      'self_sponsor_key' => $value->self_sponsor_key,
	      'sponser_unique_id' => $value->sponser_unique_id,
	      'id' => $value->id,
	      'child' => $this->childs($value->self_sponsor_key),
	    );
	  }
	  return $childs;
	}

    public function index(Request $request)
    {
        try {
            $data = [];
            $spnser_d = [];
            $spnser_f = [];
            $data['title']          = 'Admin TREE';
            $data['page_title']     = 'Admin TREE';
            $data['user']           = Sentinel::getUser();
            $data['tree_user']           = UserMaster::whereNull('mlm_side')->whereNotNull('self_sponsor_key')->first();
            $data['tree_user_all']           = UserMaster::whereNull('mlm_side')->whereNotNull('self_sponsor_key')->get();
            $baseUser = DB::table('user_master')->whereNull('mlm_side')->whereNotNull('self_sponsor_key')->first();
            $tree = $this->parent($baseUser);
            echo '<pre>'; print_r($tree);die((__FILE__).'-->'.(__FUNCTION__).'--Line('. (__LINE__).')');
            // User::getChildForTreeOneByOne($data['tree_user']['self_sponsor_key'], $spnser_f);
            
            $tree_user_1st_level           = UserMaster::where('sponser_unique_id',$data['tree_user']['self_sponsor_key'])->pluck('self_sponsor_key');

            $tree_user_2st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_1st_level)->pluck('self_sponsor_key');

            $tree_user_3st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_2st_level)->pluck('self_sponsor_key');

            $tree_user_4st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_3st_level)->pluck('self_sponsor_key');

            $tree_user_5st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_4st_level)->pluck('self_sponsor_key');

            $tree_user_6st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_5st_level)->pluck('self_sponsor_key');

            $tree_user_7st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_6st_level)->pluck('self_sponsor_key');


            $data['level1'] = $tree_user_1st_level;
            $data['level2'] = $tree_user_2st_level;
            $data['level3'] = $tree_user_3st_level;
            $data['level4'] = $tree_user_4st_level;
            $data['level5'] = $tree_user_5st_level;

            // dd($data['level1'], $tree_user_1st_level, $tree_user_1st_level, $tree_user_1st_level);

            // dd($tree_user_1st_level, $tree_user_2st_level, $tree_user_3st_level, $tree_user_4st_level, $tree_user_5st_level, $tree_user_6st_level, $tree_user_7st_level);

            // $data['tree_user_2nd_level']           = [];

            // if(!empty($data['tree_user_1st_level']->toArray()))
            // {
            //     foreach ($data['tree_user_1st_level'] as $key => $child2) {
            //          $data['tree_user_2nd_level'][] = UserMaster::where('sponser_unique_id',$child2['self_sponsor_key'])->first();
            //     }
            // }
            
            // dd($data['tree_user_2nd_level']);


            $data['all_children'] = User::getUserChildList($data['tree_user']['self_sponsor_key'],$spnser_d);
            $data['all_children_data'] = UserMaster::select('id','mlm_side')->whereIn('self_sponsor_key',$data['all_children'])->orderBy('id','ASC')->get()->toArray();
            // dd($data['all_children_data']);
            // dd($data['all_children'], $data['all_children_data']);
            // foreach ($data['all_children'] as $key => $child) {
            //     $first_leverl = UserMaster::where('self_sponsor_key', $child)->get();
            // }

            // dd($first_leverl);

            // $data['all_children_list'] = User::getAllChildFullDetailForTree($data['all_children']);

            
            
            // dd($data['tree_user'], $data['all_children'], $data['all_children_list']);    
            // dd($data['all_children_list']);    

            return \View::make($this->view.'index', $data); 

            } catch (Exception $e) {
                
            }
    }

    public function view(Request $request, $id)
    {
        try {
            
            $data = [];
            $spnser_d = [];
            $spnser_f = [];
            $data['title']          = 'VIEW TREE';
            $data['page_title']     = 'VIEW TREE';
            $data['user']           = Sentinel::getUser();
            $data['tree_user']           = UserMaster::where('id', $id)->first();
            // $data['tree_user_all']           = UserMaster::whereNull('mlm_side')->whereNotNull('self_sponsor_key')->get();
            $all_children = User::getUserChildList($data['tree_user']['self_sponsor_key'],$spnser_d);
            foreach ($all_children as $child) {
                $may = Notification::getTreeUlLi($child);                
            }
            dd('te');
            // User::getChildForTreeOneByOne($data['tree_user']['self_sponsor_key'], $spnser_f);
            
            $tree_user_1st_level           = UserMaster::where('sponser_unique_id',$data['tree_user']['self_sponsor_key'])->pluck('self_sponsor_key','id');
            $tree_user_1st_level_1           = UserMaster::where('sponser_unique_id',$data['tree_user']['self_sponsor_key'])->pluck('name');

            $tree_user_2st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_1st_level)->pluck('self_sponsor_key','id');
            $tree_user_2st_level_2           = UserMaster::whereIn('sponser_unique_id',$tree_user_1st_level)->pluck('name');

            $tree_user_3st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_2st_level)->pluck('self_sponsor_key','id');

            $tree_user_4st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_3st_level)->pluck('self_sponsor_key','id');

            $tree_user_5st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_4st_level)->pluck('self_sponsor_key','id');

            $tree_user_6st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_5st_level)->pluck('self_sponsor_key','id');

            $tree_user_7st_level           = UserMaster::whereIn('sponser_unique_id',$tree_user_6st_level)->pluck('self_sponsor_key','id');

            // dd($tree_user_3st_level);

            $data['level1'] = $tree_user_1st_level_1;
            $data['level2'] = $tree_user_2st_level_2;
            $data['level3'] = $tree_user_3st_level;
            $data['level4'] = $tree_user_4st_level;
            $data['level5'] = $tree_user_5st_level;

            // dd($data['level1'], $tree_user_1st_level, $tree_user_1st_level, $tree_user_1st_level);

            // dd($tree_user_1st_level, $tree_user_2st_level, $tree_user_3st_level, $tree_user_4st_level, $tree_user_5st_level, $tree_user_6st_level, $tree_user_7st_level);



            $data['all_children'] = User::getUserChildList($data['tree_user']['self_sponsor_key'],$spnser_d);
            dd($data['all_children']);
            $data['all_children_data'] = UserMaster::select('id','mlm_side')->whereIn('self_sponsor_key',$data['all_children'])->orderBy('id','ASC')->get()->toArray();

            // dd($data['all_children_data']);

            global $content;

            if(empty($content)){
                $content = "";
            }

            foreach ($data['all_children_data'] as $key => $downline) {

                $side = $downline['mlm_side'] == 'L'?'LEFT':'Right';

                $i = 0;
                if ($i == 0) $content .= '<ul>';
                if($downline['mlm_side'] == 'L'){
                    $content .= '<li><a href="#">' . $downline['id'] ."".$side."</a></li>";
                }
                if($downline['mlm_side'] == 'R'){
                    $content .= '<li><a href="#">' . $downline['id'] ."".$side."</a></li>";
                }
                // $this->getTree($downline['otc_id']);
                $content .= '';
                $i++;
                if ($i > 0) $content .= '</ul>';

            }

            $data['view_ul_li'] = $content;

       
            // echo "<pre>";
            // print_r($data['view_ul_li']);
            // echo "</pre>";
            // dd($data['view_ul_li']);

            return \View::make($this->view.'view', $data); 
   

        } catch (Exception $e) {
            
        }
    }

    public static function getTree()
    {

    }


    public function getList(Request $request)
    {
        try {
            
            // $user_id_list = RoleUser::where('role_id', '3')->pluck('user_id')->toArray();
            $data        = UserMaster::whereNull('mlm_side')->whereNotNull('self_sponsor_key')->get();

            return \DataTables::of($data)
                    ->addColumn('user_name', function($row) {
                        return $row->name;
                    })
                    ->addColumn('email', function($row) {
                        return $row->email;
                    })
                    // ->addColumn('sponser', function($row) {
                    //     return $row->sponsor_id;
                    // })
                    // ->addColumn('side', function($row) {
                    //     return $row->mlm_side;
                    // })
                    ->addColumn('action', function($row) {
                        // $edit_btn = '<a href="'.\URL::route('admin.user.edit', [ 'id' => $row->id ]).'" class="btn btn-outline-info btn-elevate btn-circle btn-icon mt-1 mb-1 mr-2" title="'.\Lang::get($this->index.'edit_title').'"><i class="la la-edit"></i></a>';
                        $view_tree = '<a href="'.\URL::route('admin.mlm_tree_view.view', [ 'id' => $row->id ]).'"><button type="button" class="btn btn-primary waves-effect waves-light">View Downline</button></a>';

                        // $delete_btn = '<a href="javascript:deleteClient(\''.$row->id.'\')" id="delete_'.$row->id.'" data-url="'.\URL::route('admin.user.delete', [ 'id' => $row->id ]).'" class="btn btn-outline-danger btn-elevate btn-circle btn-icon mt-1 mb-1 mr-2" title="'.\Lang::get($this->index.'delete_title').'"><i class="la la-trash"></i></a>';
                        // $delete_btn = '<a href="javascript:deleteClient(\''.$row->id.'\')" id="delete_'.$row->id.'"  data-url="'.\URL::route('admin.user.delete', [ 'id' => $row->id ]).'" ><button type="button" class="btn btn-danger waves-effect waves-light">Delete</button></a>';

                        return $view_tree;
                    })
                    ->rawColumns(['action'])
                    ->make(true);


        } catch (Exception $e) {
            
        }
    }
  

}
