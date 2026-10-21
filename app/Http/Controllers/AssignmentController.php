<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Models\Notifikasi;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    // Student Submit Assignment
    public function submit(Request $request, $tugasId)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string',
            'file_submission' => 'nullable|file|mimes:jpg,jpeg,png,zip,pdf,rar|max:20480',
            'link_submission' => 'nullable|string|max:500',
        ]);

        $tugas = Tugas::with('modul')->findOrFail($tugasId);
        $filePath = null;

        if ($request->hasFile('file_submission')) {
            $file = $request->file('file_submission');
            $filename = time() . '_' . Auth::id() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/tugas'), $filename);
            $filePath = 'uploads/tugas/' . $filename;
        } elseif (!empty($validated['link_submission'])) {
            $filePath = $validated['link_submission'];
        }

        if (!$filePath && empty($validated['catatan'])) {
            return back()->with('error', 'Silakan unggah berkas tugas atau sertakan tautan / catatan.');
        }

        $submission = TugasSubmission::updateOrCreate(
            ['tugas_id' => $tugasId, 'user_id' => Auth::id()],
            [
                'file_path' => $filePath ?? 'Submitted via text notes',
                'catatan' => $validated['catatan'] ?? null,
                'tanggal_submit' => Carbon::now(),
                'status' => 'submitted',
            ]
        );

        return redirect()->route('student.learn', [$tugas->modul->kursus_id, 'tugas_id' => $tugasId])
            ->with('success', 'Tugas praktik Anda berhasil dikirim!');
    }

    // Instructor List Submissions
    public function instructorIndex()
    {
        $submissions = TugasSubmission::with(['tugas.modul.kursus', 'user'])
            ->latest()
            ->paginate(20);

        return view('instructor.assignments.index', compact('submissions'));
    }

    // Instructor Grade Submission
    public function grade(Request $request, $submissionId)
    {
        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
            'status' => 'required|in:graded,revision_requested',
        ]);

        $submission = TugasSubmission::findOrFail($submissionId);
        $submission->nilai = $validated['nilai'];
        $submission->feedback = $validated['feedback'];
        $submission->status = $validated['status'];
        $submission->save();

        Notifikasi::create([
            'user_id' => $submission->user_id,
            'judul' => 'Nilai Tugas Diberikan',
            'pesan' => 'Tugas "' . $submission->tugas->judul . '" telah dinilai: ' . $submission->nilai . '/100.',
            'link' => '/student/courses/' . $submission->tugas->modul->kursus_id . '/learn',
        ]);

        return back()->with('success', 'Nilai dan feedback berhasil disimpan.');
    }
}
