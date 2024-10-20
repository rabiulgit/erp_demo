<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use DB;

class LookupController extends Controller
{
    /* author: Md. Salaquzzaman
     * modified-date: 25-04-2024
     * purpose: Lookup List
     */
    public function lookupIndex()
    {
        if(\Auth::user())
        {
            $rows = DB::select('select * from settings order by id desc');
            return view('lookup.index', compact('rows'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /* author: Md. Salaquzzaman
     * modified-date: 25-04-2024
     * purpose: Lookup Create
     */
    public function lookupCreate()
    {
        return view('lookup.create');
    }

    /* author: Md. Salaquzzaman
     * modified-date: 25-04-2024
     * purpose: Lookup Create Action
     */
    public function lookupStore(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                    'lookupname' => 'required',
                    'lookupvalue' => 'required',
                ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $input    = $request->all();

        $lookupData = array(
            'name'              => $input['lookupname'],
            'value'             => $input['lookupvalue'],
            'created_by'        => \Auth::user()->id,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        );

        DB::table('settings')->insert([$lookupData]);

        return redirect()->route('lookupIndex')->with('success', __('Lookup created successfully.'));

    }

}
