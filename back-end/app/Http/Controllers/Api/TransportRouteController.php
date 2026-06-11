<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransportRouteResource;
use App\Models\transportRoutes;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    /**
     * عرض كل الخطوط
     */
    public function index()
    {
        $routes = TransportRoutes::query()
            ->where('active', true)
            ->select(['id', 'name'])
            ->orderBy('id')
            ->get();

        // سيُعاد: { "data": [ {id, name}, ... ] }
        return TransportRouteResource::collection($routes);
    }

    /**
     * إضافة خط جديد
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:transport_routes,code',
            'active' => 'boolean',
        ]);

        $route = transportRoutes::create($data);

        return response()->json($route, 201);
    }

    /**
     * عرض خط واحد حسب ID
     */
    public function show($id)
    {
        $route = transportRoutes::find($id);

        if (! $route) {
            return response()->json(['message' => 'الخط غير موجود'], 404);
        }

        return response()->json($route, 200);
    }

    /**
     * تحديث خط
     */
    public function update(Request $request, $id)
    {
        $route = transportRoutes::find($id);

        if (! $route) {
            return response()->json(['message' => 'الخط غير موجود'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:transport_routes,code,'.$id,
            'active' => 'boolean',
        ]);

        $route->update($data);

        return response()->json($route, 200);
    }

    /**
     * حذف خط
     */
    public function destroy($id)
    {
        $route = transportRoutes::find($id);

        if (! $route) {
            return response()->json(['message' => 'الخط غير موجود'], 404);
        }

        $route->delete();

        return response()->json(['message' => 'تم حذف الخط بنجاح'], 200);
    }
}
