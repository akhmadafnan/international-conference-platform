export const wizardUx = {
    en: {
        references: 'References',
        selectTrack: 'Select a Track',
        tracksOptionalEmpty:
            'No active Tracks are available for this Edition. You may continue without a Track.',
        tracksRequiredEmpty:
            'This Edition requires a Track, but none are active. An Admin must configure an active Track before submission.',
        readyToSubmit: 'Ready to submit',
        needsCorrection: 'Please resolve the items below before submitting.',
        fixStep: 'Go to {step}',
        adminConfiguration:
            'An Admin must correct the Edition configuration before submission. Please contact the conference team.',
        titleRequired: 'Enter the abstract title.',
        abstractRequired: 'Enter the abstract text.',
        keywordsBelow:
            'Only {actual} keywords. Add at least {remaining} more (Edition policy: {min}–{max}).',
        keywordsAbove:
            '{actual} keywords. Remove at least {excess} (Edition policy: {min}–{max}).',
        keywordsInvalid:
            'Check keyword values, duplicates and order in Details.',
        trackInvalid: 'Select a valid active Track for this Edition.',
        authorsRequired: 'Add at least one contributor.',
        correspondingRequired: 'Select exactly one corresponding author.',
        orcidInvalid:
            'Correct the supplied ORCID or leave it empty. ORCID is optional.',
        affiliationInvalid:
            'Check each author’s affiliation, country and affiliation order. ROR is optional.',
        emailInvalid: 'Enter a valid email address for each author.',
        nameInvalid: 'Complete each author’s name.',
        countryInvalid:
            'Enter a valid two-letter country code for each author.',
        authorOrderInvalid: 'Check the contributor order and save it again.',
        fileRequired:
            'Upload a valid abstract file as required by this Edition.',
        reviewRequired:
            'Check and save the required information before submitting.',
        details: 'Details',
        files: 'Upload Files',
        contributors: 'Contributors',
        review: 'Review & Submit',
        steps: 'Submission steps',
        progress: 'Step {current} of {total}',
        intro: 'Prepare your abstract in five steps. Save a draft at any time. Presenter payment follows academic acceptance.',
        previous: 'Previous',
        continue: 'Continue',
        saveDraft: 'Save Draft',
        saving: 'Saving…',
        saved: 'Draft saved. It has not been officially submitted.',
        edit: 'Edit',
        cancel: 'Cancel',
        moveUp: 'Move author up',
        moveDown: 'Move author down',
        keywords: 'Keywords',
        removeKeyword: 'Remove {keyword}',
        keywordHint: 'Type a keyword, then Enter or comma',
        keywordCount: '{count} keywords',
        keywordRange: 'Edition policy: {min}–{max} keywords',
        editorTools: 'Abstract tools',
        plainText:
            'Academic metadata is stored as plain text. Bold/italic markup is unavailable. Select numbers to apply scientific superscript/subscript; links are inserted as text.',
        superscript: 'Superscript selected numbers',
        subscript: 'Subscript selected numbers',
        insertLink: 'Insert URL',
        invalidLink: 'Use a valid HTTP or HTTPS URL.',
        wordCount: '{count} words',
        bulkReferences: 'Reference list',
        oneReferencePerLine:
            'One reference per line. Paste your bibliography here. Existing DOI/URL metadata is retained for unchanged citations.',
        individualReferences: 'Toggle individual citation / DOI / URL editing',
        confirmReferenceRemoval: 'Confirm removed references',
        referenceRemovalHelp:
            'These saved entries are no longer in your list. Confirm only if you intend to remove them.',
        confirmChanges: 'Confirm changes',
        READY: 'Ready',
        WARNING: 'Warning — optional information',
        BLOCKED: 'Blocked — correction required',
        paidAfterAccept: 'Paid package — payment after abstract acceptance',
        awaitingPayment:
            'Abstract accepted. Payment instructions will appear once the obligation is available.',
        paidNoObligation:
            'Paid package — no payment obligation is currently available.',
        actions: {
            SUBMIT_ABSTRACT: 'Submit Abstract',
            CONTINUE_ABSTRACT: 'Continue Abstract',
            WAIT_ACADEMIC_REVIEW:
                'Abstract submitted — awaiting academic review',
            VIEW_ACADEMIC_STATUS: 'View academic decision',
            WAIT_PAYMENT_SETUP: 'Accepted — awaiting payment instructions',
            WAIT_REGISTRATION_CONFIRMATION:
                'Awaiting registration confirmation',
            REGISTRATION_CANCELLED: 'Registration cancelled',
        },
    },
    id: {
        references: 'Referensi',
        selectTrack: 'Pilih Track',
        tracksOptionalEmpty:
            'Belum ada Track aktif pada Edition ini. Anda dapat melanjutkan tanpa Track.',
        tracksRequiredEmpty:
            'Edition ini mewajibkan Track, tetapi belum ada Track aktif. Admin harus mengonfigurasi Track aktif sebelum abstrak dapat dikirim.',
        readyToSubmit: 'Siap dikirim',
        needsCorrection: 'Perbaiki hal berikut sebelum mengirim abstrak.',
        fixStep: 'Ke tahap {step}',
        adminConfiguration:
            'Admin perlu memperbaiki konfigurasi Edition sebelum abstrak dapat dikirim. Hubungi panitia konferensi.',
        titleRequired: 'Isi judul abstrak.',
        abstractRequired: 'Isi teks abstrak.',
        keywordsBelow:
            'Baru {actual} kata kunci. Tambahkan minimal {remaining} kata kunci lagi (ketentuan {min}–{max}).',
        keywordsAbove:
            'Ada {actual} kata kunci. Hapus minimal {excess} kata kunci (ketentuan {min}–{max}).',
        keywordsInvalid:
            'Periksa isi, duplikasi, dan urutan kata kunci pada Detail.',
        trackInvalid: 'Pilih Track aktif yang valid untuk Edition ini.',
        authorsRequired: 'Tambahkan minimal satu kontributor.',
        correspondingRequired: 'Pilih tepat satu penulis korespondensi.',
        orcidInvalid:
            'Perbaiki ORCID yang diisi atau kosongkan. ORCID bersifat opsional.',
        affiliationInvalid:
            'Periksa afiliasi, negara, dan urutan afiliasi setiap penulis. ROR bersifat opsional.',
        emailInvalid: 'Isi alamat email yang valid untuk setiap penulis.',
        nameInvalid: 'Lengkapi nama setiap penulis.',
        countryInvalid:
            'Isi kode negara dua huruf yang valid untuk setiap penulis.',
        authorOrderInvalid: 'Periksa urutan kontributor dan simpan kembali.',
        fileRequired:
            'Unggah file abstrak yang valid sesuai ketentuan Edition.',
        reviewRequired:
            'Periksa dan simpan informasi wajib sebelum mengirim abstrak.',
        details: 'Detail',
        files: 'Unggah File',
        contributors: 'Kontributor',
        review: 'Tinjau & Kirim',
        steps: 'Tahapan pengiriman abstrak',
        progress: 'Langkah {current} dari {total}',
        intro: 'Siapkan abstrak dalam lima langkah. Simpan draft kapan saja. Pembayaran presenter dilakukan setelah keputusan akademik ACCEPT.',
        previous: 'Sebelumnya',
        continue: 'Lanjutkan',
        saveDraft: 'Simpan Draft',
        saving: 'Menyimpan…',
        saved: 'Draft tersimpan. Abstrak belum dikirim secara resmi.',
        edit: 'Edit',
        cancel: 'Batal',
        moveUp: 'Naikkan urutan penulis',
        moveDown: 'Turunkan urutan penulis',
        keywords: 'Kata kunci',
        removeKeyword: 'Hapus {keyword}',
        keywordHint: 'Ketik kata kunci, lalu Enter atau koma',
        keywordCount: '{count} kata kunci',
        keywordRange: 'Kebijakan Edition: {min}–{max} kata kunci',
        editorTools: 'Alat editor abstrak',
        plainText:
            'Metadata akademik disimpan sebagai teks biasa. Markup tebal/miring tidak tersedia. Pilih angka untuk superskrip/subskrip ilmiah; tautan disisipkan sebagai teks.',
        superscript: 'Superskrip angka terpilih',
        subscript: 'Subskrip angka terpilih',
        insertLink: 'Sisipkan URL',
        invalidLink: 'Gunakan URL HTTP atau HTTPS yang valid.',
        wordCount: '{count} kata',
        bulkReferences: 'Daftar pustaka',
        oneReferencePerLine:
            'Satu referensi per baris. Tempel daftar pustaka di sini. Metadata DOI/URL dipertahankan untuk sitasi yang tidak berubah.',
        individualReferences: 'Beralih ke edit sitasi / DOI / URL individual',
        confirmReferenceRemoval: 'Konfirmasi referensi yang dihapus',
        referenceRemovalHelp:
            'Entri tersimpan berikut tidak lagi ada dalam daftar. Konfirmasi hanya jika memang ingin menghapusnya.',
        confirmChanges: 'Konfirmasi perubahan',
        READY: 'Siap',
        WARNING: 'Peringatan — informasi opsional',
        BLOCKED: 'Terblokir — perlu perbaikan',
        paidAfterAccept: 'Paket berbayar — pembayaran setelah abstrak ACCEPT',
        awaitingPayment:
            'Abstrak diterima. Instruksi pembayaran akan muncul setelah kewajiban pembayaran tersedia.',
        paidNoObligation:
            'Paket berbayar — belum ada kewajiban pembayaran yang tersedia.',
        actions: {
            SUBMIT_ABSTRACT: 'Mulai Abstrak',
            CONTINUE_ABSTRACT: 'Lanjutkan Draft',
            WAIT_ACADEMIC_REVIEW: 'Abstrak terkirim — menunggu proses akademik',
            VIEW_ACADEMIC_STATUS: 'Lihat keputusan akademik',
            WAIT_PAYMENT_SETUP: 'Diterima — menunggu instruksi pembayaran',
            WAIT_REGISTRATION_CONFIRMATION: 'Menunggu konfirmasi registrasi',
            REGISTRATION_CANCELLED: 'Registrasi dibatalkan',
        },
    },
    ar: {
        references: 'المراجع',
        selectTrack: 'اختر المسار',
        tracksOptionalEmpty:
            'لا توجد مسارات نشطة لهذه الدورة. يمكنك المتابعة دون مسار.',
        tracksRequiredEmpty:
            'تتطلب هذه الدورة مسارًا ولكن لا توجد مسارات نشطة. يجب على المسؤول إعداد مسار نشط قبل الإرسال.',
        readyToSubmit: 'جاهز للإرسال',
        needsCorrection: 'يرجى تصحيح العناصر التالية قبل الإرسال.',
        fixStep: 'انتقل إلى {step}',
        adminConfiguration:
            'يجب على المسؤول تصحيح إعدادات الدورة قبل الإرسال. يرجى التواصل مع فريق المؤتمر.',
        titleRequired: 'أدخل عنوان الملخص.',
        abstractRequired: 'أدخل نص الملخص.',
        keywordsBelow:
            'عدد الكلمات المفتاحية {actual}. أضف {remaining} على الأقل (المطلوب {min}–{max}).',
        keywordsAbove:
            'عدد الكلمات المفتاحية {actual}. احذف {excess} على الأقل (المطلوب {min}–{max}).',
        keywordsInvalid:
            'تحقق من الكلمات المفتاحية والتكرار والترتيب في التفاصيل.',
        trackInvalid: 'اختر مسارًا نشطًا صالحًا لهذه الدورة.',
        authorsRequired: 'أضف مساهمًا واحدًا على الأقل.',
        correspondingRequired: 'حدد مؤلفًا مراسلًا واحدًا فقط.',
        orcidInvalid: 'صحح معرّف ORCID المدخل أو اتركه فارغًا. المعرّف اختياري.',
        affiliationInvalid:
            'تحقق من انتماءات كل مؤلف وبلده وترتيب الانتماءات. معرّف ROR اختياري.',
        emailInvalid: 'أدخل بريدًا إلكترونيًا صالحًا لكل مؤلف.',
        nameInvalid: 'أكمل اسم كل مؤلف.',
        countryInvalid: 'أدخل رمز بلد صالحًا من حرفين لكل مؤلف.',
        authorOrderInvalid: 'تحقق من ترتيب المساهمين واحفظه مجددًا.',
        fileRequired: 'ارفع ملف ملخص صالحًا وفق متطلبات الدورة.',
        reviewRequired: 'تحقق من المعلومات المطلوبة واحفظها قبل الإرسال.',
        details: 'التفاصيل',
        files: 'رفع الملفات',
        contributors: 'المساهمون',
        review: 'المراجعة والإرسال',
        steps: 'خطوات إرسال الملخص',
        progress: 'الخطوة {current} من {total}',
        intro: 'جهّز ملخصك في خمس خطوات واحفظ المسودة في أي وقت. يدفع مقدم البحث بعد القبول الأكاديمي.',
        previous: 'السابق',
        continue: 'متابعة',
        saveDraft: 'حفظ المسودة',
        saving: 'جارٍ الحفظ…',
        saved: 'تم حفظ المسودة ولم يتم إرسال الملخص رسميًا.',
        edit: 'تعديل',
        cancel: 'إلغاء',
        moveUp: 'نقل المؤلف للأعلى',
        moveDown: 'نقل المؤلف للأسفل',
        keywords: 'الكلمات المفتاحية',
        removeKeyword: 'إزالة {keyword}',
        keywordHint: 'اكتب كلمة مفتاحية ثم اضغط إدخال أو فاصلة',
        keywordCount: '{count} كلمات مفتاحية',
        keywordRange: 'سياسة الدورة: {min}–{max} كلمات مفتاحية',
        editorTools: 'أدوات الملخص',
        plainText:
            'تُحفظ البيانات الأكاديمية كنص عادي. تنسيق الخط العريض والمائل غير متاح. حدد الأرقام لتحويلها إلى مرتفعة أو منخفضة؛ تُدرج الروابط كنص.',
        superscript: 'رفع الأرقام المحددة',
        subscript: 'خفض الأرقام المحددة',
        insertLink: 'إدراج رابط',
        invalidLink: 'استخدم رابط HTTP أو HTTPS صالحًا.',
        wordCount: '{count} كلمة',
        bulkReferences: 'قائمة المراجع',
        oneReferencePerLine:
            'مرجع واحد في كل سطر. الصق المراجع هنا. تُحفظ بيانات DOI والرابط للمراجع غير المعدلة.',
        individualReferences: 'التبديل إلى تحرير المرجع وDOI والرابط بشكل فردي',
        confirmReferenceRemoval: 'تأكيد إزالة المراجع',
        referenceRemovalHelp:
            'لم تعد المراجع المحفوظة التالية موجودة في القائمة. أكد فقط إذا كنت تريد حذفها.',
        confirmChanges: 'تأكيد التغييرات',
        READY: 'جاهز',
        WARNING: 'تنبيه — معلومات اختيارية',
        BLOCKED: 'محظور — يلزم التصحيح',
        paidAfterAccept: 'باقة مدفوعة — الدفع بعد قبول الملخص',
        awaitingPayment:
            'تم قبول الملخص. ستظهر تعليمات الدفع عند توفر الالتزام المالي.',
        paidNoObligation: 'باقة مدفوعة — لا يوجد التزام دفع متاح حاليًا.',
        actions: {
            SUBMIT_ABSTRACT: 'بدء الملخص',
            CONTINUE_ABSTRACT: 'متابعة المسودة',
            WAIT_ACADEMIC_REVIEW:
                'تم إرسال الملخص — بانتظار المراجعة الأكاديمية',
            VIEW_ACADEMIC_STATUS: 'عرض القرار الأكاديمي',
            WAIT_PAYMENT_SETUP: 'مقبول — بانتظار تعليمات الدفع',
            WAIT_REGISTRATION_CONFIRMATION: 'بانتظار تأكيد التسجيل',
            REGISTRATION_CANCELLED: 'تم إلغاء التسجيل',
        },
    },
};
