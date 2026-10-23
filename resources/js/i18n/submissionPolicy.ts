const messages = {
    id: {
        onePerEdition:
            'Maksimum satu abstrak per pengguna per Edition. Lanjutkan draft yang tersedia atau lihat status submission resmi.',
        alreadyExists:
            'Anda sudah memiliki abstrak untuk Edition ini. Lanjutkan draft atau lihat submission yang tersedia.',
        edit: 'Edit Draft',
        remove: 'Hapus Draft',
        view: 'Lihat Submission / Status Akademik',
        confirmTitle: 'Hapus draft ini?',
        confirmDescription:
            'Draft beserta metadata dan file khusus draft akan dihapus. Tindakan ini tidak dapat dibatalkan. Registrasi dan pembayaran tetap tersimpan.',
        cancel: 'Batal',
        deleting: 'Menghapus…',
        deleteFailed:
            'Draft tidak dapat dihapus. Hanya draft milik Anda yang belum memiliki bukti akademik yang dapat dihapus. Muat ulang halaman untuk memeriksa status terbaru.',
    },
    en: {
        onePerEdition:
            'One abstract per user per Edition. Continue an existing draft or view your official submission status.',
        alreadyExists:
            'You already have an abstract for this Edition. Continue your draft or view the existing submission.',
        edit: 'Edit Draft',
        remove: 'Delete Draft',
        view: 'View Submission / Academic Status',
        confirmTitle: 'Delete this draft?',
        confirmDescription:
            'The draft, its metadata and draft-only files will be deleted. This cannot be undone. Your registration and payments will be preserved.',
        cancel: 'Cancel',
        deleting: 'Deleting…',
        deleteFailed:
            'This draft cannot be deleted. Only your own draft without academic evidence can be deleted. Reload the page to check its latest status.',
    },
    ar: {
        onePerEdition:
            'ملخص واحد لكل مستخدم في كل دورة مؤتمر. تابع المسودة الموجودة أو اعرض حالة الإرسال الرسمي.',
        alreadyExists:
            'لديك ملخص لهذه الدورة بالفعل. تابع مسودتك أو اعرض الإرسال الموجود.',
        edit: 'تعديل المسودة',
        remove: 'حذف المسودة',
        view: 'عرض الإرسال / الحالة الأكاديمية',
        confirmTitle: 'حذف هذه المسودة؟',
        confirmDescription:
            'ستُحذف المسودة وبياناتها وملفاتها الخاصة. لا يمكن التراجع عن هذا الإجراء. سيُحتفظ بالتسجيل والمدفوعات.',
        cancel: 'إلغاء',
        deleting: 'جارٍ الحذف…',
        deleteFailed:
            'لا يمكن حذف هذه المسودة. يمكن حذف مسودتك فقط إذا لم تتضمن أدلة أكاديمية. أعد تحميل الصفحة للتحقق من أحدث حالة.',
    },
} as const;

export function submissionPolicyCopy(locale: string) {
    return (
        messages[locale.split('-')[0] as keyof typeof messages] ?? messages.en
    );
}
