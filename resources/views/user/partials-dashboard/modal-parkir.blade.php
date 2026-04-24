<!-- MODAL MULAI PARKIR -->
<div class="modal fade" id="modalParkir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form id="formParkir" action="{{ route('parkir.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Mulai Parkir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- PILIH KENDARAAN TERDAFTAR -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih Kendaraan</label>
                        <select name="kendaraan_id" id="selectKendaraan" class="form-select border-0 bg-light">
                            <option value="">-- Pilih Kendaraan Anda --</option>
                            @foreach($kendaraanTersedia as $k)
                                <option value="{{ $k->id }}" data-jenis="{{ $k->jenis }}">
                                    {{ strtoupper($k->plat_nomor) }} • {{ $k->merk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- DISPLAY TARIF -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tarif per jam</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light text-muted">Rp</span>
                            <input type="text" id="tarifDisplay" class="form-control border-0 bg-light fw-bold" placeholder="Pilih kendaraan dahulu" readonly>
                        </div>
                        <input type="hidden" name="tarif" id="tarifValue">
                    </div>

                    <div class="text-center my-3">
                        <hr class="opacity-25">
                        <span class="badge bg-light text-muted position-relative" style="top: -25px;">atau</span>
                    </div>

                    <!-- TAMBAH KENDARAAN BARU -->
                    <button class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#kendaraanBaru" id="btnCollapseKendaraan">
                        <i class="bi bi-plus-lg me-1"></i> Gunakan Kendaraan Lain
                    </button>

                    <div class="collapse" id="kendaraanBaru">
                        <div class="card card-body border-0 bg-light mt-2" style="border-radius: 15px;">
                            <div class="mb-2">
                                <label class="small fw-medium mb-1">Jenis</label>
                                <select name="jenis" id="jenisBaru" class="form-select border-0">
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="motor">Motor</option>
                                    <option value="mobil">Mobil</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-medium mb-1">Plat Nomor</label>
                                <input type="text" name="plat_nomor" id="platBaru" class="form-control border-0" placeholder="Contoh: B 1234 ABC">
                            </div>
                            <div class="mb-2">
                                <label class="small fw-medium mb-1">Merk & Warna</label>
                                <div class="row g-2">
                                    <div class="col-7">
                                        <input type="text" name="merk" id="merkBaru" class="form-control border-0" placeholder="Merk (Vario)">
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="warna" id="warnaBaru" class="form-control border-0" placeholder="Warna">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnScan" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        Lanjut Scan <i class="bi bi-qr-code-scan ms-1"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>