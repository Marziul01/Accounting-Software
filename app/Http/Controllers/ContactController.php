<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:contacts,slug',
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
        $contact = Contact::findOrFail($id);

        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:contacts,slug,' . $contact->id,
            'mobile_number' => 'required|string|max:15|unique:contacts,mobile_number,' . $contact->id,
            'email' => 'nullable|email|max:255|unique:contacts,email,' . $contact->id,
            'date_of_birth' => 'nullable|date',
            'marriage_date' => 'nullable|date',
            'sms_option' => 'required|boolean',
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

        // Check if slug exists in the contacts table
        while (Contact::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;

        $contact->update($data);

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
        $contact = Contact::findOrFail($id);

        if ($contact->image && file_exists(public_path($contact->image))) {
            unlink(public_path($contact->image));
        }

        $contact->delete();

        return back()->with('Contact deleted successfully.');
    }
}
