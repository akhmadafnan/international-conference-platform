const messages = {
    en: {
        account: 'Reviewer account',
        chooseReviewer: 'Choose a reviewer',
        noReviewers:
            'No verified Reviewer accounts are available to add in this Edition.',
        addReviewer: 'Add Reviewer',
        editReviewer: 'Edit Reviewer Topics',
        edit: 'Edit',
        save: 'Save',
        noActiveTopics: 'No active Topics',
        editorHelp:
            'Select the reviewer’s Topics and save once. Clear all to remove active eligibility.',
        historicalHelp:
            'Eligibility for inactive historical Topics is preserved and cannot be edited here.',
        stale: 'This reviewer or Topic selection has changed. Reload and open Edit again before saving.',
        reload: 'Reload reviewers',
        title: 'Reviewer Topic Management',
        intro: 'Manage reviewer eligibility by Edition and Topic. Private manuscript access still requires a paper assignment.',
        edition: 'Conference Edition',
        open: 'Open Edition',
        email: 'Verified reviewer email',
        topics: 'Topics',
        grant: 'Grant Topic eligibility',
        empty: 'No reviewers yet. Add a reviewer and select their Topics.',
        noTopics:
            'No active Topics. Configure this Edition in Academic Setup first.',
        chooseEdition: 'Select an authorized Edition to continue.',
        active: 'Active',
        inactive: 'Inactive',
        unverified: 'Email not verified',
        disable: 'Deactivate',
        reactivate: 'Reactivate',
        confirmDisable: 'Deactivate Topic eligibility?',
        confirmReactivate: 'Reactivate Topic eligibility?',
        confirmDescription:
            'This changes access to the Topic queue. Existing paper assignments and review history remain unchanged.',
        cancel: 'Cancel',
        loading: 'Saving…',
        saved: 'Changes saved.',
        invalid: 'Check the fields and current Edition context.',
        reviewerInvalid:
            'Choose a verified account designated Reviewer for this Edition. Reload if its designation has changed.',
        topicInvalid: 'Choose active Topics belonging to this Edition.',
        reviewer: 'Reviewer',
        currentRouting: 'Current routing Topic',
        snapshotTopic: 'Topic in submitted snapshot',
        routingHelp:
            'Routing may differ from the historical Topic. This correction preserves the submitted snapshot and checksum.',
        routingReason: 'Reason for routing correction',
        correctRouting: 'Correct routing Topic',
        noTopic: 'No Topic',
        routingLocked:
            'Only unassigned SUBMITTED papers may have their routing corrected.',
        chooseTopic: 'Choose a Topic',
    },
    id: {
        account: 'Akun Reviewer',
        chooseReviewer: 'Pilih reviewer',
        noReviewers:
            'Tidak ada akun Reviewer terverifikasi yang dapat ditambahkan pada Edition ini.',
        addReviewer: 'Tambah Reviewer',
        editReviewer: 'Edit Topic Reviewer',
        edit: 'Edit',
        save: 'Simpan',
        noActiveTopics: 'Tanpa Topic aktif',
        editorHelp:
            'Pilih Topic reviewer lalu simpan sekali. Kosongkan pilihan untuk menonaktifkan eligibility aktif.',
        historicalHelp:
            'Eligibility Topic historis yang nonaktif tetap tersimpan dan tidak dapat diedit di sini.',
        stale: 'Data reviewer atau pilihan Topic telah berubah. Muat ulang dan buka Edit kembali sebelum menyimpan.',
        reload: 'Muat ulang reviewer',
        title: 'Pengelolaan Topic Reviewer',
        intro: 'Kelola eligibility reviewer per Edition dan Topic. Akses naskah privat tetap memerlukan penugasan paper.',
        edition: 'Edition Konferensi',
        open: 'Buka Edition',
        email: 'Email reviewer terverifikasi',
        topics: 'Topic',
        grant: 'Berikan eligibility Topic',
        empty: 'Belum ada reviewer. Tambahkan reviewer dan pilih Topic mereka.',
        noTopics:
            'Belum ada Topic aktif. Konfigurasikan Edition melalui Academic Setup terlebih dahulu.',
        chooseEdition: 'Pilih Edition yang Anda kuasai untuk melanjutkan.',
        active: 'Aktif',
        inactive: 'Nonaktif',
        unverified: 'Email belum terverifikasi',
        disable: 'Nonaktifkan',
        reactivate: 'Aktifkan kembali',
        confirmDisable: 'Nonaktifkan eligibility Topic?',
        confirmReactivate: 'Aktifkan kembali eligibility Topic?',
        confirmDescription:
            'Perubahan ini mengatur akses antrean Topic. Penugasan paper dan riwayat review tetap tersimpan.',
        cancel: 'Batal',
        loading: 'Menyimpan…',
        saved: 'Perubahan disimpan.',
        invalid: 'Periksa isian dan konteks Edition yang aktif.',
        reviewerInvalid:
            'Pilih akun terverifikasi dengan penetapan Reviewer pada Edition ini. Muat ulang jika penetapannya berubah.',
        topicInvalid: 'Pilih Topic aktif milik Edition ini.',
        reviewer: 'Reviewer',
        currentRouting: 'Topic routing saat ini',
        snapshotTopic: 'Topic pada snapshot pengiriman',
        routingHelp:
            'Routing dapat berbeda dari Topic historis. Koreksi ini mempertahankan snapshot dan checksum pengiriman.',
        routingReason: 'Alasan koreksi routing',
        correctRouting: 'Koreksi Topic routing',
        noTopic: 'Tanpa Topic',
        routingLocked:
            'Routing hanya dapat dikoreksi untuk paper SUBMITTED yang belum memiliki penugasan.',
        chooseTopic: 'Pilih Topic',
    },
    ar: {
        account: 'حساب المحكّم',
        chooseReviewer: 'اختر محكّمًا',
        noReviewers: 'لا توجد حسابات محكّمين موثقة متاحة للإضافة في هذه الدورة.',
        addReviewer: 'إضافة محكّم',
        editReviewer: 'تعديل موضوعات المحكّم',
        edit: 'تعديل',
        save: 'حفظ',
        noActiveTopics: 'لا توجد موضوعات نشطة',
        editorHelp:
            'اختر موضوعات المحكّم ثم احفظ مرة واحدة. أزل جميع الاختيارات لتعطيل الأهلية النشطة.',
        historicalHelp:
            'تُحفظ أهلية الموضوعات التاريخية غير النشطة ولا يمكن تعديلها هنا.',
        stale: 'تغيّرت بيانات المحكّم أو الموضوعات. أعد التحميل وافتح التعديل مجددًا قبل الحفظ.',
        reload: 'إعادة تحميل المحكّمين',
        title: 'إدارة موضوعات المحكّمين',
        intro: 'إدارة أهلية المحكّمين حسب الدورة والموضوع. يتطلب الوصول إلى المخطوطات الخاصة تكليفًا بالورقة.',
        edition: 'دورة المؤتمر',
        open: 'فتح الدورة',
        email: 'البريد الإلكتروني الموثق للمحكّم',
        topics: 'الموضوعات',
        grant: 'منح أهلية الموضوع',
        empty: 'لا توجد سجلات أهلية للمحكّمين في هذه الدورة.',
        noTopics:
            'لا توجد موضوعات نشطة. اضبط موضوعات الدورة من الإعداد الأكاديمي أولًا.',
        chooseEdition: 'اختر دورة مخولًا لك إدارتها للمتابعة.',
        active: 'نشط',
        inactive: 'غير نشط',
        unverified: 'البريد غير موثق',
        disable: 'تعطيل',
        reactivate: 'إعادة التفعيل',
        confirmDisable: 'تعطيل أهلية الموضوع؟',
        confirmReactivate: 'إعادة تفعيل أهلية الموضوع؟',
        confirmDescription:
            'يغير هذا الوصول إلى قائمة الموضوع. تبقى تكليفات الأوراق وسجل التحكيم دون تغيير.',
        cancel: 'إلغاء',
        loading: 'جارٍ الحفظ…',
        saved: 'تم حفظ التغييرات.',
        invalid: 'تحقق من الحقول وسياق الدورة الحالية.',
        reviewerInvalid:
            'اختر حسابًا موثقًا معيّنًا كمحكّم لهذه الدورة. أعد التحميل إذا تغيّر تعيينه.',
        topicInvalid: 'اختر موضوعات نشطة تخص هذه الدورة.',
        reviewer: 'المحكّم',
        currentRouting: 'موضوع التوجيه الحالي',
        snapshotTopic: 'الموضوع في نسخة الإرسال المحفوظة',
        routingHelp:
            'قد يختلف التوجيه عن الموضوع التاريخي. يحافظ هذا التصحيح على نسخة الإرسال ومجموع التحقق.',
        routingReason: 'سبب تصحيح التوجيه',
        correctRouting: 'تصحيح موضوع التوجيه',
        noTopic: 'بلا موضوع',
        routingLocked:
            'يمكن تصحيح توجيه الأوراق المرسلة التي ليس لها أي تكليف فقط.',
        chooseTopic: 'اختر موضوعًا',
    },
} as const;

export function reviewerTopicCopy(locale: string) {
    return (
        messages[locale.split('-')[0] as keyof typeof messages] ?? messages.en
    );
}
