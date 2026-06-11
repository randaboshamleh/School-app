<?php

namespace App\Http\Controllers;

use App\Models\Advertisements;
use Illuminate\Http\Request;

class AdvertisementsController extends Controller
{
    // 📌 إرجاع كل الإعلانات (آخر 5 إعلانات فقط)
    public function index()
    {
        return Advertisements::latest()->take(5)->get();
    }

    // 📌 إرجاع إعلان واحد
    public function show($id)
    {
        $ad = Advertisements::find($id);

        if (! $ad) {
            return response()->json([
                'message' => 'الإعلان غير موجود',
            ], 404);
        }

        return $ad;
    }

    // 📌 إضافة إعلان جديد
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        // حذف أقدم إعلان إذا العدد >= 5
        if (Advertisements::count() >= 5) {
            Advertisements::orderBy('created_at', 'asc')->first()->delete();
        }

        return Advertisements::create([
            'content' => $request->content,
        ]);
    }

    // 📌 تحديث إعلان موجود
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $ad = Advertisements::find($id);

        if (! $ad) {
            return response()->json([
                'message' => 'الإعلان غير موجود',
            ], 404);
        }

        $ad->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'تم تحديث الإعلان بنجاح',
            'ad' => $ad,
        ], 200);
    }

    // 📌 حذف إعلان محدد
    public function destroy($id)
    {
        $ad = Advertisements::find($id);

        if (! $ad) {
            return response()->json([
                'message' => 'الإعلان غير موجود',
            ], 404);
        }

        $ad->delete();

        return response()->json([
            'message' => 'تم حذف الإعلان بنجاح',
        ], 200);
    }
}
