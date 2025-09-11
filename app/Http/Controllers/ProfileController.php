<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Home;
use App\Models\SiteSetting;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public static function profile(){
        return view('admin.profile.profile',[

        ]);
    }

    public static function siteSettings(){
        return view('admin.profile.siteSettings',[

        ]);
    }

    public static function homesettings(){
        return view('admin.profile.homesettings',[
            'home' => Home::find(1),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,mobile,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // ✅ important: return 422
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully!']);
    }

    public function update(Request $request)
    {
        $setting = SiteSetting::first(); // Assuming only 1 record

        $validator = Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'site_phone' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:255',
            'site_owner' => 'nullable|string|max:255',
            'site_link' => 'nullable|string',
            'signature' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'site_favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico|max:512',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Manually update non-file fields
        $setting->site_name = $request->site_name;
        $setting->site_name_bangla = $request->site_name_bangla;
        $setting->site_email = $request->site_email;
        $setting->site_phone = $request->site_phone;
        $setting->site_address = $request->site_address;
        $setting->site_owner = $request->site_owner;
        $setting->site_link = $request->site_link;

        // File storage path
        $uploadPath = public_path('admin-assets/img/');
        $relativePath = 'admin-assets/img/';

        // Save Site Logo
        if ($request->hasFile('site_logo')) {
            if ($setting->site_logo && file_exists(public_path($setting->site_logo))) {
                unlink(public_path($setting->site_logo));
            }

            $logoName = 'logo.'. time() . $request->site_logo->extension();
            $request->site_logo->move($uploadPath, $logoName);
            $setting->site_logo = $relativePath . $logoName;
        }

        // Save Favicon
        if ($request->hasFile('site_favicon')) {
            if ($setting->site_favicon && file_exists(public_path($setting->site_favicon))) {
                unlink(public_path($setting->site_favicon));
            }

            $faviconName = 'favicon.'. time() . $request->site_favicon->extension();
            $request->site_favicon->move($uploadPath, $faviconName);
            $setting->site_favicon = $relativePath . $faviconName;
        }

        // Save Signature
        if ($request->hasFile('signature')) {
            if ($setting->signature && file_exists(public_path($setting->signature))) {
                unlink(public_path($setting->signature));
            }

            $signatureName = 'signature.'. time() . $request->signature->extension();
            $request->signature->move($uploadPath, $signatureName);
            $setting->signature = $relativePath . $signatureName;
        }

        $setting->save();

        return response()->json(['message' => 'Site settings updated successfully!']);
    }


    public function homeupdate(Request $request)
    {
        $setting = Home::first(); // Assuming only 1 record

        $validator = Validator::make($request->all(), [
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'whatsapp'  => 'nullable|string|max:255',
            'facebook'  => 'nullable|string|max:255',
            'telegram'  => 'nullable|string|max:255',
            'insta'     => 'nullable|string|max:255',
            'name'      => 'nullable|string|max:255',
            'desc'      => 'nullable|string|max:255',
            'image'     => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $setting->email    = $request->email;
        $setting->phone    = $request->phone;
        $setting->address  = $request->address;
        $setting->whatsapp = $request->whatsapp;
        $setting->facebook = $request->facebook;
        $setting->telegram = $request->telegram;
        $setting->insta    = $request->insta;
        $setting->name     = $request->name;
        $setting->desc     = $request->desc;

        // File storage path
        $uploadPath = public_path('admin-assets/img/');
        $relativePath = 'admin-assets/img/';

        // Save Image
        if ($request->hasFile('image')) {
            if ($setting->image && file_exists(public_path($setting->image))) {
                unlink(public_path($setting->image));
            }

            $imageName = 'home_image_' . time() . '.' . $request->image->extension();
            $request->image->move($uploadPath, $imageName);
            $setting->image = $relativePath . $imageName;
        }

        if ($request->hasFile('image2')) {
            if ($setting->image2 && file_exists(public_path($setting->image2))) {
                unlink(public_path($setting->image2));
            }

            $imageName = 'home_image_2' . time() . '.' . $request->image2->extension();
            $request->image2->move($uploadPath, $imageName);
            $setting->image2 = $relativePath . $imageName;
        }

        if ($request->hasFile('image3')) {
            if ($setting->image3 && file_exists(public_path($setting->image3))) {
                unlink(public_path($setting->image3));
            }

            $imageName = 'home_image_3' . time() . '.' . $request->image3->extension();
            $request->image3->move($uploadPath, $imageName);
            $setting->image3 = $relativePath . $imageName;
        }

        if ($request->hasFile('image4')) {
            if ($setting->image4 && file_exists(public_path($setting->image4))) {
                unlink(public_path($setting->image4));
            }

            $imageName = 'home_image_4' . time() . '.' . $request->image4->extension();
            $request->image4->move($uploadPath, $imageName);
            $setting->image4 = $relativePath . $imageName;
        }

        if ($request->hasFile('image5')) {
            if ($setting->image5 && file_exists(public_path($setting->image5))) {
                unlink(public_path($setting->image5));
            }

            $imageName = 'home_image_5' . time() . '.' . $request->image5->extension();
            $request->image5->move($uploadPath, $imageName);
            $setting->image5 = $relativePath . $imageName;
        }

        $setting->save();

        return response()->json(['message' => 'Landing Page Details updated successfully!']);
    }

}
