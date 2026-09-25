<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
   ## Show Data
   public function index()
   {
       $title = "Pengaturan";
       $setting = Setting::find(1);
       return view('admin.setting.index',compact('title','setting'));
   }

   public function validate(Request $request)
   {
       if ($request->ajax()) {

           $request->validate([
               'application_name' => 'required',
               'short_application_name' => 'required',
               'small_icon' => 'image|mimes:jpg,png,jpeg|max:2000',
               'large_icon' => 'image|mimes:jpg,png,jpeg|max:2000',
               'background_login' => 'image|mimes:jpg,png,jpeg|max:2000'
           ]);
   
           return response()->json(['success' => true]);
       }
   }

   ## Edit Data
   public function update(Request $request, Setting $setting)
   {
       if ($request->ajax()) {
           $setting->address = $request->address;
           $setting->phone = $request->phone;
           $setting->email = $request->email;
           $setting->application_name = $request->application_name;
           $setting->short_application_name = $request->short_application_name;

           if ($setting->small_icon && $request->file('small_icon') != "") {
               Storage::delete('upload/setting/'.$setting->small_icon);
           }
   
           if($request->file('small_icon')){	
               $setting->small_icon = '1'.time().'.'.$request->small_icon->getClientOriginalExtension();
               Storage::putFileAs('upload/setting',$request->file('small_icon'), $setting->small_icon);
           }
                   
           if ($setting->large_icon && $request->file('large_icon') != "") {
               Storage::delete('upload/setting/'.$setting->large_icon);
           }

           if($request->file('large_icon')){	
               $setting->large_icon = '2'.time().'.'.$request->large_icon->getClientOriginalExtension();
               Storage::putFileAs('upload/setting',$request->file('large_icon'), $setting->large_icon);
           }
                 
           if ($setting->background_login && $request->file('background_login') != "") {
               Storage::delete('upload/setting/'.$setting->background_login);
           }

           if($request->file('background_login')){	
               $setting->background_login = '3'.time().'.'.$request->background_login->getClientOriginalExtension();
               Storage::putFileAs('upload/setting',$request->file('background_login'), $setting->background_login);
           }

           $setting->save();
   
           activity()->log('Edit Data Setting With ID = '.$setting->id);
           return response()->json(['success' => true,'message' => 'Ubah Data Berhasil']);
       }
   }

}

