# Warek I — Product Decision Sheet

**Document:** ICHES Product Validation  
**Status:** READY FOR PRESENTATION / DOMAIN VALIDATION  
**Audience:** Warek I / Academic & Organizing Authority  
**Updated:** 2026-09-30

## 1. Tujuan Pertemuan

Dokumen ini tidak meminta keputusan teknis seperti framework, database, hosting, atau desain tabel.

Tujuannya hanya memastikan bahwa **alur bisnis ICHES** sudah sesuai dengan kebijakan kegiatan sebelum aplikasi masuk ke tahap blueprint final dan development.

## 2. Gambaran Produk

ICHES dirancang sebagai **Conference & Event Experience Platform** yang menyatukan:

- informasi conference;
- registrasi dan pembayaran;
- submission dan review;
- LoA;
- Full Article;
- scheduling;
- operasional hari-H;
- assessment/revision;
- publication handoff;
- award;
- certificate;
- arsip edition.

Aplikasi tetap sederhana pada sisi pengguna dan tidak dimaksudkan menjadi ERP atau pengganti OJS.

## 3. Alur Utama yang Sudah Dirancang

```text
PUBLIC WEBSITE
      ↓
REGISTER
      ↓
SELECT PARTICIPATION PACKAGE
      ↓
PAYMENT
      ↓
FINANCE VERIFICATION
      ↓
REGISTRATION CONFIRMED
      ↓
EVENT PASS / QR
      ↓
SUBMIT ABSTRACT
      ↓
ADMINISTRATIVE CHECK
      ↓
SINGLE-ANONYMOUS REVIEW
      ↓
ACADEMIC DECISION
      ↓
ACCEPTED
      ↓
PRESENTATION LoA
      ↓
UPLOAD FULL ARTICLE
      ↓
CONFIRM ACTUAL PRESENTER
      ↓
SCHEDULING POOL
      ↓
SESSION / ROOM / REVIEWER / MODERATOR / SLOT
      ↓
SCHEDULE PUBLISHED
      ↓
PRESENTATION
      ↓
ASSESSMENT
      ↓
REVISION / NO REVISION
      ↓
FINAL ACC
      ↓
READY FOR PRODUCTION
      ↓
PROCEEDINGS / SELECTED JOURNAL
      ↓
CERTIFICATES / AWARDS
```

## 4. Keputusan yang Perlu Dikonfirmasi Warek I

### D-01 — Peserta sudah bayar tetapi abstract ditolak

**Rancangan yang direkomendasikan:** pembayaran adalah biaya keikutsertaan event. Jika abstract ditolak, jalur presenter berhenti tetapi registrasi participant tetap aktif.

Konsekuensi:
- tetap dapat mengikuti conference;
- tidak memperoleh status Presenter;
- refund tidak otomatis karena academic rejection;
- refund tetap dapat digunakan untuk kasus khusus seperti duplicate payment, pembatalan event, atau keputusan administratif.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-02 — Model paket partisipasi

Frontend dapat menampilkan empat paket sederhana:
1. Conference Only
2. Conference + Evening/MoU
3. Full Program: Conference + Evening/MoU + International Community Service
4. International Community Service Only

**Rancangan yang direkomendasikan:** paket bersifat fixed per edition tetapi dapat diubah oleh admin. Peserta cukup memilih satu paket dan tidak perlu merakit aktivitas satu per satu.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-03 — Matriks biaya

Perlu dipastikan apakah harga hanya mengikuti paket atau juga dibedakan berdasarkan kategori peserta.

Pilihan yang mungkin:
- package only;
- student / general;
- domestic / international;
- kategori institusi lain jika memang diperlukan.

**Rancangan yang direkomendasikan:** gunakan `Participation Package + optional Participant Category` agar tetap sederhana tetapi cukup fleksibel.

**Keputusan Warek I / matriks biaya:**  
____________________________________________________________

---

### D-04 — Kebijakan abstract review

Model review yang dirancang: **single-anonymous**.

Yang masih perlu dikonfirmasi:
- default jumlah reviewer per abstract;
- apakah satu kali revision cycle cukup sebelum final Accept/Reject.

**Rancangan yang direkomendasikan:**
- reviewer count configurable per edition/track;
- satu revision cycle sebagai default V1, dengan exception oleh Academic Authority bila diperlukan.

**Keputusan Warek I:**  
Reviewer per abstract: ______  
Revision cycle default: ______

---

### D-05 — Full Article dan actual presenter

Setelah abstract accepted:
- LoA terbit;
- Full Article upload aktif;
- setelah Full Article masuk, author mengonfirmasi siapa actual presenter dari contributor list;
- baru kemudian paper masuk scheduling.

