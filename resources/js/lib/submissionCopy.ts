import type { AppLocale } from '@/types';

const copy = {
    id: {
        nav: 'Abstrak Saya',
        index: 'Abstrak Saya',
        indexHint: 'Kelola draft dan kiriman abstrak untuk konferensi Anda.',
        back: 'Kembali ke dashboard',
        noSubmissions: 'Belum ada abstrak',
        noSubmissionsHelp:
            'Pilih registrasi dan bahasa abstrak untuk mulai membuat draft.',
        noRegistrations: 'Belum ada registrasi yang dapat digunakan.',
        noRegistrationsHelp:
            'Lakukan registrasi pada edisi konferensi terlebih dahulu. Pembayaran presenter tidak diperlukan untuk membuat draft abstrak.',
        editionLink: 'Lihat registrasi edisi',
        chooseRegistration: 'Registrasi konferensi',
        chooseLocale: 'Bahasa utama abstrak',
        create: 'Buat draft abstrak',
        open: 'Buka',
        paper: 'Kode paper',
        status: 'Status',
        draft: 'Draft',
        submitted: 'Terkirim',
        details: 'Detail abstrak',
        step: 'Langkah 1 dari 5 ┬╖ Detail',
        detailsHelp:
            'Selesaikan lima tahapan: detail abstrak, penulis, referensi, file, dan pengiriman resmi. Pembayaran presenter dilakukan setelah abstrak diterima.',
        primaryLocale: 'Bahasa akademik utama',
        primaryLocaleHelp:
            'Dipilih saat pembuatan draft dan tidak diubah pada formulir ini.',
        track: 'Bidang / Track',
        noTrack: 'Tanpa bidang',
        title: 'Judul',
        subtitle: 'Subjudul (opsional)',
        abstract: 'Isi abstrak',
        keywords: 'Kata kunci',
        keywordsHelp:
            'Satu kata kunci per baris. Ketentuan ICHES 2027: 3ΓÇô5 kata kunci sebelum submit resmi.',
        save: 'Simpan detail draft',
        savedHint: 'Penyimpanan draft tidak mengirim abstrak secara resmi.',
        locked: 'Abstrak ini sudah dikirim atau tidak lagi berstatus DRAFT. Detail tidak dapat diedit.',
        invalidRegistration:
            'Registrasi tidak dapat digunakan untuk membuat draft.',
        invalidDetails:
            'Detail draft tidak dapat disimpan. Periksa bidang dan statusnya.',
        nextSoon: 'Langkah 2 ┬╖ Penulis akan dikerjakan pada batch berikutnya.',
    },
    en: {
        nav: 'My Submissions',
        index: 'My Submissions',
        indexHint: 'Manage your conference abstract drafts and submissions.',
        back: 'Back to dashboard',
        noSubmissions: 'No abstracts yet',
        noSubmissionsHelp:
            'Choose a registration and primary scholarly language to start a draft.',
        noRegistrations: 'No eligible registrations yet.',
        noRegistrationsHelp:
            'Register for a conference edition first. Presenter payment is not required to create an abstract draft.',
        editionLink: 'Open edition registration',
        chooseRegistration: 'Conference registration',
        chooseLocale: 'Primary scholarly language',
        create: 'Create abstract draft',
        open: 'Open',
        paper: 'Paper ID',
        status: 'Status',
        draft: 'Draft',
        submitted: 'Submitted',
        details: 'Abstract details',
        step: 'Step 1 of 5 ┬╖ Details',
        detailsHelp:
            'Complete all five stages: abstract details, authors, references, file and official submission. Presenter fees are due only after acceptance.',
        primaryLocale: 'Primary scholarly language',
        primaryLocaleHelp:
            'Chosen when creating the draft and not changed in this form.',
        track: 'Track',
        noTrack: 'No track',
        title: 'Title',
        subtitle: 'Subtitle (optional)',
        abstract: 'Abstract text',
        keywords: 'Keywords',
        keywordsHelp:
            'One keyword per line. ICHES 2027 requires 3ΓÇô5 keywords before official submission.',
        save: 'Save draft details',
        savedHint: 'Saving a draft does not officially submit your abstract.',
        locked: 'This abstract is already submitted or no longer DRAFT; details cannot be changed.',
        invalidRegistration:
            'This registration cannot be used to create a draft.',
        invalidDetails:
            'Draft details could not be saved. Check your fields and status.',
        nextSoon: 'Step 2 ┬╖ Contributors will be available in the next batch.',
    },
    ar: {
        nav: '┘à┘ä╪«╪╡╪º╪¬┘è',
        index: '┘à┘ä╪«╪╡╪º╪¬┘è',
        indexHint:
            '╪Ñ╪»╪º╪▒╪⌐ ┘à╪│┘ê╪»╪º╪¬ ╪º┘ä┘à┘ä╪«╪╡╪º╪¬ ╪º┘ä┘à┘é╪»┘à╪⌐ ┘ä┘ä┘à╪ñ╪¬┘à╪▒.',
        back: '╪º┘ä╪╣┘ê╪»╪⌐ ╪Ñ┘ä┘ë ┘ä┘ê╪¡╪⌐ ╪º┘ä╪¬╪¡┘â┘à',
        noSubmissions: '┘ä╪º ╪¬┘ê╪¼╪» ┘à┘ä╪«╪╡╪º╪¬ ╪¿╪╣╪»',
        noSubmissionsHelp:
            '╪º╪«╪¬╪▒ ╪º┘ä╪¬╪│╪¼┘è┘ä ┘ê┘ä╪║╪⌐ ╪º┘ä┘à┘ä╪«╪╡ ╪º┘ä╪ú╪│╪º╪│┘è╪⌐ ┘ä╪¿╪»╪í ┘à╪│┘ê╪»╪⌐.',
        noRegistrations: '┘ä╪º ┘è┘ê╪¼╪» ╪¬╪│╪¼┘è┘ä ╪╡╪º┘ä╪¡ ╪¿╪╣╪».',
        noRegistrationsHelp:
            '╪│╪¼┘ä ╪ú┘ê┘ä╪º┘ï ┘ü┘è ╪»┘ê╪▒╪⌐ ╪º┘ä┘à╪ñ╪¬┘à╪▒. ┘ä╪º ┘è╪┤╪¬╪▒╪╖ ╪»┘ü╪╣ ╪▒╪│┘ê┘à ╪º┘ä┘à┘é╪»┘à ┘ä╪Ñ┘å╪┤╪º╪í ┘à╪│┘ê╪»╪⌐ ╪º┘ä┘à┘ä╪«╪╡.',
        editionLink: '╪╣╪▒╪╢ ╪¬╪│╪¼┘è┘ä ╪º┘ä╪»┘ê╪▒╪⌐',
        chooseRegistration: '╪º┘ä╪¬╪│╪¼┘è┘ä ┘ü┘è ╪º┘ä┘à╪ñ╪¬┘à╪▒',
        chooseLocale: '┘ä╪║╪⌐ ╪º┘ä┘à┘ä╪«╪╡ ╪º┘ä╪╣┘ä┘à┘è╪⌐ ╪º┘ä╪ú╪│╪º╪│┘è╪⌐',
        create: '╪Ñ┘å╪┤╪º╪í ┘à╪│┘ê╪»╪⌐ ┘à┘ä╪«╪╡',
        open: '┘ü╪¬╪¡',
        paper: '╪▒┘à╪▓ ╪º┘ä┘ê╪▒┘é╪⌐',
        status: '╪º┘ä╪¡╪º┘ä╪⌐',
        draft: '┘à╪│┘ê╪»╪⌐',
        submitted: '╪¬┘à ╪º┘ä╪Ñ╪▒╪│╪º┘ä',
        details: '╪¬┘ü╪º╪╡┘è┘ä ╪º┘ä┘à┘ä╪«╪╡',
        step: '╪º┘ä╪«╪╖┘ê╪⌐ ┘í ┘à┘å ┘Ñ ┬╖ ╪º┘ä╪¬┘ü╪º╪╡┘è┘ä',
        detailsHelp:
            'أكمل المراحل الخمس: تفاصيل الملخص والمؤلفين والمراجع والملف والإرسال الرسمي. رسوم العرض تُدفع بعد القبول فقط.',
        primaryLocale: '╪º┘ä┘ä╪║╪⌐ ╪º┘ä╪╣┘ä┘à┘è╪⌐ ╪º┘ä╪ú╪│╪º╪│┘è╪⌐',
        primaryLocaleHelp:
            '╪¬╪¡╪»╪» ╪╣┘å╪» ╪Ñ┘å╪┤╪º╪í ╪º┘ä┘à╪│┘ê╪»╪⌐ ┘ê┘ä╪º ╪¬╪¬╪║┘è╪▒ ┘ü┘è ┘ç╪░╪º ╪º┘ä┘å┘à┘ê╪░╪¼.',
        track: '╪º┘ä┘à╪│╪º╪▒ ╪º┘ä╪╣┘ä┘à┘è',
        noTrack: '╪¿╪»┘ê┘å ┘à╪│╪º╪▒',
        title: '╪º┘ä╪╣┘å┘ê╪º┘å',
        subtitle: '╪º┘ä╪╣┘å┘ê╪º┘å ╪º┘ä┘ü╪▒╪╣┘è (╪º╪«╪¬┘è╪º╪▒┘è)',
        abstract: '┘å╪╡ ╪º┘ä┘à┘ä╪«╪╡',
        keywords: '╪º┘ä┘â┘ä┘à╪º╪¬ ╪º┘ä┘à┘ü╪¬╪º╪¡┘è╪⌐',
        keywordsHelp:
            '┘â┘ä┘à╪⌐ ┘à┘ü╪¬╪º╪¡┘è╪⌐ ┘ê╪º╪¡╪»╪⌐ ┘ü┘è ┘â┘ä ╪│╪╖╪▒. ┘è╪¬╪╖┘ä╪¿ ICHES 2027 ┘à┘å ┘ú ╪Ñ┘ä┘ë ┘Ñ ┘â┘ä┘à╪º╪¬ ┘é╪¿┘ä ╪º┘ä╪Ñ╪▒╪│╪º┘ä ╪º┘ä╪▒╪│┘à┘è.',
        save: '╪¡┘ü╪╕ ╪¬┘ü╪º╪╡┘è┘ä ╪º┘ä┘à╪│┘ê╪»╪⌐',
        savedHint:
            '╪¡┘ü╪╕ ╪º┘ä┘à╪│┘ê╪»╪⌐ ┘ä╪º ┘è╪╣┘å┘è ╪Ñ╪▒╪│╪º┘ä ╪º┘ä┘à┘ä╪«╪╡ ╪▒╪│┘à┘è╪º┘ï.',
        locked: '╪¬┘à ╪Ñ╪▒╪│╪º┘ä ┘ç╪░╪º ╪º┘ä┘à┘ä╪«╪╡ ╪ú┘ê ┘ä┘à ┘è╪╣╪» ┘à╪│┘ê╪»╪⌐╪¢ ┘ä╪º ┘è┘à┘â┘å ╪¬╪╣╪»┘è┘ä ╪º┘ä╪¬┘ü╪º╪╡┘è┘ä.',
        invalidRegistration:
            '┘ä╪º ┘è┘à┘â┘å ╪º╪│╪¬╪«╪»╪º┘à ┘ç╪░╪º ╪º┘ä╪¬╪│╪¼┘è┘ä ┘ä╪Ñ┘å╪┤╪º╪í ┘à╪│┘ê╪»╪⌐.',
        invalidDetails:
            '╪¬╪╣╪░╪▒ ╪¡┘ü╪╕ ╪º┘ä╪¬┘ü╪º╪╡┘è┘ä. ╪¬╪¡┘é┘é ┘à┘å ╪º┘ä╪¡┘é┘ê┘ä ┘ê╪¡╪º┘ä╪⌐ ╪º┘ä┘à╪│┘ê╪»╪⌐.',
        nextSoon:
            '╪º┘ä╪«╪╖┘ê╪⌐ ┘ó ┬╖ ╪│╪¬╪¬╪º╪¡ ╪¿┘è╪º┘å╪º╪¬ ╪º┘ä┘à╪ñ┘ä┘ü┘è┘å ┘ü┘è ╪º┘ä╪»┘ü╪╣╪⌐ ╪º┘ä╪¬╪º┘ä┘è╪⌐.',
    },
} as const;

export function submissionCopy(locale: string) {
    const normalized = locale.toLowerCase().split('-')[0] as AppLocale;
    return copy[normalized] ?? copy.en;
}
