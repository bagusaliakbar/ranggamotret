<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kursus;
use App\Models\Testimoni;

/**
 * Controller Pengelolaan Kursus & Akademi Fotografi
 * 
 * Merepresentasikan Control Object 'KursusController' pada Robustness Diagram.
 * Terhubung dengan boundary 'kursus' dan entity 'Kursus'.
 */
class KursusController extends CourseController
{
    /**
     * Menampilkan katalog publik seluruh kursus akademi
     */
    public function index(Request $request)
    {
        $level = $request->query('level');
        $query = Kursus::where('status', 'aktif')->with(['instruktur', 'moduls.materis']);

        if ($level) {
            $query->where('level', $level);
        }

        $kursus = $query->get();

        return view('public.courses', compact('kursus', 'level'));
    }

    /**
     * Menampilkan detail silabus kursus akademi
     */
    public function detail($id)
    {
        $kursus = Kursus::where('status', 'aktif')
            ->with(['instruktur', 'moduls.materis', 'moduls.tugas', 'moduls.quizs'])
            ->findOrFail($id);

        $otherCourses = Kursus::where('status', 'aktif')
            ->where('id', '!=', $kursus->id)
            ->take(3)
            ->get();

        $reviews = Testimoni::where('status', 'approved')->with('user')->take(3)->get();

        return view('public.course-detail', compact('kursus', 'otherCourses', 'reviews'));
    }
}
