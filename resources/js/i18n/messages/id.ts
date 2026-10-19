export default {
    dataTable: {
        showing: 'Menampilkan {from}–{to} dari {total} entri',
        pagination: 'Navigasi halaman tabel',
        first: 'Pertama',
        previous: 'Sebelumnya',
        next: 'Berikutnya',
        last: 'Terakhir',
        page: 'Halaman {page}',
        ellipsis: 'Halaman lainnya',
    },
    admin: {
        series: {
            title: 'Conference Series',
            description: 'Kelola identitas rangkaian konferensi berkala.',
            create: 'Buat Conference Series',
            createDescription:
                'Isi identitas series. Series baru berstatus aktif secara otomatis.',
            code: 'Kode',
            codeHelp:
                'Spasi di awal dan akhir dihapus; huruf disimpan sebagai huruf besar.',
            name: 'Nama',
            acronym: 'Singkatan',
            about: 'Deskripsi',
            optional: 'opsional',
            status: 'Status',
            notProvided: 'Belum diisi',
            emptyTitle: 'Belum ada conference series',
            emptyDescription:
                'Buat conference series pertama melalui tombol di atas.',
            save: 'Buat series',
            saving: 'Menyimpan…',
            cancel: 'Batal',
            created: 'Conference series berhasil dibuat.',
            pagination: 'Halaman conference series',
            previous: 'Sebelumnya',
            next: 'Berikutnya',
            page: 'Halaman {current} dari {last}',
            languages: {
                id: 'Bahasa Indonesia',
                en: 'Bahasa Inggris',
                ar: 'Bahasa Arab',
            },
            statuses: {
                ACTIVE: 'Aktif',
                INACTIVE: 'Tidak aktif',
                ARCHIVED: 'Diarsipkan',
                unknown: 'Status tidak tersedia',
            },
            errors: {
                required: 'Kolom ini wajib diisi.',
                text: 'Isi kolom ini dengan teks.',
                shortText: 'Gunakan maksimal 255 karakter.',
                longText: 'Gunakan maksimal 10.000 karakter.',
                duplicate: 'Kode ini sudah digunakan.',
                languages:
                    'Deskripsi hanya mendukung bahasa Indonesia, Inggris, dan Arab.',
                invalid: 'Periksa nilai pada kolom ini.',
                summary: 'Periksa kolom yang ditandai sebelum menyimpan.',
            },
        },
        dashboard: {
            title: 'Ringkasan administrasi',
            eyebrow: 'Ruang Superadmin ICHES',
            description:
                'Ringkasan global edisi konferensi dari seluruh series.',
            editionCount: 'Jumlah edisi',
            recentEditions: 'Edisi terbaru',
            recentDescription:
                'Maksimal 10 edisi berdasarkan waktu pembuatan, dari yang terbaru.',
            emptyTitle: 'Belum ada edisi konferensi',
            emptyDescription:
                'Edisi konferensi akan tampil di sini setelah tersedia.',
            seriesUnavailable: 'Series tidak tersedia',
            year: 'Tahun',
            status: 'Status',
            statuses: {
                DRAFT: 'Draf',
                ACTIVE: 'Aktif',
                PUBLISHED: 'Dipublikasikan',
                COMPLETED: 'Selesai',
                CANCELLED: 'Dibatalkan',
                ARCHIVED: 'Diarsipkan',
                unknown: 'Status tidak tersedia',
            },
        },
    },
    locale: {
        label: 'Bahasa',
    },
    navigation: {
        platform: 'Platform',
        dashboard: 'Dasbor',
        repository: 'Repositori',
        documentation: 'Dokumentasi',
    },
    account: {
        settings: 'Pengaturan',
        logout: 'Keluar',
    },
    phase02: {
        dashboard: {
            eyebrow: 'Ruang peserta ICHES',
            title: 'Ringkasan registrasi',
            description:
                'Status registrasi, pembayaran, dan Event Pass Anda dalam satu tempat.',
            continue: 'Lanjutkan registrasi',
            registration: 'Registrasi',
            eventPassReady: 'Event Pass Anda sudah siap digunakan.',
            eventPassPending:
                'Event Pass akan tersedia setelah registrasi dikonfirmasi.',
            noRegistration: 'Belum ada registrasi konferensi',
            noRegistrationHelp:
                'Pilih paket partisipasi pada halaman registrasi Conference Edition yang aktif.',
        },
        registration: {
            title: 'Registrasi konferensi',
            backToDashboard: 'Kembali ke dasbor',
            choosePackage: 'Pilih paket partisipasi',
            choosePackageHelp:
                'Pilih satu paket aktif untuk memulai registrasi.',
            selectPackage: 'Pilih paket',
            registrationId: 'ID Registrasi',
            noPackages: 'Saat ini belum ada paket partisipasi yang tersedia.',
            unavailable: 'Registrasi untuk paket ini belum tersedia saat ini.',
            alreadyRegistered:
                'Anda sudah memiliki registrasi pada Conference Edition ini.',
            invalidPackage: 'Pilih paket partisipasi yang tersedia.',
        },
        billing: {
            free: 'GRATIS',
            complimentary: 'KOMPLIMEN',
            noPaymentRequired:
                'Registrasi ini tidak memerlukan pembayaran maupun bukti pembayaran.',
        },
        registrationStatus: {
            PENDING: 'Registrasi dimulai',
            PAYMENT_PENDING: 'Menunggu pembayaran',
            CONFIRMED: 'Registrasi dikonfirmasi',
            CANCELLED: 'Registrasi dibatalkan',
        },
        nextAction: {
            label: 'Tindakan berikutnya',
            EVENT_PASS_AVAILABLE: 'Buka Event Pass Anda',
            REPLACE_PAYMENT_PROOF: 'Ganti bukti pembayaran Anda',
            WAIT_FINANCE_VERIFICATION: 'Tunggu verifikasi Tim Keuangan',
            SUBMIT_PAYMENT_PROOF: 'Unggah bukti pembayaran',
            REGISTRATION_CONFIRMED: 'Registrasi telah dikonfirmasi',
            REGISTRATION_PENDING: 'Selesaikan registrasi Anda',
        },
        form: {
            required: 'Wajib',
            optional: 'Opsional',
        },
        payment: {
            title: 'Pembayaran',
            amount: 'Jumlah yang harus dibayar',
            status: 'Status pembayaran',
            bank: 'Bank',
            accountHolder: 'Nama pemilik rekening',
            accountNumber: 'Nomor rekening',
            copy: 'Salin',
            copied: 'Tersalin',
            instructions: 'Petunjuk pembayaran',
            correctionRequired:
                'Perlu tindakan: bukti pembayaran harus diperbaiki',
            proof: 'Bukti pembayaran',
            transferredAmount: 'Jumlah yang ditransfer',
            senderName: 'Nama pengirim',
            transferDate: 'Tanggal transfer',
            replaceProof: 'Ganti bukti',
            submitProof: 'Kirim bukti pembayaran',
            proofInvalid:
                'Unggah file PDF, JPG, JPEG, atau PNG maksimal 10 MB.',
            amountInvalid:
                'Masukkan jumlah transfer yang valid dan lebih dari nol.',
            senderInvalid: 'Nama pengirim maksimal 255 karakter.',
            transferDateInvalid: 'Masukkan tanggal transfer yang valid.',
        },
        paymentStatus: {
            PENDING: 'Menunggu bukti pembayaran',
            SUBMITTED: 'Bukti pembayaran diterima',
            CORRECTION_REQUIRED: 'Perlu perbaikan',
            VERIFIED: 'Pembayaran terverifikasi',
            CANCELLED: 'Pembayaran dibatalkan',
        },
        eventPass: {
            title: 'Event Pass',
            open: 'Buka Event Pass',
            back: 'Kembali ke dasbor',
            participant: 'Peserta',
            package: 'Paket partisipasi',
            confirmed: 'REGISTRASI DIKONFIRMASI',
            qrAlt: 'Kode QR Event Pass',
            lookupNote:
                'QR ini hanya memuat identitas lookup yang tidak mengandung PII; pemindaian tidak otomatis mencatat kehadiran.',
        },
    },
};
