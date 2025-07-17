<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\laporan;
use App\Models\Penyelesaian;
use App\Models\DepartemenSupervisor;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Mail\LaporanDitugaskanSupervisor;
use Illuminate\Support\Facades\Mail;

class laporanController extends Controller
{
    public function index()
    {
        // Get all departments for filter
        $departemens = DepartemenSupervisor::all();
        
        return view('walkandtalk.sejarah', compact('departemens'));
    }

    public function create()
    {
        $departemens = DepartemenSupervisor::all();
        return view('walkandtalk.laporan', compact('departemens'));
    }

    public function store(Request $request)
    {
        $messages = [
            'Foto.*.image' => 'Semua file yang diunggah harus berupa gambar.',
            'Foto.*.mimes' => 'Format foto tidak valid. Gunakan: JPG, PNG, JPEG, GIF, SVG.',
            'Foto.*.max' => 'Ukuran setiap foto tidak boleh lebih dari 2MB.',
            'Foto.max' => 'Anda hanya dapat mengunggah maksimal 5 foto.',
            'departemen_supervisor_id.required' => 'Mohon pilih departemen.',
            'kategori_masalah.required' => 'Mohon pilih kategori masalah.',
            'deskripsi_masalah.required' => 'Mohon berikan deskripsi masalah.',
            'tenggat_waktu.required' => 'Mohon tentukan tenggat waktu.',
        ];

        $request->validate([
            'Foto' => 'nullable|array|max:5',
            'Foto.*' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'departemen_supervisor_id' => 'required|exists:departemen_supervisors,id',
            'kategori_masalah' => 'required|string',
            'deskripsi_masalah' => 'required|string',
            'tenggat_waktu' => 'required|date',
        ], $messages);


        $fotoFileNames = [];
        if ($request->hasFile('Foto')) {
            foreach ($request->file('Foto') as $file) {
                $fileName = 'Foto-' . uniqid() . '.' . $file->extension();
                $file->move(public_path('images'), $fileName);
                $fotoFileNames[] = $fileName;
            }
        }

        $laporan = laporan::create([
            'Foto' => $fotoFileNames, // Langsung berikan array, model akan handle encoding
            'departemen_supervisor_id' => $request->departemen_supervisor_id,
            'kategori_masalah' => $request->kategori_masalah,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => 'Ditugaskan',
        ]);

        // Kirim email ke supervisor
        $supervisor = $laporan->departemenSupervisor;
        if ($supervisor && $supervisor->email) {
            try {
                Mail::to($supervisor->email)->send(new LaporanDitugaskanSupervisor($laporan));
            } catch (\Exception $e) {
                // Log error jika pengiriman email gagal
                \Log::error("Gagal mengirim email notifikasi: " . $e->getMessage());
            }
        }

        // Redirect dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $laporan = laporan::findOrFail($id);
        $departemens = DepartemenSupervisor::all();
        return view('walkandtalk.edit', compact('laporan', 'departemens'));
    }