**Rancangan yang direkomendasikan:** corresponding author dan presenter boleh berbeda.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-06 — Presentation Slides

Aplikasi dapat mendukung upload PPT/slides setelah acceptance.

**Rancangan yang direkomendasikan:** dibuat configurable per edition, tidak hardcoded wajib.

Untuk edition ICHES yang akan datang:  
☐ Wajib  
☐ Opsional  
☐ Tidak digunakan

---

### D-07 — Mode pelaksanaan edition

Arsitektur mendukung:
- Offline
- Online
- Hybrid

Untuk edition yang akan datang:

☐ Offline  
☐ Online  
☐ Hybrid

Catatan: field attendance mode hanya ditampilkan kepada peserta jika memang relevan.

---

### D-08 — Award

Aplikasi tidak menentukan pemenang.

```text
Reviewer Assessment
        ↓
Candidate Evidence
        ↓
Committee Deliberation
        ↓
Committee Final Decision
        ↓
Decision recorded in application
```

**Rancangan yang direkomendasikan:**
- Best Presenter = satu pemenang Overall edition;
- Best Article = winning paper, seluruh author menerima certificate individual;
- Committee boleh membuang kandidat sistem atau menetapkan kandidat lain.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-09 — Certificate

**Rancangan yang direkomendasikan:**
- Participant Certificate berdasarkan attendance;
- Presenter Certificate berdasarkan status PRESENTED;
- seorang Presenter dapat menerima Participant + Presenter Certificate;
- Reviewer Certificate berdasarkan completion of duties;
- Moderator Certificate berdasarkan duty completion;
- Committee/Appreciation Certificate tersedia;
- Award Certificate muncul setelah Committee finalize award.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-10 — Publication destination

Setelah Final ACC, paper masuk Ready for Production.

Pilihan destination:
- Proceedings;
- Selected Journal.

**Rancangan yang direkomendasikan:** Proceedings menjadi default untuk paper yang memenuhi syarat, sedangkan paper tertentu dapat dialihkan ke Selected Journal berdasarkan keputusan Academic/Publication Team.

Catatan:
- Selected for Journal **bukan** berarti Accepted by Journal;
- journal tetap memiliki otoritas editorialnya sendiri.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-11 — Publication documents

Dokumen yang dibedakan:

**Presentation LoA**
- terbit setelah abstract accepted;
- menyatakan accepted for presentation.

**Proceedings Publication Acceptance**
- opsional setelah Final ACC dan destination Proceedings ditetapkan.

**Selected Journal**
- conference hanya menerbitkan Selection/Handoff Notice;
- Publication Acceptance berasal dari jurnal setelah jurnal benar-benar menerima.

**Keputusan Warek I:**  
☐ Setuju  
☐ Perlu revisi: ______________________________

---

### D-12 — Application Authority

Jabatan kepanitiaan tidak otomatis sama dengan kewenangan aplikasi.

Perlu ditentukan siapa/unsur mana yang berwenang untuk:

| Kewenangan | Authority yang ditetapkan |
|---|---|
| Verifikasi pembayaran | __________________ |
| Final academic decision abstract | __________________ |
| Publish schedule | __________________ |
| Finalize Best Article / Best Presenter | __________________ |
| Menetapkan publication destination | __________________ |
| Issue/revoke certificate | __________________ |

## 5. Hal yang Tidak Perlu Diputuskan Warek I Sekarang

Tahap validasi ini tidak membahas:

- Laravel / Vue / framework;
- database;
- ERD;
- server/hosting;
- queue;
- storage provider;
- struktur source code;
- detail API.

Hal-hal tersebut baru diputuskan setelah Product Blueprint v1 dan scholarly metadata contract selesai.

## 6. Batas Scope V1

Aplikasi tidak diarahkan menjadi:

- ERP;
- hotel booking system;
- travel management;
- full contract/MoU lifecycle system;
- OJS replacement;
- AI automatic scheduler;
- AI automatic award winner;
- full helpdesk/ticketing;
- microservices architecture.

## 7. Setelah Validasi Warek I

```text
WAREK I VALIDATION
      ↓
PRODUCT BLUEPRINT v1
      ↓
CORRECTIVE PHASE 0 RE-BASELINE
      ↓
SUBMISSION & SCHOLARLY METADATA CONTRACT
      ↓
STACK + ERD FREEZE
      ↓
DEVELOPMENT PLAN
      ↓
CODING
```

## 8. Catatan Validasi

Tanggal: __________________

Catatan Warek I:

____________________________________________________________

____________________________________________________________

____________________________________________________________

Status:

☐ Disetujui sebagai arah produk  
☐ Disetujui dengan koreksi  
☐ Perlu pembahasan lanjutan
