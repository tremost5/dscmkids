@extends('admin.layout')

@section('title', 'Pembayaran PRA 2026')

@section('content')
@include('admin.pra.partials.nav')

<div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Belum Bayar</div><strong>{{ $stats['unpaid'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Menunggu Verifikasi</div><strong>{{ $stats['pending'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Lunas</div><strong>{{ $stats['paid'] }}</strong></div>
    <div class="stat-card"><div class="stat-label">Total Peserta</div><strong>{{ $stats['total'] }}</strong></div>
</div>

<div class="toolbar" style="margin-top:16px;">
    <form method="GET" class="toolbar-actions">
        <input
            type="text"
            name="search"
            class="input-compact"
            placeholder="Cari nama, ortu, WA, kelas..."
            value="{{ $search ?? '' }}"
            style="min-width:280px;">

        <select name="filter" class="select-compact">
            <option value="">Semua</option>
            <option value="unpaid" @selected($filter === 'unpaid')>Belum Bayar</option>
            <option value="pending_verification" @selected($filter === 'pending_verification')>Menunggu Verifikasi</option>
            <option value="paid" @selected($filter === 'paid')>Lunas</option>
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
    </form>
    <a class="btn btn-secondary" href="{{ route('admin.pra.export', ['type' => 'payments']) }}">Export Pembayaran</a>
</div>

<section class="table-shell">
    <div class="table-scroller">
        <table>
            <thead>
<tr>
    <th>Nama</th>
    <th>Grup</th>
    <th>Metode</th>
    <th>Bukti</th>
    <th>Status</th>
    <th>Aksi</th>
    <th>Keterangan</th>
</tr>
</thead>
            <tbody>
            @forelse($registrations as $registration)
                <tr>
                    <td><strong>{{ $registration->full_name }}</strong><br><span class="muted">{{ $registration->parent_name }} - {{ $registration->whatsapp_number }}</span></td>
                    <td>{{ $registration->group?->name }}</td>
                    <td>
    <select
        name="payment_method"
        form="payment-form-{{ $registration->id }}"
        onchange="toggleProofUpload({{ $registration->id }}, this.value)"
    >
        <option value="cash" @selected($registration->payment_method === 'cash')>
            Cash
        </option>

        <option value="transfer" @selected($registration->payment_method === 'transfer')>
            Transfer
        </option>
    </select>
</td>

<td style="min-width:220px;">

    @if($registration->paymentProof)

        <a
            href="{{ asset('storage/'.$registration->paymentProof->file_path) }}"
            target="_blank">
            Lihat Bukti
        </a>

        <br><br>

        <img
            src="{{ asset('storage/'.$registration->paymentProof->file_path) }}"
            style="max-width:180px;border-radius:8px;display:block;margin-bottom:10px;">

    @endif

    <div
        id="proof-upload-{{ $registration->id }}"
        style="{{ $registration->payment_method === 'transfer' ? '' : 'display:none;' }}"
    >

        @if(!$registration->paymentProof)
            <span style="color:red;font-weight:bold;">
                Belum upload bukti transfer
            </span>

            <br><br>
        @endif

        <input
            type="file"
            name="payment_proof"
            form="payment-form-{{ $registration->id }}"
            accept=".jpg,.jpeg,.png,.webp">

    </div>

    @if($registration->payment_method === 'cash')
        <span class="muted">
            Pembayaran Cash
        </span>
    @endif

</td>

<td>

@if(
    $registration->payment_method === 'transfer'
    && !$registration->paymentProof
)

    <span style="color:red;font-weight:bold;">
        Menunggu Bukti
    </span>

@else

    {{ $registration->paymentStatusLabel() }}

@endif

</td>

    <td>
    <form
    id="payment-form-{{ $registration->id }}"
    method="POST"
    enctype="multipart/form-data"
        action="{{ route('admin.pra.payments.update', $registration) }}"
        class="inline-form"
    >
        @csrf
        @method('PATCH')

        <select name="payment_status">
            <option value="unpaid" @selected($registration->payment_status === 'unpaid')>
                Belum Bayar
            </option>

            <option value="pending_verification" @selected($registration->payment_status === 'pending_verification')>
                Menunggu Verifikasi
            </option>

            <option value="paid" @selected($registration->payment_status === 'paid')>
                Lunas
            </option>
        </select>

        <button class="btn btn-primary" type="submit">
            Simpan
        </button>
    </form>
</td>

<td style="min-width:220px;">
    <textarea
        name="payment_notes"
        form="payment-form-{{ $registration->id }}"
        rows="2"
        placeholder="Catatan pembayaran..."
    >{{ $registration->payment_notes }}</textarea>
</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-state">Belum ada data pembayaran.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $registrations->links() }}
</section>
<script>
let paymentSearchTimer;

document
    .querySelector('input[name="search"]')
    ?.addEventListener('keyup', function () {
        clearTimeout(paymentSearchTimer);

        paymentSearchTimer = setTimeout(() => {
            this.form?.submit();
        }, 400);
    });

function toggleProofUpload(id, value)
{
    const el = document.getElementById('proof-upload-' + id);

    if (!el) return;

    if (value === 'transfer') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>
@endsection