    public function update(Request $request, $id)
    {
        $messages = [
            'Foto.*.image' => 'Semua file yang diunggah harus berupa gambar.',
            'Foto.*.mimes' => 'Format foto tidak valid. Gunakan: JPG, PNG, JPEG, GIF, SVG.',
            'Foto.*.max' => 'Ukuran setiap foto tidak boleh lebih dari 2MB.',
            'Foto.max' => 'Jumlah total foto tidak boleh lebih dari 5.',
            'departemen_supervisor_id.required' => 'Mohon pilih departemen untuk menentukan penanggung jawab masalah.',
            'departemen_supervisor_id.exists' => 'Departemen yang dipilih tidak valid. Silakan pilih dari daftar yang tersedia.',
            'kategori_masalah.required' => 'Mohon pilih kategori masalah untuk klasifikasi yang tepat.',
            'deskripsi_masalah.required' => 'Mohon berikan deskripsi masalah agar dapat dipahami dengan jelas.',
            'tenggat_waktu.required' => 'Mohon tentukan tenggat waktu penyelesaian masalah.',
            'tenggat_waktu.date' => 'Format tanggal tenggat waktu tidak valid. Gunakan format yang sesuai.',
            'status.required' => 'Mohon pilih status terbaru dari laporan ini.'
        ];

        $request->validate([
            'Foto' => 'nullable|array',
            'Foto.*' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'departemen_supervisor_id' => 'required|exists:departemen_supervisors,id',
            'kategori_masalah' => 'required|string',
            'deskripsi_masalah' => 'required|string',
            'tenggat_waktu' => 'required|date',
            'status' => 'required|string',
        ], $messages);

        // Cari laporan berdasarkan ID
        $laporan = laporan::findOrFail($id);

        $existingPhotos = $request->input('existing_photos', []);
        $newlyUploadedPhotos = [];

        if ($request->hasFile('Foto')) {
            foreach ($request->file('Foto') as $file) {
                $fileName = 'Foto-' . uniqid() . '.' . $file->extension();
                $file->move(public_path('images'), $fileName);
                $newlyUploadedPhotos[] = $fileName;
            }
        }

        $allPhotos = array_merge($existingPhotos, $newlyUploadedPhotos);
        
        if (count($allPhotos) > 5) {
            return back()->withErrors(['Foto' => 'Jumlah total foto tidak boleh lebih dari 5.'])->withInput();
        }

        // Hapus file foto lama yang tidak ada di `existing_photos`
        $oldPhotos = json_decode($laporan->Foto, true) ?: [];
        $photosToDelete = array_diff($oldPhotos, $existingPhotos);
        foreach ($photosToDelete as $photo) {
            if (file_exists(public_path('images/' . $photo))) {
                unlink(public_path('images/' . $photo));
            }
        }

        // Perbarui data di database
        $laporan->update([
            'Foto' => json_encode($allPhotos),
            'departemen_supervisor_id' => $request->departemen_supervisor_id,
            'kategori_masalah' => $request->kategori_masalah,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => $request->status, // Perbarui status
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Cari laporan berdasarkan ID
            $laporan = laporan::findOrFail($id);
            
            // Hapus file foto laporan jika ada
            $fotoFiles = json_decode($laporan->Foto, true);
            if (is_array($fotoFiles)) {
                foreach ($fotoFiles as $file) {
                    if ($file && file_exists(public_path('images/' . $file))) {
                        unlink(public_path('images/' . $file));
                    }
                }
            }
            
            // Cek dan hapus penyelesaian terkait jika ada
            if ($laporan->penyelesaian) {
                // Hapus foto penyelesaian
                $fotoPenyelesaian = json_decode($laporan->penyelesaian->Foto, true);
                 if (is_array($fotoPenyelesaian)) {
                    foreach ($fotoPenyelesaian as $file) {
                        if ($file && file_exists(public_path('images/' . $file))) {
                            unlink(public_path('images/' . $file));
                        }
                    }
                }
                $laporan->penyelesaian->delete(); // Soft delete penyelesaian
            }
            
            // Hapus laporan
            $laporan->delete(); // Soft delete laporan

            // Return JSON response untuk AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan berhasil dihapus'
                ]);
            }

            // Redirect dengan pesan sukses untuk non-AJAX request
            $message = 'Laporan berhasil dihapus';
            if ($request->has('ref') && $request->ref === 'sejarah') {
                return redirect()->route('sejarah')->with('success', $message);
            }
            
            return redirect()->route('dashboard')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error deleting laporan: ' . $e->getMessage());
            
