<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Session;
use App\DB\UserMaster;
use App\DB\User;
use App\DB\Wallet;
use App\Commands\User\UserAccountStoreCommand;

class WalletController extends Controller
{
    public $view                = '';    

    public function __construct() {
        $this->view             = 'user.wallet.';                  
    }

    public function index(Request $request)
    {
        try {
            $data = [];

            $data['title']          = 'My Wallet';
            $data['page_title']     = 'My Wallet';
            $data['user']           = Sentinel::getUser();
            $data['user_master']    = UserMaster::getUserMaster($data['user']['user_master_id']);    
            return \View::make($this->view.'index', $data); 

        } catch (Exception $e) {
                
        }
    }

    public function all_transactions(Request $request)
    {
        try {
            $data = [];

            $data['title']          = 'My MLM';
            $data['page_title']     = 'My MLM';
            $data['user']           = Sentinel::getUser();
            $data['user_master']    = UserMaster::getUserMaster($data['user']['user_master_id']);    
            // $data['transaction_list']         = Wallet::where('user_id', $data['user']['id'])->where('user_master_id', $data['user_master']['id'])->get();
            return \View::make($this->view.'all_transactions', $data); 
        } catch (Exception $e) {
                
        }
    }

    public function all_transactions_get_list(Request $request)
    {
        try {
            $user           = Sentinel::getUser();
            $user_master    = UserMaster::getUserMaster($user['user_master_id']);    
            $data           = Wallet::where('user_id', $user['id'])->where('user_master_id', $user_master['id'])->get();
            return \DataTables::of($data)
                    ->addColumn('id', function($row) {
                        return $row->id;
                    })
                    ->addColumn('entry_date', function($row) {
                        return $row->entry_date;
                    })
                    ->addColumn('payment_detail', function($row) {
                        return $row->payment_detail;
                    })
                    ->addColumn('sending_or_receiving', function($row) {
                        if($row->sending_or_receiving == 0)
                        {
                            return  'Debit';    
                        } else {
                            return 'Credit';
                        }                        
                    })
                    ->addColumn('pay_amount', function($row) {
                        return $row->pay_amount;
                    })
                    // ->addColumn('action', function($row) {
                        
                    //     $edit_btn = '<a href="'.\URL::route('admin.user.edit', [ 'id' => $row->id ]).'"><button type="button" class="btn btn-primary waves-effect waves-light">Edit</button></a>';

                    //     $delete_btn = '<a href="javascript:deleteClient(\''.$row->id.'\')" id="delete_'.$row->id.'"  data-url="'.\URL::route('admin.user.delete', [ 'id' => $row->id ]).'" ><button type="button" class="btn btn-danger waves-effect waves-light">Delete</button></a>';

                    //     return $edit_btn." ".$delete_btn;
                    // })
                    // ->rawColumns(['action'])
                    ->make(true);


        } catch (Exception $e) {
            
        }
    }

    
  

}