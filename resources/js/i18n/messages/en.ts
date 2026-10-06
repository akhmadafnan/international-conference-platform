export default {
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
