export default {
    dataTable: {
        showing: 'Showing {from}–{to} of {total} entries',
        pagination: 'Table pagination',
        first: 'First',
        previous: 'Previous',
        next: 'Next',
        last: 'Last',
        page: 'Page {page}',
        ellipsis: 'More pages',
    },
    admin: {
        deletion: {
            actions: 'Actions',
            delete: 'Delete',
            cancel: 'Cancel',
            deleteIdentity: 'Delete {code}',
            confirm: 'Delete permanently',
            deleting: 'Deleting…',
            seriesTitle: 'Delete Conference Series',
            editionTitle: 'Delete Conference Edition',
            seriesDescription:
                'This permanently deletes the series and cannot be undone. Only a series without editions can be deleted.',
            editionDescription:
                'This permanently deletes the edition and cannot be undone. Only a draft edition without related data can be deleted.',
            confirmLabel: 'Confirm the code',
            confirmHelp:
                'Type {code} exactly, including letter case, to confirm.',
            seriesDeleted: 'Conference Series deleted successfully.',
            editionDeleted: 'Conference Edition deleted successfully.',
            errors: {
                confirmation:
                    'The code must match exactly. Check letter case and extra spaces.',
                seriesHasEditions:
                    'This series has conference editions and cannot be deleted.',
                editionNotDraft:
                    'Only an edition that is still a draft can be deleted.',
                editionHasData:
                    'This edition has related data and cannot be deleted. Its records have been preserved.',
                failed: 'Deletion could not be completed. No changes were saved. Please try again.',
            },
        },
        editions: {
            title: 'Conference Editions',
            description:
                'Manage conference editions, hosts, dates, and event modes.',
            create: 'Create Conference Edition',
            createDescription:
                'Create a draft edition. Registration and publication remain closed.',
            save: 'Create edition',
            created: 'Conference edition created successfully as a draft.',
            series: 'Conference Series',
            selectSeries: 'Select an active series',
            noSeries:
                'Create an active Conference Series before creating an Edition.',
            codeHelp:
                'Edition Code identifies this edition independently of Series Code. Letters are saved in uppercase.',
            timezoneHelp:
                'Use an IANA timezone, for example Asia/Jakarta. Start and end times are entered in this timezone.',
            identity: 'Edition / Host',
            year: 'Year',
            mode: 'Mode',
            dates: 'Event dates',
            status: 'Status',
            number: 'Edition {number}',
            emptyTitle: 'Your next conference starts here',
            emptyDescription:
                'Create a draft edition to define its host, event dates, and conference identity.',
            noPageResults:
                'There are no editions on this page. Use pagination to return to an available page.',
            unknownMode: 'Mode unavailable',
            modes: { OFFLINE: 'In person', ONLINE: 'Online', HYBRID: 'Hybrid' },
            fields: {
                edition_code: 'Edition Code',
                edition_number: 'Edition Number',
                year: 'Year',
                host_name: 'Host Name',
                timezone: 'Timezone',
                starts_at: 'Start Date / Time',
                ends_at: 'End Date / Time',
                theme_i18n: 'Theme',
                organizer_i18n: 'Organizer',
            },
            errors: {
                required: 'This field is required.',
                text: 'Enter text in this field.',
                tooLong: 'The value is too long.',
                integer: 'Enter a whole number.',
                range: 'Enter a number within the allowed range.',
                series: 'Choose an active Conference Series.',
                duplicate: 'This edition code is already in use.',
                mode: 'Choose an available event mode.',
                timezone: 'Enter a valid IANA timezone.',
                date: 'Enter a valid local date and time in the selected timezone.',
                dateRange: 'End must be later than start.',
                languages:
                    'Only Indonesian, English, and Arabic are supported.',
                unexpected: 'This field cannot be supplied.',
                invalid: 'Check the value in this field.',
                summary: 'Check the highlighted fields before saving.',
            },
        },
        series: {
            title: 'Conference Series',
            description: 'Manage the identity of recurring conference series.',
            create: 'Create Conference Series',
            createDescription:
                'Enter the series identity. New series are active by default.',
            code: 'Code',
            codeHelp:
                'Leading and trailing spaces are removed; letters are saved in uppercase.',
            name: 'Name',
            acronym: 'Acronym',
            about: 'Description',
            optional: 'optional',
            status: 'Status',
            notProvided: 'Not provided',
            emptyTitle: 'No conference series yet',
            emptyDescription:
                'Create the first conference series using the button above.',
            save: 'Create series',
            saving: 'Saving…',
            cancel: 'Cancel',
            created: 'Conference series created successfully.',
            pagination: 'Conference series pages',
            previous: 'Previous',
            next: 'Next',
            page: 'Page {current} of {last}',
            languages: { id: 'Indonesian', en: 'English', ar: 'Arabic' },
            statuses: {
                ACTIVE: 'Active',
                INACTIVE: 'Inactive',
                ARCHIVED: 'Archived',
                unknown: 'Status unavailable',
            },
            errors: {
                required: 'This field is required.',
                text: 'Enter text in this field.',
                shortText: 'Use no more than 255 characters.',
                longText: 'Use no more than 10,000 characters.',
                duplicate: 'This code is already in use.',
                languages:
                    'Descriptions support Indonesian, English, and Arabic only.',
                invalid: 'Check the value in this field.',
                summary: 'Check the highlighted fields before saving.',
            },
        },
        dashboard: {
            statistics: 'Conference statistics',
            seriesCount: 'Total conference series',
            manageSeries: 'View series',
            manageEditions: 'View editions',
            quickActions: 'Quick actions',
            quickDescription: 'Set up the foundations of your next conference.',
            seriesActionHelp: 'Define the identity of a recurring conference.',
            editionActionHelp: 'Add a draft edition to an active series.',
            title: 'Administration overview',
            eyebrow: 'ICHES Superadmin workspace',
            description:
                'Global overview of conference editions across all series.',
            editionCount: 'Total editions',
            recentEditions: 'Latest editions',
            recentDescription:
                'Up to 10 editions, ordered by creation date, newest first.',
            emptyTitle: 'No conference editions yet',
            emptyDescription:
                'Conference editions will appear here once they are available.',
            seriesUnavailable: 'Series unavailable',
            year: 'Year',
            status: 'Status',
            statuses: {
                DRAFT: 'Draft',
                ACTIVE: 'Active',
                PUBLISHED: 'Published',
                COMPLETED: 'Completed',
                CANCELLED: 'Cancelled',
                ARCHIVED: 'Archived',
                unknown: 'Status unavailable',
            },
        },
    },
    locale: {
        label: 'Language',
    },
    navigation: {
        platform: 'Platform',
        dashboard: 'Dashboard',
        repository: 'Repository',
        documentation: 'Documentation',
    },
    account: {
        settings: 'Settings',
        logout: 'Log out',
    },
    phase02: {
        dashboard: {
            eyebrow: 'ICHES participant workspace',
            title: 'Registration overview',
            description:
                'Your registration, payment, and Event Pass status in one place.',
            continue: 'Continue registration',
            registration: 'Registration',
            eventPassReady: 'Your Event Pass is ready to use.',
            eventPassPending:
                'Your Event Pass will appear after registration is confirmed.',
            noRegistration: 'No conference registration yet',
            noRegistrationHelp:
                'Choose a participation package from the active Conference Edition registration page.',
        },
        registration: {
            title: 'Conference registration',
            backToDashboard: 'Back to dashboard',
            choosePackage: 'Choose your participation package',
            choosePackageHelp:
                'Select one active package to start your registration.',
            selectPackage: 'Select package',
            registrationId: 'Registration ID',
            noPackages: 'No participation package is currently available.',
            unavailable:
                'Registration is not available for this package right now.',
            alreadyRegistered:
                'You already have a registration for this Conference Edition.',
            invalidPackage: 'Choose an available participation package.',
        },
        billing: {
            free: 'FREE',
            complimentary: 'COMPLIMENTARY',
            noPaymentRequired:
                'No payment or payment proof is required for this registration.',
        },
        registrationStatus: {
            PENDING: 'Registration started',
            PAYMENT_PENDING: 'Payment required',
            CONFIRMED: 'Registration confirmed',
            CANCELLED: 'Registration cancelled',
        },
        nextAction: {
            label: 'Next action',
            EVENT_PASS_AVAILABLE: 'Open your Event Pass',
            REPLACE_PAYMENT_PROOF: 'Replace your payment proof',
            WAIT_FINANCE_VERIFICATION: 'Wait for Finance verification',
            SUBMIT_PAYMENT_PROOF: 'Upload your payment proof',
            REGISTRATION_CONFIRMED: 'Registration confirmed',
            REGISTRATION_PENDING: 'Complete your registration',
        },
        form: {
            required: 'Required',
            optional: 'Optional',
        },
        payment: {
            title: 'Payment',
            amount: 'Amount due',
            status: 'Payment status',
            bank: 'Bank',
            accountHolder: 'Account holder',
            accountNumber: 'Account number',
            copy: 'Copy',
            copied: 'Copied',
            instructions: 'Payment instructions',
            correctionRequired:
                'Action required: payment proof needs correction',
            proof: 'Payment proof',
            transferredAmount: 'Transferred amount',
            senderName: 'Sender name',
            transferDate: 'Transfer date',
            replaceProof: 'Replace proof',
            submitProof: 'Submit payment proof',
            proofInvalid: 'Upload a PDF, JPG, JPEG, or PNG file up to 10 MB.',
            amountInvalid:
                'Enter a valid transferred amount greater than zero.',
            senderInvalid: 'Sender name must be 255 characters or fewer.',
            transferDateInvalid: 'Enter a valid transfer date.',
        },
        paymentStatus: {
            PENDING: 'Waiting for payment proof',
            SUBMITTED: 'Payment proof received',
            CORRECTION_REQUIRED: 'Correction required',
            VERIFIED: 'Payment verified',
            CANCELLED: 'Payment cancelled',
        },
        eventPass: {
            title: 'Event Pass',
            open: 'Open Event Pass',
            back: 'Back to dashboard',
            participant: 'Participant',
            package: 'Participation package',
            confirmed: 'REGISTRATION CONFIRMED',
            qrAlt: 'Event Pass QR code',
            lookupNote:
                'This QR contains an opaque lookup identity only; scanning it does not record attendance.',
        },
    },
};
