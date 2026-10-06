export default {
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
