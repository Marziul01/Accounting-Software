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

        $categories = AssetCategory::with('assetSubCategories.assetSubSubCategories')->get();
        $subSubcategories = AssetSubSubCategory::all();

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        // Default category, subcategory, sub-subcategory
        $defaultCategory = $categories->first();
        $defaultSubcategory = $defaultCategory->assetSubCategories->first() ?? null;
        $defaultSubsubcategory = $defaultSubcategory?->assetSubSubCategories->first();

        // Fetch assets under default selection
        $filteredAssets = Asset::query()
            ->when($defaultCategory, fn($q) => $q->where('category_id', $defaultCategory->id))
            ->when($defaultSubcategory, fn($q) => $q->where('subcategory_id', $defaultSubcategory->id))
            ->when($defaultSubsubcategory, fn($q) => $q->where('subsubcategory_id', $defaultSubsubcategory->id))
            ->whereBetween('entry_date', [$startDate, $endDate])
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

        if ($startDate) {
            $query->whereDate('entry_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('entry_date', '<=', $endDate);
        }

        // Get filtered data
        $assets = $query->get();

        // Return as JSON (for AJAX)
        return response()->json($assets->map(function ($asset) {
            return [
                'slug' => $asset->slug,
                'category_name' => $asset->category->name ?? 'N/A',
                'subcategory_name' => $asset->subcategory->name ?? 'N/A',
                'name' => $asset->name,
                'description' => $asset->description,
                'value' => $asset->amount,
                'formatted_date' => \Carbon\Carbon::parse($asset->entry_date)->format('d M, Y'),
            ];
        }));
    }

    public function fullAssetReport(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $categories = AssetCategory::with([
            'assetSubCategories.assetSubSubCategories.assets' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->get();

        return view('admin.asset.full_report', compact('categories', 'startDate', 'endDate'));
    }

    public function categoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $category = AssetCategory::where('slug', $slug)->with([
            'assetSubCategories.assetSubSubCategories.assets' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->firstOrFail();

        return view('admin.asset.category_report', compact('category', 'startDate', 'endDate'));
    }

    public function subcategoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $subcategory = AssetSubCategory::where('slug', $slug)->with([
            'assetSubSubCategories.assets' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->firstOrFail();

        return view('admin.asset.subcategory_report', compact('subcategory', 'startDate', 'endDate'));
    }

    public function subsubcategoryReport(Request $request, $slug)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $subsubcategory = AssetSubSubCategory::where('slug', $slug)->with([
            'assets' => function ($query) use ($startDate, $endDate) {
                if ($startDate) {
                    $query->whereDate('entry_date', '>=', $startDate);
                }
                if ($endDate) {
                    $query->whereDate('entry_date', '<=', $endDate);
                }
            }
        ])->firstOrFail();

        return view('admin.asset.sub_subcategory_report', compact('subsubcategory', 'startDate', 'endDate'));
    }

    public function singleassetReport($slug)
    {
        $asset = Asset::with(['transactions', 'subsubcategory.assetSubCategory.assetCategory'])->where('slug', $slug)->firstOrFail();

        // Calculations
        $totalDeposit = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
        $initialAmount = $asset->amount - $totalDeposit + $totalWithdraw;
        $currentBalance = $asset->amount;

        return view('admin.asset.asset_report', compact('asset', 'totalDeposit', 'totalWithdraw', 'initialAmount', 'currentBalance'));
    }


}
