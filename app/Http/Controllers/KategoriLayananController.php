<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @deprecated Sesuai Robustness Diagram (UML Analysis), kategori layanan terintegrasi dalam App\Http\Controllers\KatalogController.
 * Berkas ini dipertahankan sebagai backward-compatibility bridge.
 */
class KategoriLayananController extends KatalogController
{
    public function index(Request $request)
    {
        return $this->kategoriIndex($request);
    }

    public function store(Request $request)
    {
        return $this->kategoriStore($request);
    }

    public function update(Request $request, $id)
    {
        return $this->kategoriUpdate($request, $id);
    }

    public function destroy($id)
    {
        return $this->kategoriDestroy($id);
    }

    public function toggleStatus($id)
    {
        return $this->kategoriToggleStatus($id);
    }
}
