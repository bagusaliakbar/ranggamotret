<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @deprecated Sesuai Robustness Diagram, testimoni diintegrasikan dalam ReservasiController.
 * Berkas ini dipertahankan sebagai backward-compatibility bridge.
 */
class TestimonialController extends ReservasiController
{
    public function store(Request $request)
    {
        return $this->storeTestimonial($request);
    }

    public function adminIndex(Request $request = null)
    {
        return $this->adminTestimonialsIndex();
    }

    public function adminUpdateStatus(Request $request, $id)
    {
        return $this->adminUpdateTestimonialStatus($request, $id);
    }
}