            // Return JSON response untuk AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus laporan'
                ], 500);
            }

            // Redirect dengan pesan error untuk non-AJAX request
            $message = 'Terjadi kesalahan saat menghapus laporan. Silakan coba lagi.';
            if ($request->has('ref') && $request->ref === 'sejarah') {
                return redirect()->route('sejarah')->with('error', $message);
            }
            
            return redirect()->route('dashboard')->with('error', $message);
        }
    }

    public function dashboard()
    {
        // Hitung total laporan
        $totalLaporan = laporan::count();
        $laporanDitugaskan = laporan::where('status', 'Ditugaskan')->count();
        $laporanDiproses = laporan::where('status', 'Proses')->count();
        $laporanSelesai = laporan::where('status', 'Selesai')->count();

        // Get all departments for filter
        $departemens = DepartemenSupervisor::all();

        return view('walkandtalk.dashboard', compact(
            'totalLaporan',
            'laporanDitugaskan',
            'laporanDiproses',
            'laporanSelesai',
            'departemens'
        ));
    }

    public function tindakan($id)
    {
        $laporan = laporan::findOrFail($id);
        return view('walkandtalk.tindakan', compact('laporan'));
    }

    public function storeTindakan(Request $request, $id)
    {
        $rules = [
            'status' => 'required|in:Ditugaskan,Proses,Selesai',
        ];

        $messages = [
            'status.required' => 'Silakan pilih status tindakan.',
            'status.in' => 'Status yang dipilih tidak valid.'
        ];

        if ($request->status === 'Selesai') {
            $rules['Tanggal'] = 'required|date';
            $rules['deskripsi_penyelesaian'] = 'required|string|max:1000';
            $rules['Foto'] = 'nullable|array|max:5';
            $rules['Foto.*'] = 'image|mimes:jpg,png,jpeg,gif,svg|max:2048';

            $messages += [
                'Tanggal.required' => 'Tanggal penyelesaian wajib diisi.',
                'deskripsi_penyelesaian.required' => 'Deskripsi penyelesaian wajib diisi.',
                'Foto.max' => 'Anda hanya dapat mengunggah maksimal 5 foto penyelesaian.',
                'Foto.*.image' => 'File penyelesaian harus berupa gambar.',
                'Foto.*.mimes' => 'Format foto penyelesaian tidak valid.',
                'Foto.*.max' => 'Ukuran setiap foto penyelesaian maksimal 2MB.',
            ];
        }

        $request->validate($rules, $messages);

        $laporan = laporan::findOrFail($id);
        
        if ($request->status === 'Selesai') {
            $fotoPenyelesaianNames = [];
            if ($request->hasFile('Foto')) {
                foreach ($request->file('Foto') as $file) {
                    $fileName = 'Penyelesaian-' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('images'), $fileName);
                    $fotoPenyelesaianNames[] = $fileName;
                }
            }

            Penyelesaian::updateOrCreate(
                ['laporan_id' => $laporan->id],
                [
                    'Tanggal' => $request->Tanggal,
                    'deskripsi_penyelesaian' => $request->deskripsi_penyelesaian,
                    'Foto' => $fotoPenyelesaianNames // PERBAIKAN: Berikan array langsung, bukan JSON string
                ]
            );
        }

        $laporan->update(['status' => $request->status]);

        return redirect()->route('dashboard')->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function dashboardDatatables(Request $request)
    {
        $query = laporan::with(['departemenSupervisor', 'penyelesaian'])
            ->where('status', '!=', 'Selesai');

        $query = $this->applyFilters($request, $query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                $fotos = $row->Foto; // Ini sudah menjadi array karena $casts di Model
                if (is_array($fotos) && !empty($fotos)) {
                    $firstFotoUrl = url('images/' . $fotos[0]);
                    // Siapkan semua URL foto untuk modal carousel
                    $allPhotosUrls = array_map(fn($foto) => url('images/' . $foto), $fotos);
                    $allPhotosJson = htmlspecialchars(json_encode($allPhotosUrls), ENT_QUOTES, 'UTF-8');

                    return '<img src="' . $firstFotoUrl . '" alt="Foto Masalah" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalFotoFull" data-photos=\'' . $allPhotosJson . '\'>';
                }
                return '<img src="' . url('images/nophoto.jpg') . '" alt="Foto tidak tersedia" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">';
            })
            ->addColumn('departemen', function ($row) {
                if ($row->departemenSupervisor) {
                    return $row->departemenSupervisor->departemen . '<br><small class="text-muted">' . $row->departemenSupervisor->supervisor . '</small>';
                }
                return '-';
            })
            ->editColumn('Tanggal', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
            })
            ->addColumn('status', function ($row) {
                $class = 'bg-secondary';
                if ($row->status == 'Ditugaskan') $class = 'bg-warning text-dark';
                if ($row->status == 'Proses') $class = 'bg-primary';
                if ($row->status == 'Selesai') $class = 'bg-success';
                return '<span class="badge ' . $class . '">' . $row->status . '</span>';
            })
            ->addColumn('penyelesaian', function ($row) {
                return '<a href="' . route('laporan.tindakan', $row->id) . '" class="btn btn-purple btn-sm">Tindakan</a>';
            })
            ->addColumn('aksi', function ($row) {
                $editBtn = '<a href="/edit' . $row->id . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-delete-url="/laporan/' . $row->id . '/delete" data-return-url="' . url()->current() . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['foto', 'departemen', 'status', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function sejarahDatatables(Request $request)
    {
        $query = laporan::with(['departemenSupervisor', 'penyelesaian'])
            ->where('status', 'Selesai');
        
        $query = $this->applyFilters($request, $query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                $fotos = $row->Foto; // Ini sudah menjadi array karena $casts di Model
                if (is_array($fotos) && !empty($fotos)) {
                    $firstFotoUrl = url('images/' . $fotos[0]);
                    // Siapkan semua URL foto untuk modal carousel
                    $allPhotosUrls = array_map(fn($foto) => url('images/' . $foto), $fotos);
                    $allPhotosJson = htmlspecialchars(json_encode($allPhotosUrls), ENT_QUOTES, 'UTF-8');

                    return '<img src="' . $firstFotoUrl . '" alt="Foto Masalah" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalFotoFull" data-photos=\'' . $allPhotosJson . '\'>';
                }
                return '<img src="' . url('images/nophoto.jpg') . '" alt="Foto tidak tersedia" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">';
            })
            ->editColumn('Tanggal', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
            })
            ->addColumn('departemen', function ($row) {
                if ($row->departemenSupervisor) {
                    return $row->departemenSupervisor->departemen . '<br><small class="text-muted">' . $row->departemenSupervisor->supervisor . '</small>';
                }
                return '-';
            })
            ->addColumn('penyelesaian', function ($row) {
                if ($row->penyelesaian) {
                    return '<button type="button" class="btn btn-success btn-sm lihat-penyelesaian-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalPenyelesaian">Lihat</button>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('aksi', function ($row) {
                $editBtn = '<a href="/edit' . $row->id . '" class="btn btn-sm btn-warning me-1" title="Edit"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '" data-delete-url="/laporan/' . $row->id . '/delete" data-return-url="' . url()->current() . '" title="Hapus"><i class="fas fa-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['foto', 'departemen', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function getSupervisor($id)
    {
        $departemen = DepartemenSupervisor::find($id);
        return response()->json([
            'supervisor' => $departemen ? $departemen->supervisor : null
        ]);
    }

    public function getPenyelesaian($id)
    {
        $laporan = \App\Models\laporan::with('penyelesaian')->find($id);
        if (!$laporan || !$laporan->penyelesaian) {
            return response()->json(['success' => false, 'message' => 'Data penyelesaian tidak ditemukan.']);
        }
        $penyelesaian = $laporan->penyelesaian;
        $fotoUrls = [];
        
        // PERBAIKAN: $penyelesaian->Foto sudah menjadi array karena casting di model
        if (is_array($penyelesaian->Foto)) {
            foreach ($penyelesaian->Foto as $file) {
                if (!empty($file)) { // Pastikan nama file tidak kosong
                    $fotoUrls[] = url('images/' . $file);
                }
            }
        }

        return response()->json([
            'success' => true,
            'Tanggal' => \Carbon\Carbon::parse($penyelesaian->Tanggal)->format('d-m-Y'),
            'deskripsi_penyelesaian' => $penyelesaian->deskripsi_penyelesaian,
            'Foto' => $fotoUrls, // Kirim sebagai array URL yang sudah benar
        ]);
    }

    // Tambahkan method baru
    public function downloadSejarah(Request $request)
    {
        try {
            // Ambil data laporan yang selesai
            $laporan = laporan::with(['departemenSupervisor', 'penyelesaian'])
                ->where('status', 'Selesai')
                ->orderBy('created_at', 'desc')
                ->get();

            // Format periode untuk judul
            $periode = Carbon::now()->format('F Y');

            // Generate PDF
            $pdf = PDF::loadView('walkandtalk.pdf.laporan-selesai', [
                'laporan' => $laporan,
                'periode' => $periode
            ]);

            // Set paper dan orientasi
            $pdf->setPaper('a4', 'landscape');

            // Download PDF dengan nama yang dinamis
            return $pdf->download('Laporan_Safety_Walk_and_Talk_'.$periode.'.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh laporan: ' . $e->getMessage());
        }
    }

    // Helper function untuk menerapkan filter pada query
    private function applyFilters(Request $request, $query)
    {
        // Filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Filter departemen
        if ($request->filled('departemen')) {
            $query->where('departemen_supervisor_id', $request->departemen);
        }
        
        // Filter kategori masalah
        if ($request->filled('kategori')) {
            $query->where('kategori_masalah', $request->kategori);
        }
        
        // Filter status (hanya pada dashboard)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter tenggat waktu berdasarkan bulan
        if ($request->filled('tenggat_bulan')) {
            $month = $request->tenggat_bulan;
            $query->whereMonth('tenggat_waktu', $month);
        }
        
        return $query;
    }
}