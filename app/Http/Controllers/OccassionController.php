<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\OcassionContact;
use App\Models\SMSEMAILTEMPLATE;
use App\Models\SMSTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OccassionController extends Controller
{
    public static function occassion(){
        if (Auth::user()->access->sms_and_email == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        return view('admin.occassion.index',[
            'occassions' => SMSEMAILTEMPLATE::all(),
            'contacts' => Contact::all(),
        ]);
    }

    public static function store(Request $request){

        if (Auth::user()->access->sms_and_email != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }

        $request->validate([
            'contact_ids' => 'required|array',
            'occasion_type' => 'required|string',
            'message' => 'required|string',
        ]);

        $occasion = new SMSEMAILTEMPLATE();
        $occasion->contact_ids = implode(',', $request->contact_ids);
        $occasion->occassion = $request->occasion_type === 'Custom' ? $request->custom_occasion : $request->occasion_type;
        $occasion->custom_date = $request->custom_date;
        $occasion->english = $request->english;

        if($request->english == 1){
            $occasion->message = $request->message;
        }else{
            $occasion->english_message = $request->message;
        }

        $occasion->save();

        $currentYear = now()->year;

        // Insert records for each contact
        foreach ($request->contact_ids as $contactId) {
            OcassionContact::create([
                'ocassion_id' => $occasion->id,
                'contact_id' => $contactId,
                'sented' => 1,
                'next_send' => $currentYear,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Occasion created successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->access->sms_and_email != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }

        $request->validate([
            'contact_ids' => 'required|array',
            'occasion_type' => 'required|string',
            'message' => 'required|string',
        ]);

        $occasion = SMSEMAILTEMPLATE::find($id);
        $occasion->contact_ids = implode(',', $request->contact_ids);
        $occasion->occassion = $request->occasion_type === 'Custom' ? $request->custom_occasion : $request->occasion_type;
        $occasion->custom_date = $request->custom_date;
        $occasion->english = $request->english;
        if($request->english == 1){
            $occasion->message = $request->message;
        }else{
            $occasion->english_message = $request->message;
        }
        $occasion->save();

        $newContactIds = $request->contact_ids;
        $existingContactIds = OcassionContact::where('ocassion_id', $occasion->id)
                                            ->pluck('contact_id')
                                            ->toArray();

        // ✅ Delete contacts that are no longer selected
        $toDelete = array_diff($existingContactIds, $newContactIds);
        if (!empty($toDelete)) {
            OcassionContact::where('ocassion_id', $occasion->id)
                        ->whereIn('contact_id', $toDelete)
                        ->delete();
        }

        // ✅ Add new contacts that didn’t exist before
        $toAdd = array_diff($newContactIds, $existingContactIds);
        $currentYear = now()->year;

        foreach ($toAdd as $contactId) {
            OcassionContact::create([
                'ocassion_id' => $occasion->id,
                'contact_id' => $contactId,
                'sented' => 1,
                'next_send' => $currentYear,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Occasion updated successfully.',
            'id' => $occasion->id,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (Auth::user()->access->sms_and_email != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }
        $occasion = SMSEMAILTEMPLATE::find($id);

        // Delete related occasion contacts first
        OcassionContact::where('ocassion_id', $occasion->id)->delete();
        
        $occasion->delete();

        return back()->with('success', 'Occasion deleted successfully.');
    }

}
