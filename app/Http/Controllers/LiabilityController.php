<?php

namespace App\Http\Controllers;

use App\Models\Liability;
use App\Models\LiabilitySubCategory;
use App\Models\LiabilityTransaction;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\LiabilityCategory;
use App\Models\LiabilitySubSubCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LiabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->access->liability == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        $Liabilities = Liability::where('category_id', 1)->get();
        return view('admin.liability.index',[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 1)->where('status', 1)->get(),
            'liabilityTransactions' => LiabilityTransaction::all(),
            'users' => Contact::all(),
        ]);
    }

    public function fixed(){
        if (Auth::user()->access->liability == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $Liabilities = Liability::where('category_id', 2)->get();
        return view('admin.liability.fixed',[
            'liabilities' => $Liabilities,
            'liabilityCategories' => LiabilitySubCategory::where('liability_category_id', 2)->where('status', 1)->get(),
            'liabilityTransactions' => LiabilityTransaction::all(),
            'users' => Contact::all(),
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
        if (Auth::user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }
        $request->validate([
            'name' => 'required',
            'subcategory_id' => 'required',
            'subsubcategory_id' => 'required',
            'amount' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_name' => 'nullable|string',
            'mobile' => 'nullable|string',
            'email' => 'nullable|email',
            'contact_id' => 'nullable|exists:contacts,id',

        ]);

        $data = $request->all();
        $photoPath = null;

        // If contact_id is provided, use contact's image as asset photo
        if (!empty($request->contact_id)) {
            $contact = Contact::find($request->contact_id);
            if ($contact) {
                $data['photo'] = $contact->image;
            }
        } else {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $imageFile = $request->file('photo');
                $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $destinationPath = public_path('admin-assets/img/Liability');
                $imageFile->move($destinationPath, $imageName);
                $photoPath = 'admin-assets/img/Liability/' . $imageName;
                $data['photo'] = $photoPath;
            }

            // Find or create contact
            $existingContact = Contact::where('name', $request->user_name)
                ->where('mobile_number', $request->mobile)
                ->where('email', $request->email)
                ->first();

            if ($existingContact) {
                $data['contact_id'] = $existingContact->id;
                $data['photo'] = $existingContact->image;
            } else {
                $baseSlug = Str::slug($this->convertToEnglish($request->user_name));
                $slug = $baseSlug;
                $counter = 1;

                // Check if slug exists in the contacts table
                while (Contact::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $contact = new Contact();
                $contact->name = $request->user_name;
                $contact->mobile_number = $request->mobile;
                $contact->email = $request->email;
                $contact->slug = $slug;
                if ($photoPath) {
                    $contact->image = $photoPath;
                }
                $contact->save();
                $data['contact_id'] = $contact->id;
            }
        }
        // If contact_id exists but no photo uploaded, do not override photo
        if ($request->hasFile('photo') && empty($data['photo'])) {
            if ($photoPath) {
                $data['photo'] = $photoPath;
            }
        }
        
        // Generate a unique slug for the liability
        $baseSlug = Str::slug($this->convertToEnglish($request->name));
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the liabilities table
        while (Liability::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;

        $assetsfdf = Liability::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Liability created successfully.',
            'id' => $assetsfdf->id
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
    public function update(Request $request, $id)
    {
        if (Auth::user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }
        $request->validate([
            'name' => 'required',
            'subcategory_id' => 'required',
            'subsubcategory_id' => 'required',
            'amount' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $asset = Liability::findOrFail($id);
        $data = $request->except('amount'); // Exclude amount from update
        $photoPath = null;

        // --- Contact logic ---
        if (!empty($request->contact_id)) {
            $contact = Contact::find($request->contact_id);
            if ($contact) {
                $data['photo'] = $contact->image;
                $data['contact_id'] = $contact->id;
            }
        } else {
            // If no contact_id, try to find or create contact
            $existingContact = Contact::where('name', $request->user_name)
                ->where('mobile_number', $request->mobile)
                ->where('email', $request->email)
                ->first();

            if ($existingContact) {
                $data['contact_id'] = $existingContact->id;
                $data['photo'] = $existingContact->image;
            } else {
                // Upload new image if available
                if ($request->hasFile('photo')) {
                    if ($asset->photo && file_exists(public_path($asset->photo))) {
                        unlink(public_path($asset->photo));
                    }

                    $imageFile = $request->file('photo');
                    $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                    $destinationPath = public_path('admin-assets/img/Liability');
                    $imageFile->move($destinationPath, $imageName);
                    $photoPath = 'admin-assets/img/Liability/' . $imageName;
                    $data['photo'] = $photoPath;
                }

                // Create new contact
                $baseSlug = Str::slug($this->convertToEnglish($request->user_name));
                $slug = $baseSlug;
                $counter = 1;

                // Check if slug exists in the contacts table
                while (Contact::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $contact = new Contact();
                $contact->name = $request->user_name;
                $contact->mobile_number = $request->mobile;
                $contact->email = $request->email;
                $contact->slug = $slug;
                if ($photoPath) {
                    $contact->image = $photoPath;
                }
                $contact->save();

                $data['contact_id'] = $contact->id;
            }
        }

        // If contact_id exists but no photo uploaded, do not override photo
        if ($request->hasFile('photo') && empty($data['photo'])) {
            if ($asset->photo && file_exists(public_path($asset->photo))) {
                unlink(public_path($asset->photo));
            }

            $imageFile = $request->file('photo');
            $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            $destinationPath = public_path('admin-assets/img/Liability');
            $imageFile->move($destinationPath, $imageName);
            $photoPath = 'admin-assets/img/Liability/' . $imageName;
            $data['photo'] = $photoPath;

            // If contact exists, update its image too
            if (isset($contact) && $contact instanceof Contact) {
                $contact->image = $photoPath;
                $contact->save();
            }
        }
        // --- Generate a unique slug for the liability ---
        $baseSlug = Str::slug($this->convertToEnglish($request->name));
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the liabilities table
        while (Liability::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;

        // --- Update asset (except amount) ---
        $asset->update($data);

        // --- Recalculate amount with transaction logic ---
        $finalAmount = $request->amount;

        $transactions = LiabilityTransaction::where('liability_id', $id)->get();

        foreach ($transactions as $transaction) {
            if ($transaction->transaction_type === 'Deposit') {
                $finalAmount += $transaction->amount;
            } elseif ($transaction->transaction_type === 'Withdraw') {
                $finalAmount -= $transaction->amount;
            }
        }

        $asset->amount = $finalAmount;
        $asset->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Liability updated successfully.',
            'id' => $id,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->access->liability != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission.');
        }
        $asset = Liability::findOrFail($id);
        // Delete the image file if it exists
        $oldPath = public_path($asset->photo);
        if ($asset->photo && file_exists($oldPath)) {
            unlink($oldPath);
        }
        $asset->delete();

        return back()->with('success', 'liability deleted successfully.');
    }

    private function convertToEnglish($text)
    {
        // You can enhance this map as needed
        $bangla = ['অ','আ','ই','ঈ','উ','ঊ','ঋ','এ','ঐ','ও','ঔ','ক','খ','গ','ঘ','ঙ','চ','ছ','জ','ঝ','ঞ','ট','ঠ','ড','ঢ','ণ','ত','থ','দ','ধ','ন','প','ফ','ব','ভ','ম','য','র','ল','শ','ষ','স','হ','ড়','ঢ়','য়','ৎ','ং','ঃ','ঁ'];
        $english = ['a','a','i','ii','u','uu','ri','e','oi','o','ou','k','kh','g','gh','ng','ch','chh','j','jh','n','t','th','d','dh','n','t','th','d','dh','n','p','ph','b','bh','m','y','r','l','sh','ss','s','h','r','rh','y','t','ng','h','n'];

        return str_replace($bangla, $english, $text);
    }

    public function getSubcategories($categoryId)
    {
        $subCategories = LiabilitySubCategory::where('liability_category_id', $categoryId)->where('status', 1)->get();
        return response()->json($subCategories);
    }
    public function getSubSubcategories($subCategoryId)
    {
        $subSubCategories = LiabilitySubSubCategory::where('liability_sub_category_id', $subCategoryId)->where('status', 1)->get();
        return response()->json($subSubCategories);
    }


    public function report(Request $request)
    {
        if (auth()->user()->access->liability == 3) {
            return redirect()->route('admin.dashboard.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $categories = LiabilityCategory::with('liabilitySubCategories.liabilitySubSubCategories')->get();
        $subSubcategories = LiabilitySubSubCategory::all(); // If you have subsubcategories model, fetch here

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // Default category, subcategory, sub-subcategory
        $defaultCategory = $categories->first();
        $defaultSubcategory = $defaultCategory?->liabilitySubCategories->first();
        $defaultSubsubcategory = $defaultSubcategory?->liabilitySubSubCategories->first();

        // Fetch liabilities under default selection
        $filteredLiabilities = Liability::query()
            ->when($defaultCategory, fn($q) => $q->where('category_id', $defaultCategory->id))
            ->when($defaultSubcategory, fn($q) => $q->where('subcategory_id', $defaultSubcategory->id))
            ->when($defaultSubsubcategory, fn($q) => $q->where('subsubcategory_id', $defaultSubsubcategory->id))
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->get();

        return view('admin.liability.report', [
            'categories' => $categories,
            'filteredLiabilities' => $filteredLiabilities,
            'subSubcategories' => $subSubcategories,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function filterliability(Request $request)
    {
        // Get all filters
        $subcategoryId = $request->input('subcategory_id');
        $subsubcategoryId = $request->input('subsubcategory_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base query
        $query = Liability::query();

        // Join relations
        $query->with(['subcategory']);

        // Apply filters
        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        }

        if ($subsubcategoryId) {
            $query->where('subsubcategory_id', $subsubcategoryId);
        }

        if ($startDate) {
            $query->whereDate('entry_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('entry_date', '<=', $endDate);
        }

        // Get filtered data
        $liabilities = $query->get();

        // Return as JSON (for AJAX)
        return response()->json($liabilities->map(function ($liability) {
            return [
                'slug' => $liability->slug,
                'subcategory_name' => $liability->subcategory->name ?? 'N/A',
                'name' => $liability->name,
                'description' => $liability->description,
                'value' => $liability->amount,
                'formatted_date' => \Carbon\Carbon::parse($liability->entry_date)->format('d M, Y'),
            ];
        }));
    }

    public function fullLiabilityReport(Request $request)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $categories = LiabilityCategory::with([
            'liabilitySubCategories.liabilitySubSubCategories.liabilities' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->get();

        return view('admin.liability.full_report', compact('categories', 'startDate', 'endDate'));
    }

    public function categoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $category = LiabilityCategory::where('slug', $slug)->with([
            'liabilitySubCategories.liabilitySubSubCategories.liabilities' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->firstOrFail();

        return view('admin.liability.category_report', compact('category', 'startDate', 'endDate'));
    }

    public function subcategoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $subcategory = LiabilitySubCategory::where('slug', $slug)->with([
            'liabilitySubSubCategories.liabilities' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->firstOrFail();

        return view('admin.liability.subcategory_report', compact('subcategory', 'startDate', 'endDate'));
    }

    public function subsubcategoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : null;

        $subsubcategory = null;
        if (class_exists('\App\Models\LiabilitySubSubCategory')) {
            $subsubcategory = \App\Models\LiabilitySubSubCategory::where('slug', $slug)->with([
                'liabilities' => function ($query) use ($startDate, $endDate) {
                    if ($startDate) {
                        $query->whereDate('entry_date', '>=', $startDate);
                    }
                    if ($endDate) {
                        $query->whereDate('entry_date', '<=', $endDate);
                    }
                }
            ])->firstOrFail();
        }

        return view('admin.liability.sub_subcategory_report', compact('subsubcategory', 'startDate', 'endDate'));
    }

    public function singleliabilityReport($slug)
    {
        $liability = Liability::with(['transactions', 'subsubcategory','subcategory' ,'category' ])->where('slug', $slug)->firstOrFail();

        // Calculations
        $totalDeposit = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');
        $initialAmount = $liability->amount - $totalDeposit + $totalWithdraw;
        $currentBalance = $liability->amount;

        return view('admin.liability.liability_report', compact('liability', 'totalDeposit', 'totalWithdraw', 'initialAmount', 'currentBalance'));
    }
}
