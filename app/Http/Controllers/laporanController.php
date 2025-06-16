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
            'Foto.image' => 'File yang diunggah harus berupa foto/gambar.',
            'Foto.mimes' => 'Format foto tidak sesuai. Gunakan format: JPG, PNG, JPEG, GIF, atau SVG.',
            'Foto.max' => 'Ukuran foto terlalu besar. Maksimal 2MB.',
            'departemen_supervisor_id.required' => 'Mohon pilih departemen untuk menentukan penanggung jawab masalah.',
            'departemen_supervisor_id.exists' => 'Departemen yang dipilih tidak valid. Silakan pilih dari daftar yang tersedia.',
            'kategori_masalah.required' => 'Mohon pilih kategori masalah untuk klasifikasi yang tepat.',
            'deskripsi_masalah.required' => 'Mohon berikan deskripsi masalah agar dapat dipahami dengan jelas.',
            'tenggat_waktu.required' => 'Mohon tentukan tenggat waktu penyelesaian masalah.',
            'tenggat_waktu.date' => 'Format tanggal tenggat waktu tidak valid. Gunakan format yang sesuai.'
        ];

        $request->validate([
            'Foto' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'departemen_supervisor_id' => 'required|exists:departemen_supervisors,id',
            'kategori_masalah' => 'required|string',
            'deskripsi_masalah' => 'required|string',
            'tenggat_waktu' => 'required|date',
        ], $messages);

        // Proses upload foto (jika ada)
        $fileName = null;
        if ($request->hasFile('Foto')) {
            $fileName = 'Foto-' . uniqid() . '.' . $request->Foto->extension();
            $request->Foto->move(public_path('images'), $fileName);
        }

        // Simpan data ke database
        $laporan = laporan::create([
            'Foto' => $fileName,
            'departemen_supervisor_id' => $request->departemen_supervisor_id,
            'kategori_masalah' => $request->kategori_masalah,
            'deskripsi_masalah' => $request->deskripsi_masalah,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => 'Ditugaskan', // Set status default
        ]);

        // Kirim email ke supervisor
        $supervisor = $laporan->departemenSupervisor;
        if ($supervisor && $supervisor->email) {
            Mail::to($supervisor->email)->send(new LaporanDitugaskanSupervisor($laporan));
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
            'Foto.image' => 'File yang diunggah harus berupa foto/gambar.',
            'Foto.mimes' => 'Format foto tidak sesuai. Gunakan format: JPG, PNG, JPEG, GIF, atau SVG.',
            'Foto.max' => 'Ukuran foto terlalu besar. Maksimal 2MB.',
            'departemen_supervisor_id.required' => 'Mohon pilih departemen untuk menentukan penanggung jawab masalah.',
            'departemen_supervisor_id.exists' => 'Departemen yang dipilih tidak valid. Silakan pilih dari daftar yang tersedia.',
            'kategori_masalah.required' => 'Mohon pilih kategori masalah untuk klasifikasi yang tepat.',
            'deskripsi_masalah.required' => 'Mohon berikan deskripsi masalah agar dapat dipahami dengan jelas.',
            'tenggat_waktu.required' => 'Mohon tentukan tenggat waktu penyelesaian masalah.',
            'tenggat_waktu.date' => 'Format tanggal tenggat waktu tidak valid. Gunakan format yang sesuai.',
            'status.required' => 'Mohon pilih status terbaru dari laporan ini.'
        ];

        $request->validate([
            'Foto' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'departemen_supervisor_id' => 'required|exists:departemen_supervisors,id',
            'kategori_masalah' => 'required|string',
            'deskripsi_masalah' => 'required|string',
            'tenggat_waktu' => 'required|date',
            'status' => 'required|string',
        ], $messages);

        // Cari laporan berdasarkan ID
        $laporan = laporan::findOrFail($id);

        // Proses upload foto (jika ada)
        if ($request->hasFile('Foto')) {
            // Hapus foto lama jika ada
            if ($laporan->Foto && file_exists(public_path('images/' . $laporan->Foto))) {
                unlink(public_path('images/' . $laporan->Foto));
            }

            // Simpan foto baru
            $fileName = 'Foto-' . uniqid() . '.' . $request->Foto->extension();
            $request->Foto->move(public_path('images'), $fileName);
        } else {
            $fileName = $laporan->Foto; // Gunakan foto lama jika tidak ada foto baru
        }

        // Perbarui data di database
        $laporan->update([
            'Foto' => $fileName,
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
            if ($laporan->Foto && file_exists(public_path('images/' . $laporan->Foto))) {
                unlink(public_path('images/' . $laporan->Foto));
            }
            
            // Cek dan hapus penyelesaian terkait jika ada
            if ($laporan->penyelesaian) {
                if ($laporan->penyelesaian->Foto && file_exists(public_path('images/' . $laporan->penyelesaian->Foto))) {
                    unlink(public_path('images/' . $laporan->penyelesaian->Foto));
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
            'status.required' => 'Silakan pilih status tindakan yang akan dilakukan',
            'status.in' => 'Status yang dipilih tidak valid. Silakan pilih dari opsi yang tersedia'
        ];

        // Jika status Selesai, tambahkan validasi tambahan
        if ($request->status === 'Selesai') {
            $rules['Tanggal'] = 'required|date';
            $rules['deskripsi_penyelesaian'] = 'required|string|max:500';
            $rules['Foto'] = 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048';

            $messages += [
                'Tanggal.required' => 'Mohon isi tanggal penyelesaian untuk dokumentasi',
                'Tanggal.date' => 'Format tanggal tidak valid',
                'deskripsi_penyelesaian.required' => 'Mohon berikan deskripsi penyelesaian yang telah dilakukan',
                'deskripsi_penyelesaian.max' => 'Deskripsi terlalu panjang (maksimal 500 karakter)',
                'Foto.image' => 'File yang diunggah harus berupa foto/gambar',
                'Foto.mimes' => 'Format foto harus JPG, PNG, JPEG, GIF, atau SVG',
                'Foto.max' => 'Ukuran foto maksimal 2MB'
            ];
        }

        $validator = $request->validate($rules, $messages);

        // Proses data setelah validasi berhasil
        $laporan = laporan::findOrFail($id);
        
        if ($request->status !== 'Selesai') {
            $laporan->update([
                'status' => $request->status
            ]);
        } else {
            // Update laporan with just the date for penyelesaian
            $laporan->update([
                'status' => $request->status,
                'Tanggal' => $request->Tanggal // Store just the date
            ]);

            // Create penyelesaian record
            if ($request->filled('deskripsi_penyelesaian')) {
                $penyelesaianData = [
                    'laporan_id' => $laporan->id,
                    'deskripsi_penyelesaian' => $request->deskripsi_penyelesaian,
                    'Tanggal' => $request->Tanggal // Store just the date
                ];

                if ($request->hasFile('Foto')) {
                    $fileName = 'Penyelesaian-' . uniqid() . '.' . $request->Foto->extension();
                    $request->Foto->move(public_path('images'), $fileName);
                    $penyelesaianData['Foto'] = $fileName;
                }

                Penyelesaian::create($penyelesaianData);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function dashboardDatatables(Request $request)
    {
        $query = laporan::with(['departemenSupervisor', 'penyelesaian'])
            ->where('status', '!=', 'Selesai');

        // Apply filters
        $query = $this->applyFilters($request, $query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                if ($row->Foto && file_exists(public_path('images/' . $row->Foto))) {
                    $imgUrl = url('images/' . $row->Foto);
                    return '<img src="' . $imgUrl . '" alt="Foto" class="img-thumbnail" style="width: 100px;" 
                            data-bs-toggle="modal" data-bs-target="#modalFotoFull" data-img-src="' . $imgUrl . '">';
                }
                return '<img src="' . url('images/nophoto.jpg') . '" alt="Foto tidak tersedia" class="img-thumbnail" style="width: 100px;">';
            })
            ->addColumn('departemen', function ($row) {
                return $row->departemenSupervisor->departemen . ': ' . $row->departemenSupervisor->supervisor;
            })
            ->addColumn('kategori_masalah', function ($row) {
                $categories = explode(':', $row->kategori_masalah, 2);
                $prefix = trim($categories[0]);
                $description = isset($categories[1]) ? trim($categories[1]) : '';
                
                $badges = '';
                switch ($prefix) {
                    case 'Safety':
                        $badges = '<span class="badge bg-danger">' . $prefix . '</span>';
                        break;
                    case 'Seiri':
                    case 'Seiton':
                    case 'Seiso':
                    case 'Seiketsu':
                    case 'Shitsuke':
                        $badges = '<span class="badge bg-primary">' . $prefix . '</span>';
                        break;
                    default:
                        $badges = '<span class="badge bg-secondary">' . $prefix . '</span>';
                }
                
                return $badges . ' ' . $description;
            })
            ->addColumn('status', function ($row) {
                $class = 'bg-secondary';
                switch ($row->status) {
                    case 'Ditugaskan':
                        $class = 'bg-warning text-dark';
                        break;
                    case 'Proses':
                        $class = 'bg-info text-dark';
                        break;
                    case 'Selesai':
                        $class = 'bg-success';
                        break;
                }
                return '<span class="badge ' . $class . '">' . $row->status . '</span>';
            })
            ->addColumn('penyelesaian', function ($row) {
                return '<a href="' . route('laporan.tindakan', $row->id) . '" class="btn btn-primary btn-sm">Tindakan</a>';
            })
            ->addColumn('aksi', function ($row) {
                $editBtn = '<a href="' . route('index.edit', $row->id) . '" class="btn btn-secondary btn-sm me-1"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-danger btn-sm delete-button" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fas fa-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->editColumn('Tanggal', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
            })
            ->rawColumns(['foto', 'kategori_masalah', 'status', 'penyelesaian', 'aksi'])
            ->make(true);
    }

    public function sejarahDatatables(Request $request)
    {
        $query = laporan::with(['departemenSupervisor', 'penyelesaian'])
            ->where('status', 'Selesai');
        
        // Apply filters
        $query = $this->applyFilters($request, $query);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('foto', function ($row) {
                if ($row->Foto && file_exists(public_path('images/' . $row->Foto))) {
                    $imgUrl = url('images/' . $row->Foto);
                    return '<img src="' . $imgUrl . '" alt="Foto" class="img-thumbnail" style="width: 100px;" 
                            data-bs-toggle="modal" data-bs-target="#modalFotoFull" data-img-src="' . $imgUrl . '">';
                }
                return '<img src="' . url('images/nophoto.jpg') . '" alt="Foto tidak tersedia" class="img-thumbnail" style="width: 100px;">';
            })
            ->editColumn('Tanggal', function ($row) {
                return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
            })
            ->addColumn('departemen', function ($row) {
                return $row->departemenSupervisor->departemen . ': ' . $row->departemenSupervisor->supervisor;
            })
            ->addColumn('kategori_masalah', function ($row) {
                $categories = explode(':', $row->kategori_masalah, 2);
                $prefix = trim($categories[0]);
                $description = isset($categories[1]) ? trim($categories[1]) : '';
                
                $badges = '';
                switch ($prefix) {
                    case 'Safety':
                        $badges = '<span class="badge bg-danger">' . $prefix . '</span>';
                        break;
                    case 'Seiri':
                    case 'Seiton':
                    case 'Seiso':
                    case 'Seiketsu':
                    case 'Shitsuke':
                        $badges = '<span class="badge bg-primary">' . $prefix . '</span>';
                        break;
                    default:
                        $badges = '<span class="badge bg-secondary">' . $prefix . '</span>';
                }
                
                return $badges . ' ' . $description;
            })
            ->addColumn('status', function ($row) {
                return '<span class="badge bg-success">Selesai</span>';
            })
            ->addColumn('penyelesaian', function ($row) {
                if ($row->penyelesaian) {
                    return '<button type="button" class="btn btn-info btn-sm lihat-penyelesaian-btn" data-id="' . $row->id . '" data-bs-toggle="modal" data-bs-target="#modalPenyelesaian">
                                <i class="fas fa-eye me-1"></i>Lihat
                            </button>';
                }
                return '<span class="text-muted">Tidak ada data</span>';
            })
            ->addColumn('aksi', function ($row) {
                $editBtn = '<a href="' . route('index.edit', $row->id) . '" class="btn btn-secondary btn-sm me-1"><i class="fas fa-edit"></i></a>';
                $deleteBtn = '<button type="button" class="btn btn-danger btn-sm delete-button" data-id="' . $row->id . '" data-ref="sejarah" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="fas fa-trash"></i></button>';
                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['foto', 'kategori_masalah', 'status', 'penyelesaian', 'aksi'])
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
            return response()->json(['success' => false]);
        }
        $penyelesaian = $laporan->penyelesaian;
        return response()->json([
            'success' => true,
            'Tanggal' => $penyelesaian->Tanggal ? \Carbon\Carbon::parse($penyelesaian->Tanggal)->format('d-m-Y') : null,
            'Foto' => $penyelesaian->Foto ? asset('images/' . $penyelesaian->Foto) : null,
            'deskripsi_penyelesaian' => $penyelesaian->deskripsi_penyelesaian,
        ]);
    }

    // Tambahkan method baru
    public function downloadSejarah()
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