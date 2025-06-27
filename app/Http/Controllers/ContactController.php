<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Contact;
use App\Models\Liability;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(Auth::user()->access->contact == 3 ){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        return view('admin.contact.contact',[
            'contacts' => Contact::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        if(Auth::user()->access->contact != 2){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
        }

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:contacts,mobile_number',
            'email' => 'nullable|email|max:255|unique:contacts,email',
            'date_of_birth' => 'nullable|date',
            'marriage_date' => 'nullable|date',
            'sms_option' => 'nullable|boolean',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {

            $imageFile = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('admin-assets/img/contacts');
            $imageFile->move($destinationPath, $imageName);
            $photoPath = 'admin-assets/img/contacts/' . $imageName;
            $data['image'] = $photoPath;
        }

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug exists in the contacts table
        while (Contact::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;

        $contact = Contact::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

         if(Auth::user()->access->contact != 2){
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update .');
        }

        $contact = Contact::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15|unique:contacts,mobile_number,' . $contact->id,
            'email' => 'nullable|email|max:255|unique:contacts,email,' . $contact->id,
            'date_of_birth' => 'nullable|date',
            'marriage_date' => 'nullable|date',
            'sms_option' => 'nullable|boolean',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {

            if ($contact->photo && file_exists(public_path($contact->photo))) {
                unlink(public_path($contact->image));
            }

            $imageFile = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('admin-assets/img/contacts');
            $imageFile->move($destinationPath, $imageName);
            $photoPath = 'admin-assets/img/contacts/' . $imageName;
            $data['image'] = $photoPath;
        }

        $baseSlug = $request->slug;
        $slug = $baseSlug;
        $counter = 1;
        while (
            Contact::where('slug', $slug)->where('id', '!=', $contact->id)->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;

        $contact->update($data);

        // 2. Update related Assets
        Asset::where('contact_id', $contact->id)->update([
            'user_name'           => $contact->name,
            'name'                => $contact->name,
            'slug'                => $contact->slug,
            'mobile'              => $contact->mobile_number,
            'photo'               => $contact->image,
            'national_id'         => $contact->national_id,
            'father_name'         => $contact->father_name,
            'father_mobile'       => $contact->father_mobile,
            'mother_name'         => $contact->mother_name,
            'mother_mobile'       => $contact->mother_mobile,
            'spouse_name'         => $contact->spouse_name,
            'spouse_mobile'       => $contact->spouse_mobile,
            'present_address'     => $contact->present_address,
            'permanent_address'   => $contact->permanent_address,
            'send_sms'          => $contact->sms_option,
            'send_email'          => $contact->send_email,
        ]);

        // Sync related Liabilities
        Liability::where('contact_id', $contact->id)->update([
            'user_name'           => $contact->name,
            'name'                => $contact->name,
            'slug'                => $contact->slug,
            'mobile'              => $contact->mobile_number,
            'photo'               => $contact->image,
            'national_id'         => $contact->national_id,
            'father_name'         => $contact->father_name,
            'father_mobile'       => $contact->father_mobile,
            'mother_name'         => $contact->mother_name,
            'mother_mobile'       => $contact->mother_mobile,
            'spouse_name'         => $contact->spouse_name,
            'spouse_mobile'       => $contact->spouse_mobile,
            'present_address'     => $contact->present_address,
            'permanent_address'   => $contact->permanent_address,
            'send_sms'          => $contact->sms_option,
            'send_email'          => $contact->send_email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully.',
            'id' => $contact->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->access->contact != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete.');
        }

        $contact = Contact::findOrFail($id);

        // ✅ Check if this contact is used in any Asset or Liability
        $isUsedInAssets = Asset::where('contact_id', $id)->exists();
        $isUsedInLiabilities = Liability::where('contact_id', $id)->exists();

        if ($isUsedInAssets || $isUsedInLiabilities) {
            return back()->with('error', 'Cannot delete this contact. It is associated with existing assets or liabilities.');
        }

        // ✅ Delete the image if exists
        if ($contact->image && file_exists(public_path($contact->image))) {
            unlink(public_path($contact->image));
        }

        // ✅ Delete the contact
        $contact->delete();

        return back()->with('success', 'Contact deleted successfully.');
    }

}
