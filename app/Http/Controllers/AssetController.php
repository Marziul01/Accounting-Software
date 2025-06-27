<?php

namespace App\Http\Controllers;

use App\Mail\AssetInvoiceMail;
use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetSubCategory;
use App\Models\AssetTransaction;
use App\Models\Contact;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\AssetSubSubCategory;
use App\Models\SiteSetting;
use App\Models\SMSTemplate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

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
        $assets = Asset::where('category_id', 4)->get();
        return view('admin.asset.index',[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 4)->where('status', 1)->get(),
            'assetTransactions' => AssetTransaction::all(),
            'users' => Contact::all(),
        ]);
    }

    public function fixed(){

        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $assets = Asset::where('category_id', 5)->get();
        return view('admin.asset.fixed',[
            'assets' => $assets,
            'assetCategories' => AssetSubCategory::where('asset_category_id', 5)->where('status', 1)->get(),
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
            'entry_date' => 'required',
            'amount' => 'required|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_name' => 'required_if:category_id,4|nullable|string',
            'mobile' => 'required_if:category_id,4|nullable|string',
            'email' => 'nullable|email',
            'contact_id' => 'required_if:category_id,4|nullable|exists:contacts,id',
            
        ]);

        $data = $request->except('amount');
        $photoPath = null;

        if($request->category_id == 4){
            // If contact_id is provided, use contact's image as asset photo
            if (!empty($request->contact_id)) {
                $contact = Contact::find($request->contact_id);
                if ($contact) {
                    $contact->email = $request->email;
                    $contact->national_id = $request->national_id;
                    $contact->father_name = $request->father_name;
                    $contact->father_mobile = $request->father_mobile;
                    $contact->mother_name = $request->mother_name;
                    $contact->mother_mobile = $request->mother_mobile;
                    $contact->spouse_name = $request->spouse_name;
                    $contact->spouse_mobile = $request->spouse_mobile;
                    $contact->present_address = $request->present_address;
                    $contact->permanent_address = $request->permanent_address;
                    $contact->sms_option = $request->send_sms;
                    $contact->send_email = $request->send_email;
                    $data['photo'] = $contact->image;
                    $contact->save();
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
                    ->first();

                if ($existingContact) {
                    $data['contact_id'] = $existingContact->id;
                    $data['photo'] = $existingContact->image;
                    $existingContact->email = $request->email;
                    $existingContact->national_id = $request->national_id;
                    $existingContact->father_name = $request->father_name;
                    $existingContact->father_mobile = $request->father_mobile;
                    $existingContact->mother_name = $request->mother_name;
                    $existingContact->mother_mobile = $request->mother_mobile;
                    $existingContact->spouse_name = $request->spouse_name;
                    $existingContact->spouse_mobile = $request->spouse_mobile;
                    $existingContact->present_address = $request->present_address;
                    $existingContact->permanent_address = $request->permanent_address;
                    $existingContact->sms_option = $request->send_sms;
                    $existingContact->send_email = $request->send_email;
                    $existingContact->save();
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
                    $contact->sms_option = $request->send_sms;
                    $contact->send_email = $request->send_email;
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

        $firsttransaction = new AssetTransaction();
        $firsttransaction->asset_id = $assetsfdf->id;
        $firsttransaction->amount = $request->amount;
        $firsttransaction->transaction_type = 'Deposit';
        $firsttransaction->transaction_date = $request->entry_date;
        $firsttransaction->save();

        if($request->category_id == 4){

            if ($request->send_sms == 1 && $request->mobile) {
                $number = '88'.$request->mobile;
                $body = SMSTemplate::find(1);
                $templateText = $body?->body ?? '';
                $site_name = SiteSetting::find(1);
                $accountName = $assetsfdf->name;
                $accountNumber = '#'.$assetsfdf->slug.$assetsfdf->id; // or $assetsfdf->id if you prefer
                $amount = $this->engToBnNumber($request->amount);

                $message = "প্রিয় {$accountName}, $templateText {$accountNumber} । গৃহীত ঋণের পরিমাণ $amount টাকা । 

ধন্যবাদান্তে,

$site_name->site_owner";

                $response = sendSMS($number, $message);

                // Optional: Map response code to readable message
                $errorMessages = [
                    '1001' => '❌ ভুল API কী প্রদান করা হয়েছে।',
                    '1002' => '❌ ভুল Sender ID ব্যবহার করা হয়েছে।',
                    '1003' => '❌ টাইপ অবশ্যই text অথবা unicode হতে হবে।',
                    '1004' => '❌ শুধুমাত্র GET বা POST মেথড অনুমোদিত।',
                    '1005' => '❌ এই prefix এ SMS পাঠানো সম্ভব নয় কারণ এটি নিষ্ক্রিয়।',
                    '1006' => '❌ অ্যাকাউন্টে পর্যাপ্ত ব্যালেন্স নেই।',
                    '1007' => '❌ মোবাইল নম্বর অবশ্যই country code (88) দিয়ে শুরু হতে হবে।',
                ];

                if (isset($errorMessages[$response])) {
                    session()->flash('error', $errorMessages[$response]);
                }
            }


            if ($request->send_email == 1) {
                Mail::to($request->email)->send(new AssetInvoiceMail($assetsfdf, $request));
            }

        }

        return response()->json([
            'status' => 'success',
            'message' => 'Asset created successfully.',
            'id' => $assetsfdf->id
        ]);
    }

    public function engToBnNumber($number) {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn  = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $bn, $number);
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
            'entry_date' => 'required',
           'contact_id' => 'required_if:category_id,4|nullable|exists:contacts,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_name' => 'required_if:category_id,4|nullable|string',
            'mobile' => 'required_if:category_id,4|nullable|string',
        ]);

        $asset = Asset::findOrFail($id);
        $data = $request->except('amount'); // Exclude amount from update
        $photoPath = null;

        if($request->category_id == 4){
            // --- Contact logic ---
            if (!empty($request->contact_id)) {
                $contact = Contact::find($request->contact_id);
                if ($contact) {
                    // ✅ Update contact details from the request
                    $contact->name = $request->user_name;
                    $contact->slug = $request->slug;
                    $contact->mobile_number = $request->mobile;
                    $contact->email = $request->email;
                    $contact->national_id = $request->national_id;
                    $contact->father_name = $request->father_name;
                    $contact->father_mobile = $request->father_mobile;
                    $contact->mother_name = $request->mother_name;
                    $contact->mother_mobile = $request->mother_mobile;
                    $contact->spouse_name = $request->spouse_name;
                    $contact->spouse_mobile = $request->spouse_mobile;
                    $contact->present_address = $request->present_address;
                    $contact->permanent_address = $request->permanent_address;
                    $contact->sms_option = $request->send_sms;
                    $contact->send_email = $request->send_email;

                    // ✅ If new photo is uploaded, replace the old one
                    if ($request->hasFile('photo')) {
                        if ($asset->photo && file_exists(public_path($asset->photo))) {
                            unlink(public_path($asset->photo));
                        }

                        $imageFile = $request->file('photo');
                        $imageName = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                        $destinationPath = public_path('admin-assets/img/assets');
                        $imageFile->move($destinationPath, $imageName);
                        $photoPath = 'admin-assets/img/assets/' . $imageName;
                        $contact->image = $photoPath;
                        $data['photo'] = $photoPath;
                    } else {
                        // Use contact's existing image if no new photo
                        $data['photo'] = $contact->image;
                    }

                    $contact->save();

                    // ✅ Assign updated contact ID
                    $data['contact_id'] = $contact->id;
                }
            } else {
                // If no contact_id, try to find or create contact
                $existingContact = Contact::where('name', $request->user_name)
                    ->where('mobile_number', $request->mobile)
                    ->first();

                if ($existingContact) {
                    $data['contact_id'] = $existingContact->id;
                    $data['photo'] = $existingContact->image;
                    $existingContact->email = $request->email;
                    $existingContact->national_id = $request->national_id;
                    $existingContact->father_name = $request->father_name;
                    $existingContact->father_mobile = $request->father_mobile;
                    $existingContact->mother_name = $request->mother_name;
                    $existingContact->mother_mobile = $request->mother_mobile;
                    $existingContact->spouse_name = $request->spouse_name;
                    $existingContact->spouse_mobile = $request->spouse_mobile;
                    $existingContact->present_address = $request->present_address;
                    $existingContact->permanent_address = $request->permanent_address;
                    $existingContact->sms_option = $request->send_sms;
                    $existingContact->send_email = $request->send_email;
                    $existingContact->save();
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
                    $contact->sms_option = $request->send_sms;
                    $contact->send_email = $request->send_email;
                    $contact->save();

                    $data['contact_id'] = $contact->id;
                }
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

        $transactions = AssetTransaction::where('asset_id' , $asset->id )->get();
        foreach ($transactions as $transaction) {
            // Delete each transaction
            $transaction->delete();
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
    // public function getSubSubcategories($subCategoryId)
    // {
    //     $subSubCategories = AssetSubSubCategory::where('asset_sub_category_id', $subCategoryId)->where('status', 1)->get();
    //     return response()->json($subSubCategories);
    // }
    
    public function report(Request $request)
    {
        if (auth()->user()->access->asset == 3) {
            return redirect()->route('admin.dashboard');
        }

        $firstest = Asset::min('entry_date');
        $latest = Asset::max('entry_date');
        $firstesttransactions = AssetTransaction::min('transaction_date');
        $latesttransactions = AssetTransaction::max('transaction_date');
        // Filter out nulls
        $minDates = array_filter([$firstest, $firstesttransactions]);
        $maxDates = array_filter([$latest, $latesttransactions]);

        $minDate = !empty($minDates) ? min($minDates) : null;
        $maxDate = !empty($maxDates) ? max($maxDates) : null;

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
        $defaultCategory = $categories->first() ?? null;
        if($defaultCategory && $defaultCategory->assetSubCategories){
            $defaultSubcategory = $defaultCategory->assetSubCategories->where('status', 1)->first() ?? null;
        }
        
        

        // Fetch assets under default selection
        $filteredAssets = Asset::query()
            ->where('category_id', $defaultCategory->id ?? null )
            ->where('subcategory_id', $defaultSubcategory->id ?? null)
            
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
        // $subsubcategoryId = $request->input('subsubcategory_id');
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

        // if ($subsubcategoryId) {
        //     $query->where('subsubcategory_id', $subsubcategoryId);
        // }

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

                    $initialAmount = $asset->allTransactions->first()->amount ?? 0;

                    if ($asset->allTransactions->isNotEmpty() && $asset->allTransactions->first()->transaction_date >= $startDate) {
                        // Start date is before investment, or no previous transactions, so no previous amount
                        $previousAmount = $initialAmount;
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

                        $previousAmount = $depositBeforeStart - $withdrawBeforeStart;
                    }
            return [
                'slug' => $asset->slug,
                'category_name' => $asset->category->name ?? 'N/A',
                'subcategory_name' => $asset->subcategory->name ?? 'N/A',
                'name' => $asset->name,
                'description' => $asset->description,
                'value' => number_format($currentAmount,2),
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
