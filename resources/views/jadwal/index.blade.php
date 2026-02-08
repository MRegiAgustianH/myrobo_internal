@extends('layouts.app')

@section('header')
Kalender Jadwal Pelatihan
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
    {{ session('success') }}
</div>
@endif

{{-- ADMIN BUTTON --}}
@if(auth()->user()->role === 'admin')
<div class="flex justify-end mb-4">
    <button onclick="openCreateModal()"
        class="inline-flex items-center gap-2 bg-[#8FBFC2] hover:bg-[#6FA9AD] text-white px-4 py-2 rounded-lg text-sm transition">
        <i data-feather="plus" class="w-4 h-4"></i>
        Tambah Jadwal
    </button>
</div>
@endif

{{-- CALENDAR CONTAINER --}}
<div class="bg-white rounded-xl shadow p-3 sm:p-4 overflow-hidden">
    <div id="calendar" class="w-full"></div>
</div>

@endsection


@push('scripts')



<script>
    const jadwalMap = @json($jadwalMap);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') return;

    const isMobile = window.innerWidth < 640;

    const events = [
        @foreach($jadwals as $j)
        {
            id: '{{ $j->id }}',
            title: '{{ addslashes($j->sekolah->nama_sekolah ?? ($j->homePrivate->nama_kegiatan ?? $j->nama_kegiatan)) }}',
            start: '{{ $j->tanggal_mulai }}T{{ $j->jam_mulai }}',
            end: (() => {
                const d = new Date('{{ $j->tanggal_selesai }}T{{ $j->jam_selesai }}');
                d.setMinutes(d.getMinutes() + 1);
                return d.toISOString();
            })(),

        },
        @endforeach
    ];

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'listWeek' : 'dayGridMonth',
        height: 'auto',

        locale: 'id',

        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: isMobile ? '' : 'dayGridMonth,timeGridWeek'
        },

        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            list: 'Daftar'
        },

        views: {
            timeGridWeek: {
                slotMinTime: '06:00:00',
                slotMaxTime: '20:00:00'
            }
        },

        eventDisplay: 'block',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        events,

        eventClick: function(info) {
            @if(auth()->user()->role === 'admin')
            openDetailModal(info.event.id);
            @endif
        },

        windowResize: function(view) {
            calendar.changeView(
                window.innerWidth < 640 ? 'listWeek' : 'dayGridMonth'
            );
        }
    });

    calendar.render();
});
</script>

@endpush


<script>
    function openCreateModal() {
        Swal.fire({
            title: 'Tambah Jadwal',
            html: jadwalForm(),
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            width: '100%',
            padding: '1rem',
            customClass: {
                popup: 'max-w-3xl rounded-xl'
            },
            heightAuto: false,
            scrollbarPadding: false,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),

            preConfirm: () => {
                const mode = document.getElementById('mode_penjadwalan').value;

                const action = mode === 'single'
                    ? "{{ route('jadwal.store') }}"
                    : "{{ route('jadwal.recurring') }}";

                return handleSubmit(action, 'POST');

            }
        });
    }

    function openEditModal(id) {

        if (!jadwalMap[id]) {
            Swal.fire('Error', 'Data jadwal tidak ditemukan', 'error');
            return;
        }

        const jadwal = jadwalMap[id];

        Swal.fire({
            title: 'Edit Jadwal',
            html: jadwalForm(jadwal),
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Batal',
            width: '100%',
            padding: '1rem',
            customClass: {
                popup: 'max-w-3xl rounded-xl'
            },
            heightAuto: false,
            scrollbarPadding: false,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),

            preConfirm: () => {
                return handleSubmit(`/jadwal/${jadwal.id}`, 'PUT');
            }
        });
    }

    function actionButtons(action, method) {
        return `
            <div class="w-full flex justify-start gap-2">
                <button onclick="handleSubmit('${action}','${method}')" class="btn-primary">
                    ${method === 'POST' ? 'Simpan' : 'Update'}
                </button>
                <button onclick="Swal.close()" class="btn-secondary">Batal</button>
            </div>
        `;
    }
