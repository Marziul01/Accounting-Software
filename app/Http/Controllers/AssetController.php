<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetSubCategory;
use App\Models\AssetTransaction;
use App\Models\Contact;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\AssetSubSubCategory;
use Illuminate\Support\Carbon;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        $assets = Asset::where('category_id', 1)->get();
        return view('admin.asset.index',[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 1)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
            'users' => Contact::all(),
        ]);
    }

    public function fixed(){

        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $assets = Asset::where('category_id', 2)->get();
        return view('admin.asset.fixed',[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 2)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
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
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
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
                $destinationPath = public_path('admin-assets/img/assets');
                $imageFile->move($destinationPath, $imageName);
                $photoPath = 'admin-assets/img/assets/' . $imageName;
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
                $contact->national_id = $request->national_id;
                $contact->father_name = $request->father_name;
                $contact->father_mobile = $request->father_mobile;
                $contact->mother_name = $request->mother_name;
                $contact->mother_mobile = $request->mother_mobile;
                $contact->spouse_name = $request->spouse_name;
                $contact->spouse_mobile = $request->spouse_mobile;
                $contact->present_address = $request->present_address;
                $contact->permanent_address = $request->permanent_address;
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
        // --- Slug logic ---
        $baseSlug = Str::slug($this->convertToEnglish($request->name));
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the assets table
        while (Asset::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;


        $assetsfdf = Asset::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Asset created successfully.',
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
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create .');
        }
        $request->validate([
            'name' => 'required',
            'subcategory_id' => 'required',
            'subsubcategory_id' => 'required',
            'amount' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $asset = Asset::findOrFail($id);
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
                    $destinationPath = public_path('admin-assets/img/assets');
                    $imageFile->move($destinationPath, $imageName);
                    $photoPath = 'admin-assets/img/assets/' . $imageName;
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
                $contact->national_id = $request->national_id;
                $contact->father_name = $request->father_name;
                $contact->father_mobile = $request->father_mobile;
                $contact->mother_name = $request->mother_name;
                $contact->mother_mobile = $request->mother_mobile;
                $contact->spouse_name = $request->spouse_name;
                $contact->spouse_mobile = $request->spouse_mobile;
                $contact->present_address = $request->present_address;
                $contact->permanent_address = $request->permanent_address;
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
            $destinationPath = public_path('admin-assets/img/assets');
            $imageFile->move($destinationPath, $imageName);
            $photoPath = 'admin-assets/img/assets/' . $imageName;
            $data['photo'] = $photoPath;

            // If contact exists, update its image too
            if (isset($contact) && $contact instanceof Contact) {
                $contact->image = $photoPath;
                $contact->save();
            }
        }

        // --- Update slug logic ---
        $baseSlug = Str::slug($this->convertToEnglish($request->name));
        $slug = $baseSlug;
        $counter = 1;
        // Check if slug exists in the assets table
        while (Asset::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;

        // --- Update asset (except amount) ---
        $asset->update($data);

        // --- Recalculate amount with transaction logic ---
        $finalAmount = $request->amount;

        $transactions = AssetTransaction::where('asset_id', $id)->get();

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
            'message' => 'Asset updated successfully.',
            'id' => $id,
        ]);
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (auth()->user()->access->asset != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete .');
        }
        $asset = Asset::findOrFail($id);
        // Delete the image file if it exists
        $oldPath = public_path($asset->photo);
        if ($asset->photo && file_exists($oldPath)) {
            unlink($oldPath);
        }
        $asset->delete();

        return back()->with('success', 'Asset deleted successfully.');
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
        $subCategories = AssetSubCategory::where('asset_category_id', $categoryId)->where('status', 1)->get();
        return response()->json($subCategories);
    }
    public function getSubSubcategories($subCategoryId)
    {
        $subSubCategories = AssetSubSubCategory::where('asset_sub_category_id', $subCategoryId)->where('status', 1)->get();
        return response()->json($subSubCategories);
    }
    
    public function report(Request $request)
    {
        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard');
        }

        $firstest = Asset::min('entry_date');
        $latest = Asset::max('entry_date');
        $firstesttransactions = AssetTransaction::min('transaction_date');
        $latesttransactions = AssetTransaction::max('transaction_date');
        $minDate = min(array_filter([$firstest, $firstesttransactions]));
        $maxDate = max(array_filter([$latest, $latesttransactions]));

        $categories = AssetCategory::with('assetSubCategories.assetSubSubCategories')->where('status',1)->get();
        $subSubcategories = AssetSubSubCategory::all();

        $startDate = $minDate ? Carbon::parse($minDate)->toDateString() : Carbon::now()->toDateString();
        $endDate = $maxDate ? Carbon::parse($maxDate)->toDateString() : Carbon::now()->toDateString();

        if ($request->has('start_date')) {
            $startDate = $request->input('start_date');
        }
        if ($request->has('end_date')) {
            $endDate = $request->input('end_date');
        }

        // Default category, subcategory, sub-subcategory
        $defaultCategory = $categories->first();
        $defaultSubcategory = $defaultCategory->assetSubCategories->where('status', 1)->first();
        $defaultSubsubcategory = $defaultSubcategory?->assetSubSubCategories->where('status', 1)->first();

        // Fetch assets under default selection
        $filteredAssets = Asset::query()
            ->where('category_id', $defaultCategory->id)
            ->where('subcategory_id', $defaultSubcategory->id)
            ->where('subsubcategory_id', $defaultSubsubcategory->id)
            ->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                }
            }])
            ->get();

        return view('admin.asset.report', [
            'categories' => $categories,
            'filteredAssets' => $filteredAssets,
            'subSubcategories' => $subSubcategories,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }


    public function filterasset(Request $request)
    {
        // Get all filters
        $categoryId = $request->input('category_id');
        $subcategoryId = $request->input('subcategory_id');
        $subsubcategoryId = $request->input('subsubcategory_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Base query
        $query = Asset::query();

        // Join relations
        $query->with(['category', 'subcategory']);

        // Apply filters
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        }

        if ($subsubcategoryId) {
            $query->where('subsubcategory_id', $subsubcategoryId);
        }

        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        // Get filtered data
        $assets = $query->get();

        // Return as JSON (for AJAX)
        return response()->json($assets->map(function ($asset) use ($startDate, $endDate) {
                    // Sum filtered transactions
                    $depositInRange = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                    $withdrawInRange = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                    $currentAmount = $depositInRange - $withdrawInRange;

                    // Sum all transactions
                    $totalDeposits = $asset->allTransactions->where('transaction_type', 'Deposit')->sum('amount');
                    $totalWithdrawals = $asset->allTransactions->where('transaction_type', 'Withdraw')->sum('amount');
                    $initialAmount = $asset->amount - $totalDeposits + $totalWithdrawals;

                    
                    

                    if ($startDate <= $asset->entry_date ) {
                        // Start date is before investment, or no previous transactions, so no previous amount
                        $currentAmount += $initialAmount;
                        $depositInRange += $initialAmount;
                        $previousAmount = null;
                    } else {
                        // Start date is on or after investment date, so calculate previous
                        $depositBeforeStart = $asset->allTransactions
                            ->where('transaction_type', 'Deposit')
                            ->where('transaction_date', '<', $startDate)
                            ->sum('amount');

                        $withdrawBeforeStart = $asset->allTransactions
                            ->where('transaction_type', 'Withdraw')
                            ->where('transaction_date', '<', $startDate)
                            ->sum('amount');

                        $previousAmount = $initialAmount + $depositBeforeStart - $withdrawBeforeStart;
                    }
            return [
                'slug' => $asset->slug,
                'category_name' => $asset->category->name ?? 'N/A',
                'subcategory_name' => $asset->subcategory->name ?? 'N/A',
                'name' => $asset->name,
                'description' => $asset->description,
                'value' => $currentAmount,
                'formatted_date' => \Carbon\Carbon::parse($asset->entry_date)->format('d M, Y'),
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        }));
    }

    public function fullAssetReport(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $categories = AssetCategory::where('status', 1)->get();

        $query = Asset::with(['category', 'subcategory','subsubcategory']);
        
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $assets = $query->orderBy('entry_date', 'desc')->get();

        return view('admin.asset.full_report', compact('categories', 'startDate', 'endDate' , 'assets'));
    }

    public function categoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date ;

        $category = AssetCategory::where('slug', $slug)->firstOrFail();

        $query = Asset::where('category_id', $category->id);
        
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $assets = $query->orderBy('entry_date', 'desc')->get();

        return view('admin.asset.category_report', compact('category', 'startDate', 'endDate' ,'assets'));
    }

    public function subcategoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ;
        $endDate = $request->end_date;

        $subcategory = AssetSubCategory::where('slug', $slug)->firstOrFail();

        $query = Asset::where('subcategory_id', $subcategory->id);
        
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $assets = $query->orderBy('entry_date', 'desc')->get();

        return view('admin.asset.subcategory_report', compact('subcategory', 'startDate', 'endDate' , 'assets'));
    }

    public function subsubcategoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date ;

        $subsubcategory = AssetSubSubCategory::where('slug', $slug)->firstOrFail();

        $query = Asset::where('subsubcategory_id', $subsubcategory->id);
        
        $query->with(['transactions' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            }
        }]);

        $assets = $query->orderBy('entry_date', 'desc')->get();

        return view('admin.asset.sub_subcategory_report', compact('subsubcategory', 'startDate', 'endDate' , 'assets'));
    }

    public function singleassetReport(Request $request ,$slug)
    {
        $asset = Asset::with(['transactions', 'subsubcategory.assetSubCategory.assetCategory'])->where('slug', $slug)->firstOrFail();

        $transactions = $asset->transactions()
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date')
            ->get();

        $startDate = $request->start_date;
        $endDate = $request->end_date ;

        return view('admin.asset.asset_report', compact('asset', 'startDate', 'endDate', 'transactions' ));
    }


}
