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
        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700
               text-white px-4 py-2 rounded text-sm">
        + Tambah Jadwal
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
            title: '{{ $j->sekolah->nama_sekolah ?? "-" }}',
            start: '{{ $j->tanggal_mulai }}T{{ $j->jam_mulai }}',
            end: '{{ $j->tanggal_selesai }}T{{ $j->jam_selesai }}',
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
        width: 700,

        preConfirm: () => {
            return handleSubmit("{{ route('jadwal.store') }}", 'POST');
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
        width: 700,

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

    Swal.fire({
        title: 'Detail Jadwal',
        html: `
        <div class="text-left text-sm space-y-3">

            <div>
                <p class="font-semibold">Sekolah</p>
                <p>${j.sekolah_nama ?? '-'}</p>
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

    <div class="space-y-4 text-left text-sm">

        <div>
            <label class="block mb-1 font-medium">Sekolah</label>
            <select id="sekolah_id" class="w-full px-3 py-2 border rounded">
                <option value="">-- Pilih Sekolah --</option>
                @foreach($sekolahs as $s)
                <option value="{{ $s->id }}"
                    ${data.sekolah_id == {{ $s->id }} ? 'selected' : ''}>
                    {{ $s->nama_sekolah }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Nama Kegiatan</label>
            <input id="nama_kegiatan"
                class="w-full px-3 py-2 border rounded"
                value="${data.nama_kegiatan ?? ''}">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-medium">Tanggal Mulai</label>
                <input type="date" id="tanggal_mulai"
                    class="w-full px-3 py-2 border rounded"
                    value="${data.tanggal_mulai ?? ''}">
            </div>

            <div>
                <label class="block mb-1 font-medium">Tanggal Selesai</label>
                <input type="date" id="tanggal_selesai"
                    class="w-full px-3 py-2 border rounded"
                    value="${data.tanggal_selesai ?? ''}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-medium">Jam Mulai</label>
                <input type="time" id="jam_mulai"
                    class="w-full px-3 py-2 border rounded"
                    value="${data.jam_mulai ?? ''}">
            </div>

            <div>
                <label class="block mb-1 font-medium">Jam Selesai</label>
                <input type="time" id="jam_selesai"
                    class="w-full px-3 py-2 border rounded"
                    value="${data.jam_selesai ?? ''}">
            </div>
        </div>

        <div>
            <label class="block mb-1 font-medium">Instruktur</label>
            <select id="instrukturs" multiple
                class="w-full px-3 py-2 border rounded">
                @foreach($instrukturs as $i)
                <option value="{{ $i->id }}"
                    ${(data.instrukturs ?? []).includes({{ $i->id }}) ? 'selected' : ''}>
                    {{ $i->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Materi</label>
            <select id="materis" multiple
                class="w-full px-3 py-2 border rounded">
                @foreach($materis as $m)
                <option value="{{ $m->id }}"
                    ${(data.materis ?? []).includes({{ $m->id }}) ? 'selected' : ''}>
                    {{ $m->nama_materi }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-1 font-medium">Status</label>
            <select id="status" class="w-full px-3 py-2 border rounded">
                <option value="aktif" ${data.status !== 'nonaktif' ? 'selected' : ''}>Aktif</option>
                <option value="nonaktif" ${data.status === 'nonaktif' ? 'selected' : ''}>Nonaktif</option>
            </select>
        </div>

    </div>
    `;
}
</script>

<script>
function handleSubmit(action, method) {

    const nama = document.getElementById('nama_kegiatan').value;
    const sekolah = document.getElementById('sekolah_id').value;

    if (!nama || !sekolah) {
        Swal.showValidationMessage('Sekolah dan nama kegiatan wajib diisi');
        return Promise.reject();
    }

    return submitFormAjax(action, method);
}

//ajax error
function submitFormAjax(action, method) {

    const instrukturs = [...document.getElementById('instrukturs').selectedOptions]
        .map(o => o.value);

    const materis = [...document.getElementById('materis').selectedOptions]
        .map(o => o.value);

    const formData = new FormData();
    formData.append('_token', document.getElementById('csrf').value);
    if (method !== 'POST') formData.append('_method', method);

    formData.append('sekolah_id', document.getElementById('sekolah_id').value);
    formData.append('nama_kegiatan', document.getElementById('nama_kegiatan').value);
    formData.append('tanggal_mulai', document.getElementById('tanggal_mulai').value);
    formData.append('tanggal_selesai', document.getElementById('tanggal_selesai').value);
    formData.append('jam_mulai', document.getElementById('jam_mulai').value);
    formData.append('jam_selesai', document.getElementById('jam_selesai').value);
    formData.append('status', document.getElementById('status').value);

    instrukturs.forEach(i => formData.append('instrukturs[]', i));
    materis.forEach(m => formData.append('materis[]', m));

    return fetch(action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(async response => {

        if (!response.ok) {
            const data = await response.json();
            return Promise.reject(data);
        }

        window.location.reload();
    })
    .catch(error => {

        let message = 'Terjadi kesalahan saat menyimpan jadwal';

        if (error?.errors) {
            message = Object.values(error.errors).flat().join('\n');
        }

        // ⛔ TUTUP MODAL FORM
        Swal.close();

        // ✅ MODAL ERROR BARU
        Swal.fire({
            icon: 'error',
            title: 'Jadwal Bentrok',
            text: message,
            confirmButtonText: 'Mengerti'
        });

        return Promise.reject();
    });
}



</script>

<script>
function submitForm(action, method) {

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const instrukturs = [...document.getElementById('instrukturs').selectedOptions]
        .map(o => o.value);

    const materis = [...document.getElementById('materis').selectedOptions]
        .map(o => o.value);

    form.innerHTML = `
        <input type="hidden" name="_token" value="${document.getElementById('csrf').value}">
        ${method !== 'POST'
            ? `<input type="hidden" name="_method" value="${method}">`
            : ''
        }

        <input type="hidden" name="sekolah_id" value="${document.getElementById('sekolah_id').value}">
        <input type="hidden" name="nama_kegiatan" value="${document.getElementById('nama_kegiatan').value}">
        <input type="hidden" name="tanggal_mulai" value="${document.getElementById('tanggal_mulai').value}">
        <input type="hidden" name="tanggal_selesai" value="${document.getElementById('tanggal_selesai').value}">
        <input type="hidden" name="jam_mulai" value="${document.getElementById('jam_mulai').value}">
        <input type="hidden" name="jam_selesai" value="${document.getElementById('jam_selesai').value}">
        <input type="hidden" name="status" value="${document.getElementById('status').value}">
    `;

    instrukturs.forEach(id => {
        form.innerHTML += `<input type="hidden" name="instrukturs[]" value="${id}">`;
    });

    materis.forEach(id => {
        form.innerHTML += `<input type="hidden" name="materis[]" value="${id}">`;
    });

    document.body.appendChild(form);
    form.submit();
}
</script>

@endpush