</script>

<script>
    function openDetailModal(id) {

        if (!jadwalMap[id]) {
            Swal.fire('Error', 'Data jadwal tidak ditemukan', 'error');
            return;
        }

        const j = jadwalMap[id];

        const jenisLabel = j.jenis_jadwal === 'sekolah' ? 'Sekolah' : 'Home Private';
        const namaLokasi = j.jenis_jadwal === 'sekolah' ? j.sekolah_nama : j.home_private_nama;

        Swal.fire({
            title: 'Detail Jadwal',
            html: `
            <div class="text-left text-sm space-y-3">

                <div>
                    <p class="font-semibold">${jenisLabel}</p>
                    <p>${namaLokasi ?? '-'}</p>
                </div>

                <div>
                    <p class="font-semibold">Kegiatan</p>
                    <p>${j.nama_kegiatan}</p>
                </div>

                <div>
                    <p class="font-semibold">Tanggal & Waktu</p>
                    <p>
                        ${j.tanggal_mulai} ${j.jam_mulai}
                        –
                        ${j.tanggal_selesai} ${j.jam_selesai}
                    </p>
                </div>

                <div>
                    <p class="font-semibold">Instruktur</p>
                    <p>
                    ${
                        Array.isArray(j.instruktur_nama) && j.instruktur_nama.length > 0
                        ? j.instruktur_nama.join(', ')
                        : '-'
                    }
                    </p>
                </div>

                <div>
                    <p class="font-semibold">Materi</p>
                    <p>
                    ${
                        Array.isArray(j.materi_nama) && j.materi_nama.length > 0
                        ? j.materi_nama.join(', ')
                        : '-'
                    }
                    </p>

                </div>

                <hr>

                <div class="flex justify-end gap-2 pt-2">
                    <button onclick="openEditModal(${j.id})"
                        class="px-3 py-1 bg-yellow-500 text-white rounded text-sm">
                        Edit
                    </button>

                    <button onclick="deleteJadwal(${j.id})"
                        class="px-3 py-1 bg-red-600 text-white rounded text-sm">
                        Hapus
                    </button>

                    <a href="/absensi/jadwal/${j.id}"
                        class="px-3 py-1 bg-blue-600 text-white rounded text-sm">
                        Lihat Absensi
                    </a>
                </div>

            </div>
            `,
            showConfirmButton: false,
            width: 600
        });
    }
</script>

