<?php

namespace App\Http\Controllers;

/**
 * @deprecated Sesuai Robustness Diagram (UML Analysis), layanan terintegrasi dalam App\Http\Controllers\KatalogController.
 * Berkas ini dipertahankan sebagai backward-compatibility bridge.
 */
class LayananController extends KatalogController
{
    // Mewarisi index, store, update, destroy, toggleStatus, storePaket, updatePaket, destroyPaket dari KatalogController
}
