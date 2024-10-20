<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage branch'))
        {
            $teams = Team::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('team.index', compact('teams'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create branch'))
        {
            return view('team.create');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create branch'))
        {

            $validator = \Validator::make(
                $request->all(), [
                                   'name' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $team             = new Team();
            $team->name       = $request->name;
            $team->created_by = \Auth::user()->creatorId();
            $team->save();

            return redirect()->route('team.index')->with('success', __('Team  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Team $team)
    {
        return redirect()->route('team.index');
    }

    public function edit(Team $team)
    {
        if(\Auth::user()->can('edit branch'))
        {
            if($team->created_by == \Auth::user()->creatorId())
            {

                return view('team.edit', compact('team'));
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

    public function update(Request $request, Team $team)
    {
        if(\Auth::user()->can('edit branch'))
        {
            if($team->created_by == \Auth::user()->creatorId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $team->name = $request->name;
                $team->save();

                return redirect()->route('team.index')->with('success', __('Team successfully updated.'));
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

    public function destroy(Team $team)
    {
        if(\Auth::user()->can('delete branch'))
        {
            if($team->created_by == \Auth::user()->creatorId())
            {
                $team->delete();

                return redirect()->route('team.index')->with('success', __('Team successfully deleted.'));
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

}
