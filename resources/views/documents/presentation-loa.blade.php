<!DOCTYPE html>
<html lang="{{ $document['locale'] }}" dir="{{ $document['locale'] === 'ar' ? 'rtl' : 'ltr' }}">
<head><meta charset="UTF-8"><style>body { font-family: dejavusans; font-size: 11pt; line-height: 1.7; color: #171717; } h1 { color: #047857; font-size: 21pt; } .identity { border-bottom: 1px solid #d4d4d4; padding-bottom: 18px; } .provenance { font-size: 8pt; color: #525252; margin-top: 35px; }</style></head>
<body>
@php
    $labels = [
        'en' => ['Presentation Letter of Acceptance', 'Number', 'Date', 'Paper ID', 'Title', 'Authors', 'ACCEPTED for presentation at this conference.', 'This letter confirms acceptance for presentation only. It is not publication acceptance or evidence of payment.'],
        'id' => ['Surat Penerimaan Presentasi', 'Nomor', 'Tanggal', 'Paper ID', 'Judul', 'Penulis', 'DITERIMA untuk dipresentasikan pada konferensi ini.', 'Surat ini hanya menyatakan penerimaan untuk presentasi, bukan penerimaan publikasi atau bukti pembayaran.'],
        'ar' => ['خطاب قبول العرض', 'الرقم', 'التاريخ', 'معرّف البحث', 'العنوان', 'المؤلفون', 'مقبول للعرض في هذا المؤتمر.', 'يؤكد هذا الخطاب قبول العرض فقط، ولا يمثل قبول النشر أو إثبات الدفع.'],
    ][$document['locale']];
@endphp
<div class="identity"><h1>{{ $labels[0] }}</h1><h2>{{ $document['edition'] }}</h2><p>{{ $document['host'] }}</p></div>
<p>{{ $labels[1] }}: <span dir="ltr">{{ $document['number'] }}</span><br>{{ $labels[2] }}: {{ $document['date'] }} ({{ $document['timezone'] }})</p>
<p>{{ $labels[3] }}: <span dir="ltr">{{ $document['paper_code'] }}</span></p>
<p><strong>{{ $labels[4] }}:</strong> {{ $document['title'] }}</p>
<p><strong>{{ $labels[5] }}:</strong> {{ implode('; ', $document['authors']) }}</p>
<p><strong>{{ $labels[6] }}</strong></p><p>{{ $labels[7] }}</p>
<div class="provenance" dir="ltr">ICHES · {{ $document['schema'] }}<br>Decision: {{ $document['decision_id'] }}<br>Snapshot: {{ $document['snapshot_id'] }}<br>SHA-256: {{ $document['snapshot_checksum'] }}<br>Issued: {{ $document['issued_at'] }}</div>
</body></html>