<script>
    function deleteJadwal(id) {
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: 'Data tidak dapat dikembalikan',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/jadwal/${id}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

<script>

    function jadwalForm(data = {}) {

        return `
        <input type="hidden" id="csrf" value="{{ csrf_token() }}">

        <div class="space-y-5 text-sm">

            <!-- MODE PENJADWALAN (FIELD BARU) -->
            <div class="mb-4">
                <label class="block mb-1 font-medium">Mode Penjadwalan</label>
                <select id="mode_penjadwalan"
                    onchange="toggleRecurring(this.value)"
                    class="w-full rounded-lg border px-3 py-2">
                    <option value="single">Satu Kali</option>
                    <option value="bulanan">Bulanan</option>
                    <option value="semester">Semester</option>
                </select>
            </div>


            <!-- JENIS JADWAL -->
            <div>
                <label class="block mb-1 font-medium">Jenis Jadwal</label>
                <select id="jenis_jadwal"
                    onchange="toggleJenis(this.value)"
                    class="w-full rounded-lg border px-3 py-2 focus:ring focus:ring-blue-200">
                    <option value="sekolah" ${data.jenis_jadwal === 'sekolah' ? 'selected' : ''}>Sekolah</option>
                    <option value="home_private" ${data.jenis_jadwal === 'home_private' ? 'selected' : ''}>Home Private</option>
                </select>
            </div>

            <!-- SEKOLAH / HOME PRIVATE -->
            <div id="sekolahField">
                <label class="block mb-1 font-medium">Sekolah</label>
                <select id="sekolah_id"
                    class="w-full rounded-lg border px-3 py-2">
                    @foreach($sekolahs as $s)
                        <option value="{{ $s->id }}" ${data.sekolah_id == {{ $s->id }} ? 'selected' : ''}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="homePrivateField" style="display:none">
                <label class="block mb-1 font-medium">Home Private</label>
                <select id="home_private_id"
                    class="w-full rounded-lg border px-3 py-2">
                    @foreach($homePrivates as $hp)
                        <option value="{{ $hp->id }}" ${data.home_private_id == {{ $hp->id }} ? 'selected' : ''}>
                            {{ $hp->nama_kegiatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- NAMA -->
            <div>
                <label class="block mb-1 font-medium">Nama Kegiatan</label>
                <input id="nama_kegiatan"
                    class="w-full rounded-lg border px-3 py-2"
                    placeholder="Contoh: Pelatihan Robotik"
                    value="${data.nama_kegiatan ?? ''}">
            </div>

            <!-- TANGGAL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai"
                        class="w-full rounded-lg border px-3 py-2"
                        value="${data.tanggal_mulai ?? ''}">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai"
                        class="w-full rounded-lg border px-3 py-2"
                        value="${data.tanggal_selesai ?? ''}">
                </div>
            </div>

            <!-- HARI (KHUSUS RECURRING) -->
            <div id="hariField" class="mb-4" style="display:none">
                <label class="block mb-1 font-medium">Hari (untuk jadwal berulang)</label>
                <select id="hari"
                    class="w-full rounded-lg border px-3 py-2">
                    <option value="senin">Senin</option>
                    <option value="selasa">Selasa</option>
                    <option value="rabu">Rabu</option>
                    <option value="kamis">Kamis</option>
                    <option value="jumat">Jumat</option>
                    <option value="sabtu">Sabtu</option>
                    <option value="minggu">Minggu</option>
                </select>
            </div>

            <!-- JAM -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Jam Mulai</label>
                    <input type="time" id="jam_mulai"
                        class="w-full rounded-lg border px-3 py-2"
                        value="${data.jam_mulai ?? ''}">
                </div>
                <div>
                    <label class="block mb-1 font-medium">Jam Selesai</label>
                    <input type="time" id="jam_selesai"
                        class="w-full rounded-lg border px-3 py-2"
                        value="${data.jam_selesai ?? ''}">
                </div>
            </div>

            <!-- INSTRUKTUR -->
            <div>
                <label class="block mb-1 font-medium">Instruktur</label>
                <select id="instrukturs" multiple
                    class="w-full rounded-lg border px-3 py-2">
                    @foreach($instrukturs as $i)
                        <option value="{{ $i->id }}"
                            ${(data.instrukturs ?? []).includes({{ $i->id }}) ? 'selected' : ''}>
                            {{ $i->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Minimal 1 instruktur</p>
            </div>

            <!-- MATERI -->
            <div>
                <label class="block mb-1 font-medium">Materi</label>
                <select id="materis" multiple
                    class="w-full rounded-lg border px-3 py-2">
                    @foreach($materis as $m)
                        <option value="{{ $m->id }}"
                            ${(data.materis ?? []).includes({{ $m->id }}) ? 'selected' : ''}>
                            {{ $m->nama_materi }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Maksimal 2 materi</p>
            </div>

            <!-- STATUS -->
            <div>
                <label class="block mb-1 font-medium">Status</label>
                <select id="status"
                    class="w-full rounded-lg border px-3 py-2">
                    <option value="aktif" ${data.status !== 'nonaktif' ? 'selected' : ''}>Aktif</option>
                    <option value="nonaktif" ${data.status === 'nonaktif' ? 'selected' : ''}>Nonaktif</option>
                </select>
            </div>

        </div>
        `;

    }

    function toggleJenis(val) {
    document.getElementById('sekolahField').style.display =
        val === 'sekolah' ? 'block' : 'none';

    document.getElementById('homePrivateField').style.display =
        val === 'home_private' ? 'block' : 'none';
    }
</script>

<script>
    function handleSubmit(action, method) {

        const jenis = document.getElementById('jenis_jadwal').value;
        const nama  = document.getElementById('nama_kegiatan').value;
        const sekolah = document.getElementById('sekolah_id')?.value;
        const homePrivate = document.getElementById('home_private_id')?.value;

        const instrukturs = [...document.getElementById('instrukturs').selectedOptions];
        if (instrukturs.length === 0) {
            Swal.showValidationMessage('Minimal 1 instruktur harus dipilih');
            return Promise.reject();
        }

        const materis = [...document.getElementById('materis').selectedOptions];
        if (materis.length > 2) {
            Swal.showValidationMessage('Maksimal 2 materi');
            return Promise.reject();
        }



        if (!nama) {
            Swal.showValidationMessage('Nama kegiatan wajib diisi');
            return Promise.reject();
        }

        if (jenis === 'sekolah' && !sekolah) {
            Swal.showValidationMessage('Sekolah wajib dipilih');
            return Promise.reject();
        }

        if (jenis === 'home_private' && !homePrivate) {
            Swal.showValidationMessage('Home Private wajib dipilih');
            return Promise.reject();
        }

        return submitFormAjax(action, method);
    }

    function submitFormAjax(action, method) {

        const instrukturs = [...document.getElementById('instrukturs').selectedOptions]
            .map(o => o.value);

        const materis = [...document.getElementById('materis').selectedOptions]
            .map(o => o.value);

        const jenis = document.getElementById('jenis_jadwal').value;

        const formData = new FormData();
        formData.append('_token', document.getElementById('csrf').value);
        if (method !== 'POST') formData.append('_method', method);

        // ===============================
        // FIELD WAJIB
        // ===============================
        const mode = document.getElementById('mode_penjadwalan')?.value ?? 'single';
        formData.append('mode', mode);

        if (mode !== 'single') {
            formData.append('hari', document.getElementById('hari').value);
        }

        formData.append('jenis_jadwal', jenis);
        formData.append('nama_kegiatan', document.getElementById('nama_kegiatan').value);
        formData.append('tanggal_mulai', document.getElementById('tanggal_mulai').value);
        formData.append('tanggal_selesai', document.getElementById('tanggal_selesai').value);
        formData.append('jam_mulai', document.getElementById('jam_mulai').value);
        formData.append('jam_selesai', document.getElementById('jam_selesai').value);
        formData.append('status', document.getElementById('status').value);

        // ===============================
        // CONDITIONAL FIELD (INI INTINYA)
        // ===============================
        if (jenis === 'sekolah') {
            formData.append('sekolah_id', document.getElementById('sekolah_id').value);
        }

        if (jenis === 'home_private') {
            formData.append('home_private_id', document.getElementById('home_private_id').value);
        }

        instrukturs.forEach(i => formData.append('instrukturs[]', i));
        materis.forEach(m => formData.append('materis[]', m));

        return fetch(action, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(async response => {
            if (!response.ok) {
                const data = await response.json();
                return Promise.reject(data);
            }
            window.location.reload();
        })
        .catch(error => {
            console.error('ERROR RESPONSE:', error);
            handleAjaxError(error);
            return Promise.reject();
        });


    }

    function handleAjaxError(error) {


        let title = 'Gagal Menyimpan Jadwal';
        let message = 'Terjadi kesalahan saat menyimpan jadwal';

        if (error?.errors) {

            if (error.errors.materis) {
                title = 'Batas Materi';
                message = error.errors.materis.join('\n');
            }
            else if (error.errors.instrukturs) {
                title = 'Jadwal Bentrok';
                message = error.errors.instrukturs.join('\n');
            }
            else {
                message = Object.values(error.errors).flat().join('\n');
            }
        }

        Swal.showValidationMessage(message);
    }

    function toggleRecurring(mode) {
        document.getElementById('hariField').style.display =
            mode === 'single' ? 'none' : 'block';
    }


</script>



