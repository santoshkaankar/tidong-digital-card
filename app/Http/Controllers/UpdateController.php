<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    // Upload form dikhane ke liye
    public function showUploadForm()
    {
        return view('admin.update_website');
    }

    // Zip file upload aur extract karne ke liye
    public function uploadUpdate(Request $request)
    {
        // Check karein ki user ne zip file hi daali hai
        $request->validate([
            'update_zip' => 'required|mimes:zip|max:50000', // Max 50MB
        ]);

        try {
            $file = $request->file('update_zip');
            $fileName = 'update_' . time() . '.zip';
            
            // Zip ko temporarily store karein
            $file->storeAs('updates', $fileName);
            $zipPath = storage_path('app/updates/' . $fileName);

            $zip = new ZipArchive;
            $res = $zip->open($zipPath);
            
            if ($res === TRUE) {
                // Code ko project ke main folder (root) me extract karein
                $zip->extractTo(base_path());
                $zip->close();
                
                // Extraction ke baad zip file ko delete kar dein taaki space bache
                unlink($zipPath);
                
                return back()->with('success', 'Website successfully update ho gayi hai! Naya code live ho gaya hai.');
            } else {
                return back()->with('error', 'Zip file open karne me problem aayi. Kripya dobara koshish karein.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'System Error: ' . $e->getMessage());
        }
    }
}