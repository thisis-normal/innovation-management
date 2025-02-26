<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SangKien; // Sử dụng model chính xác

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Nhận thông tin lọc từ request
        $status = $request->input('status');
        $year = $request->input('year');
        $unit = $request->input('unit');
        $rating = $request->input('rating');

        // Truy vấn sáng kiến theo bộ lọc
        $innovations = SangKien::query()
            ->when($status, function ($query) use ($status) {
                return $query->where('ma_trang_thai_sang_kien', $status);
            })
            ->when($year, function ($query) use ($year) {
                return $query->whereYear('created_at', $year);
            })
            ->when($unit, function ($query) use ($unit) {
                return $query->where('ma_don_vi', $unit);
            })
            ->when($rating, function ($query) use ($rating) {
                return $query->where('ket_qua', $rating);
            })
            ->get();

        return view('search.index', compact('innovations'));
    }
}
