<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\Mail\PromotionSend;
use App\Models\Promotion;
use App\Models\Utility;
use App\Models\Approval;
use App\Models\ApprovalStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage promotion'))
        {
            $isAdmin=0;
            $module='promotion';

            $user_id = Auth::id();

            $roleId = DB::table('model_has_roles')->where('model_id', $user_id)->value('role_id');
            $isApproval = Approval::where('module', $module)->where('role_id', $roleId)->get();

            if ($isApproval->isEmpty()) {
                if(\Auth::user()->type == 'company'){
                    $promotions = Promotion::where('created_by', '=', \Auth::user()->creatorId())->with(['designation','employee'])->get();
                }
                else{
                    $emp        = Employee::where('user_id', '=', \Auth::user()->id)->first();
                    $promotions = Promotion::where('created_by', '=', \Auth::user()->creatorId())->where('employee_id', '=', $emp->id)->with(['designation','employee'])->get();
                }

            } else {
                $isAdmin=1;
                $order = $isApproval[0]->order;
                $minOrder = Approval::where('module', $module)->min('order');
                if ($order == $minOrder) {
                    $promotions = Promotion::where('created_by', '=', \Auth::user()->creatorId())->with(['designation','employee'])->get();
                } else {
                    $promotions=[];
                    $prevRole = Approval::where('module', $module)->where('order', '<', $order)->orderBy('order', 'desc')->value('role_id');
    
                    $allPromotions = Promotion::where('created_by', '=', \Auth::user()->creatorId())->with(['designation','employee'])->get();

                    foreach ($allPromotions as $promotion) {
                        $approvalArray=ApprovalStatus::where('module', $module)->where('module_id', $promotion->id)
                            ->where('role_id', $prevRole)->where('status', 1)->get();

                        if(!$approvalArray->isEmpty()){
                            $promotions[]=$promotion;
                        }
                        
                    }
                }
            }
    
            foreach ($promotions as $promotion) {

                if(!$isApproval->isEmpty()){
                    $statusChecked=ApprovalStatus::where('module', $module)
                        ->where('module_id','=',$promotion->id)
                        ->where('role_id','=',$roleId)->first();
    
                    if(isset($statusChecked)){
                        $promotion->statusChecked=1;
                        $promotion->leaveStatus=$statusChecked->status;
                    }
                    else{
                        $promotion->statusChecked=0;
                    }
                }
            }

            return view('promotion.index', compact('promotions', 'isAdmin'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create promotion'))
        {
            $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            $employees    = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('promotion.create', compact('employees', 'designations'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create promotion'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'employee_id' => 'required',
                                   'designation_id' => 'required',
                                   'promotion_title' => 'required',
                                   'promotion_date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $promotion                  = new Promotion();
            $promotion->employee_id     = $request->employee_id;
            $promotion->designation_id  = $request->designation_id;
            $promotion->promotion_title = $request->promotion_title;
            $promotion->promotion_date  = $request->promotion_date;
            $promotion->description     = $request->description;
            $promotion->status          = 'Pending';
            $promotion->created_by      = \Auth::user()->creatorId();
            $promotion->save();

            $setings = Utility::settings();
            if($setings['promotion_sent'] == 1)
            {
                $employee               = Employee::find($promotion->employee_id);
                $designation            = Designation::find($promotion->designation_id);
                $promotion->designation = $designation->name;
                $promotionArr = [
                    'employee_name'=>$employee->name,
                    'promotion_designation'  =>$promotion->designation,
                    'promotion_title'  =>$promotion->promotion_title,
                    'promotion_date'  =>$promotion->promotion_date,

                ];

                $resp = Utility::sendEmailTemplate('promotion_sent', [$employee->email], $promotionArr);

                return redirect()->route('promotion.index')->with('success', __('Promotion  successfully created.'). ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

            return redirect()->route('promotion.index')->with('success', __('Promotion  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Promotion $promotion)
    {
        return redirect()->route('promotion.index');
    }

    public function edit(Promotion $promotion)
    {
        $designations = Designation::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        $employees    = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
        if(\Auth::user()->can('edit promotion'))
        {
            if($promotion->created_by == \Auth::user()->creatorId())
            {
                return view('promotion.edit', compact('promotion', 'employees', 'designations'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Promotion $promotion)
    {
        if(\Auth::user()->can('edit promotion'))
        {
            if($promotion->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'employee_id' => 'required',
                                       'designation_id' => 'required',
                                       'promotion_title' => 'required',
                                       'promotion_date' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $promotion->employee_id     = $request->employee_id;
                $promotion->designation_id  = $request->designation_id;
                $promotion->promotion_title = $request->promotion_title;
                $promotion->promotion_date  = $request->promotion_date;
                $promotion->description     = $request->description;
                $promotion->save();

                return redirect()->route('promotion.index')->with('success', __('Promotion successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Promotion $promotion)
    {
        if(\Auth::user()->can('delete promotion'))
        {
            if($promotion->created_by == \Auth::user()->creatorId())
            {
                $promotion->delete();

                return redirect()->route('promotion.index')->with('success', __('Promotion successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function approvePromotion($id)
    {
        $userId=auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "promotion",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 1
        ]);

        $totalApproval = Approval::where('module', 'promotion')->count();

        $totalApproved = ApprovalStatus::where('module', 'promotion')->where('module_id', $id)->count();

        if($totalApproval == $totalApproved){
            Promotion::where('id', $id)->update(['status' => 'Approved']);
        }

        return redirect()->route('promotion.index')->with('success', __('Promotion Approved.'));
    }

    public function rejectPromotion($id)
    {
        $userId=auth()->user()->id;
        $roleId = DB::table('model_has_roles')->where('model_id', $userId)->value('role_id');

        ApprovalStatus::create([
            "module" => "promotion",
            "module_id" => $id,
            "user_id" => $userId,
            "role_id" => $roleId,
            "status" => 0
        ]);

        Promotion::where('id', $id)->update(['status' => 'Rejected']);

        return redirect()->route('promotion.index')->with('success', __('Promotion Rejected.'));
    }

}
