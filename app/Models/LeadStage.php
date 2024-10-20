<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    protected $fillable = [
        'name',
        'pipeline_id',
        'created_by',
        'order',
    ];

    public function lead()
    {
        if(\Auth::user()->type=='company'){
            return Lead::select('leads.*')->where('leads.created_by', '=', \Auth::user()->creatorId())->where('leads.stage_id', '=', $this->id)->orderBy('leads.order')->get();
        }else{
            $employee = Employee::where('user_id', \Auth::user()->id)->first();

            if(!empty($employee->team_lead) && !empty($employee->team_id)){
                if($employee->team_lead == '1'){
                    
                    return Lead::select('leads.*')
                    ->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')
                    ->join('employees', 'employees.user_id', '=', 'user_leads.user_id')
                    ->where('employees.team_id', '=', $employee->team_id)
                    ->where('leads.stage_id', '=', $this->id)
                    ->distinct()
                    ->orderBy('leads.order')
                    ->get();
                }
            }

            return Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', \Auth::user()->id)->where('leads.stage_id', '=', $this->id)->orderBy('leads.order')->get();

        }

    }
}
