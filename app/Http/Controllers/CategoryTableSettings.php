<?php

namespace App\Http\Controllers;

use App\Models\UserAccess;
use Illuminate\Http\Request;

class CategoryTableSettings extends Controller
{
    public static function categoryTableSettings()
    {
        if (auth()->user()->access->admin_panel == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all bank accounts from the database
        $fieldStatuses = UserAccess::first();

        // Return the view with the bank accounts data
        return view('admin.settings.categoryTableSettings', [
            'fieldStatuses' => $fieldStatuses,
        ]);
    }

    public function updateCategoryField(Request $request)
    {
        $field = $request->input('field');
        $status = $request->input('status');

        // Allow only these fields to be updated
        $allowedFields = [
            'asset_category_table',
            'asset_name_table',
            'asset_category',
            'asset_subcategory',
            'liability_category_table',
            'liability_name_table',
            'liability_category',
            'liability_subcategory',
            'income_category',
            'expense_category',
            'investments_category'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field update attempt.']);
        }

        $userAccess = UserAccess::first(); // You can also add `->where('user_id', auth()->id())` if needed

        $userAccess->$field = $status;
        $userAccess->save();

        return response()->json(['success' => true, 'message' => 'Setting updated successfully.']);
    }
}
