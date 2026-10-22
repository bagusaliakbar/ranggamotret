<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @deprecated Sesuai Robustness Diagram (UML Analysis), kuis terintegrasi dalam App\Http\Controllers\MateriController.
 * Berkas ini dipertahankan sebagai backward-compatibility bridge.
 */
class QuizController extends MateriController
{
    public function show($quizId)
    {
        return $this->showQuiz($quizId);
    }

    public function submit(Request $request, $quizId)
    {
        return $this->submitQuiz($request, $quizId);
    }
}
