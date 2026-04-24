<div class="modal fade" id="modalTransaksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header bg-light rounded-top-4 border-bottom-0 py-3">
                <h5 class="modal-title d-flex align-items-center fw-bold text-dark">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i> Detail Kendaraan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <span class="text-muted small text-uppercase fw-bold d-block mb-1">Nomor Plat</span>
                    <h2 class="fw-black text-primary mb-0" id="m_plat" style="letter-spacing: 2px;">-</h2>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="text-muted small d-block">Jenis Kendaraan</label>
                        <span id="m_jenis" class="fw-bold text-dark text-capitalize">-</span>
                    </div>
                    <div class="col-6 text-end">
                        <label class="text-muted small d-block">Tarif / Jam</label>
                        <span id="m_tarif" class="fw-bold text-dark">-</span>
                    </div>
                    
                    <div class="col-12 border-top pt-2"></div>

                    <div class="col-6">
                        <label class="text-muted small d-block">Waktu Masuk</label>
                        <span id="m_masuk" class="fw-bold text-dark">-</span>
                    </div>
                    <div class="col-6 text-end">
                        <label class="text-muted small d-block">Durasi Parkir</label>
                        <span id="m_durasi" class="fw-bold text-dark">-</span>
                    </div>
                </div>

                <div class="bg-primary-subtle rounded-3 p-3 mt-4 text-center border border-primary-subtle">
                    <span class="text-primary-emphasis small fw-bold text-uppercase">Total Pembayaran</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1" id="m_total">Rp 0</h3>
                </div>
            </div>

            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-light fw-bold text-muted flex-fill py-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary fw-bold flex-fill py-2 shadow-sm" onclick="keluarkanKendaraan()">
                    <i class="bi bi-box-arrow-right me-1"></i> Proses Keluar
                </button>
            </div>

        </div>
    </div>
</div>