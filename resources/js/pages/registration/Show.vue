<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    PhCheckCircle as CheckCircle2,
    PhCopy as Copy,
    PhCreditCard as CreditCard,
    PhUploadSimple as FileUp,
    PhTicket as Ticket,
} from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useI18n } from 'vue-i18n';

type LocalizedText = Record<string, string> | null;

type ParticipationPackage = {
    id: string;
    code: string;
    name: LocalizedText;
    description: LocalizedText;
    billingMode: 'FREE' | 'PAID';
    price: string;
    currencyCode: string;
};

type Payment = {
    id: string;
    status: string;
    expectedAmount: string;
    currencyCode: string;
    submittedAmount: string | null;
    senderName: string | null;
    transferDate: string | null;
    correctionReason: string | null;
    destination: Record<string, unknown>;
    proofUploadUrl: string;
};

type EventPass = {
    id: string;
    issuedAt: string | null;
    publicCode: string | null;
    showUrl: string;
    qrUrl: string;
};

type Registration = {
    id: string;
    code: string;
    status: string;
    packageName: string;
    billingMode: 'FREE' | 'PAID';
    feeAmount: string;
    currencyCode: string;
    participantCategory: string | null;
    confirmedAt: string | null;
    complimentary: boolean;
    nextAction: string;
    nextActionUrl: string | null;
    billingState: string;
    payment: Payment | null;
    eventPass: EventPass | null;
    registrationUrl: string;
};

const props = defineProps<{
    edition: {
        id: string;
        code: string;
        year: number;
        seriesName: string | null;
        theme: LocalizedText;
    };
    packages: ParticipationPackage[];
    registration: Registration | null;
    routes: {
        store: string;
        dashboard: string;
    };
}>();

const { t, locale, te } = useI18n();
const actionLabel = (key: string) =>
    te(`wizardUx.actions.${key}`)
        ? t(`wizardUx.actions.${key}`)
        : t(`phase02.nextAction.${key}`);

const registrationForm = useForm({
    package_id: '',
    participant_category: '',
});

const proofForm = useForm({
    proof: null as File | null,
    submitted_amount: '',
    sender_name: '',
    transfer_date: '',
});

const accountCopied = ref(false);

const destination = computed(
    () => props.registration?.payment?.destination ?? {},
);

const paymentInstructions = computed(() =>
    localizedUnknown(destination.value.instructions_i18n),
);

function localized(value: LocalizedText): string {
    if (!value) {
        return '';
    }

    return (
        value[String(locale.value)] ??
        value.en ??
        value.id ??
        value.ar ??
        Object.values(value)[0] ??
        ''
    );
}

function localizedUnknown(value: unknown): string {
    if (!value || typeof value !== 'object' || Array.isArray(value)) {
        return '';
    }

    const entries = Object.entries(value).filter(
        (entry): entry is [string, string] => typeof entry[1] === 'string',
    );

    return localized(Object.fromEntries(entries));
}

function registrationError(error: string | undefined): string {
    switch (error) {
        case 'PACKAGE_INVALID':
            return t('phase02.registration.invalidPackage');
        case 'PACKAGE_UNAVAILABLE':
        case 'REGISTRATION_UNAVAILABLE':
            return t('phase02.registration.unavailable');
        case 'ALREADY_REGISTERED':
            return t('phase02.registration.alreadyRegistered');
        default:
            return error ?? '';
    }
}

function proofError(error: string | undefined): string {
    switch (error) {
        case 'PROOF_INVALID':
            return t('phase02.payment.proofInvalid');
        case 'AMOUNT_INVALID':
            return t('phase02.payment.amountInvalid');
        case 'SENDER_INVALID':
            return t('phase02.payment.senderInvalid');
        case 'TRANSFER_DATE_INVALID':
            return t('phase02.payment.transferDateInvalid');
        default:
            return error ?? '';
    }
}

function money(amount: string, currency: string): string {
    return new Intl.NumberFormat(String(locale.value), {
        style: 'currency',
        currency,
        maximumFractionDigits: 2,
    }).format(Number(amount));
}

function choosePackage(packageItem: ParticipationPackage): void {
    registrationForm.package_id = packageItem.id;
    registrationForm.post(props.routes.store, {
        preserveScroll: true,
    });
}

function onProofChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    proofForm.proof = input.files?.[0] ?? null;
}

function submitProof(): void {
    const payment = props.registration?.payment;

    if (!payment) {
        return;
    }

    proofForm.post(payment.proofUploadUrl, {
        forceFormData: true,
        preserveScroll: true,
    });
}

async function copyAccount(): Promise<void> {
    const account = destination.value.account_number;

    if (typeof account === 'string' && navigator.clipboard) {
        await navigator.clipboard.writeText(account);
        accountCopied.value = true;
    }
}
</script>

<template>
    <Head :title="t('phase02.registration.title')" />

    <div
        class="flex w-full min-w-0 flex-col gap-6 p-4 [overflow-wrap:anywhere] md:p-8"
    >
        <header class="flex flex-col gap-2">
            <Link
                :href="routes.dashboard"
                class="text-sm font-medium text-muted-foreground hover:text-foreground"
            >
                {{ t('phase02.registration.backToDashboard') }}
            </Link>
            <div>
                <p class="text-sm font-medium text-muted-foreground">
                    {{ edition.code }} · {{ edition.year }}
                </p>
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ t('phase02.registration.title') }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ edition.seriesName }}
                </p>
            </div>
        </header>

        <Card
            v-if="!registration"
            class="grid min-w-0 gap-4 md:grid-cols-2"
            aria-labelledby="package-heading"
        >
            <div class="min-w-0 md:col-span-2">
                <h2 id="package-heading" class="text-lg font-semibold">
                    {{ t('phase02.registration.choosePackage') }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    {{ t('phase02.registration.choosePackageHelp') }}
                </p>
            </div>

            <article
                v-for="packageItem in packages"
                :key="packageItem.id"
                class="flex min-w-0 flex-col justify-between gap-5 p-4"
            >
                <div class="min-w-0 space-y-2">
                    <div class="flex min-w-0 items-start justify-between gap-3">
                        <h3 class="font-semibold">
                            {{ localized(packageItem.name) }}
                        </h3>
                        <span
                            class="rounded-full border px-2.5 py-1 text-xs font-semibold"
                        >
                            {{
                                packageItem.billingMode === 'FREE'
                                    ? t('phase02.billing.free')
                                    : money(
                                          packageItem.price,
                                          packageItem.currencyCode,
                                      )
                            }}
                        </span>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        {{ localized(packageItem.description) }}
                    </p>
                </div>

                <Button
                    type="button"
                    class="inline-flex h-auto min-h-10 max-w-full min-w-0 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold whitespace-normal text-primary-foreground disabled:opacity-50"
                    :disabled="registrationForm.processing"
                    @click="choosePackage(packageItem)"
                >
                    {{ t('phase02.registration.selectPackage') }}
                </Button>
            </article>

            <p
                v-if="packages.length === 0"
                class="rounded-lg border border-dashed p-5 text-sm text-muted-foreground md:col-span-2"
            >
                {{ t('phase02.registration.noPackages') }}
            </p>

            <p
                v-if="registrationForm.errors.package_id"
                class="text-sm text-destructive md:col-span-2"
            >
                {{ registrationError(registrationForm.errors.package_id) }}
            </p>
        </Card>

        <template v-else>
            <Card class="min-w-0 p-4">
                <div
                    class="flex min-w-0 flex-col gap-4 md:flex-row md:items-start md:justify-between"
                >
                    <div>
                        <p class="text-sm text-muted-foreground">
                            {{ t('phase02.registration.registrationId') }}
                        </p>
                        <p class="font-mono text-lg font-semibold">
                            {{ registration.code }}
                        </p>
                        <p class="mt-2 text-sm">
                            {{ registration.packageName }}
                        </p>
                    </div>

                    <span
                        class="inline-flex w-fit items-center gap-2 rounded-full border px-3 py-1.5 text-sm font-medium"
                    >
                        <CheckCircle2
                            v-if="registration.status === 'CONFIRMED'"
                            class="size-4"
                        />
                        {{
                            t(
                                `phase02.registrationStatus.${registration.status}`,
                            )
                        }}
                    </span>
                </div>
            </Card>

            <Card class="min-w-0 p-4">
                <p
                    class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                >
                    {{ t('phase02.nextAction.label') }}
                </p>
                <h2 class="mt-2 text-xl font-semibold">
                    {{ actionLabel(registration.nextAction) }}
                </h2>

                <Link
                    v-if="registration.eventPass"
                    :href="registration.eventPass.showUrl"
                    class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground"
                >
                    <Ticket class="size-4" />
                    {{ t('phase02.eventPass.open') }}
                </Link>
            </Card>

            <Card v-if="registration.payment" class="min-w-0 p-4">
                <div class="flex min-w-0 items-center gap-2">
                    <CreditCard class="size-5" />
                    <h2 class="text-lg font-semibold">
                        {{ t('phase02.payment.title') }}
                    </h2>
                </div>

                <div class="mt-4 grid min-w-0 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('phase02.payment.amount') }}
                        </p>
                        <p class="font-semibold">
                            {{
                                money(
                                    registration.payment.expectedAmount,
                                    registration.payment.currencyCode,
                                )
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('phase02.payment.status') }}
                        </p>
                        <p class="font-semibold">
                            {{
                                t(
                                    `phase02.paymentStatus.${registration.payment.status}`,
                                )
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('phase02.payment.bank') }}
                        </p>
                        <p class="font-medium">
                            {{ destination.bank_name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('phase02.payment.accountHolder') }}
                        </p>
                        <p class="font-medium">
                            {{ destination.account_holder }}
                        </p>
                    </div>
                    <div class="min-w-0 sm:col-span-2">
                        <p class="text-xs text-muted-foreground">
                            {{ t('phase02.payment.accountNumber') }}
                        </p>
                        <div
                            class="mt-1 flex min-w-0 flex-wrap items-center gap-2"
                        >
                            <code
                                dir="ltr"
                                class="rounded bg-muted px-2 py-1 text-base"
                            >
                                {{ destination.account_number }}
                            </code>
                            <Button
                                type="button"
                                class="inline-flex h-auto min-h-10 max-w-full min-w-0 items-center gap-2 rounded-md border px-3 text-sm font-medium whitespace-normal"
                                @click="copyAccount"
                            >
                                <Copy class="size-4" />
                                {{
                                    accountCopied
                                        ? t('phase02.payment.copied')
                                        : t('phase02.payment.copy')
                                }}
                            </Button>
                            <span class="sr-only" aria-live="polite">
                                {{
                                    accountCopied
                                        ? t('phase02.payment.copied')
                                        : ''
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <div
                    v-if="paymentInstructions"
                    class="mt-5 min-w-0 rounded-lg border bg-muted/30 p-4"
                >
                    <p class="text-sm font-semibold">
                        {{ t('phase02.payment.instructions') }}
                    </p>
                    <p
                        class="mt-1 text-sm whitespace-pre-line text-muted-foreground"
                    >
                        {{ paymentInstructions }}
                    </p>
                </div>

                <div
                    v-if="
                        registration.payment.status === 'CORRECTION_REQUIRED' &&
                        registration.payment.correctionReason
                    "
                    class="mt-5 min-w-0 rounded-lg border border-destructive/30 bg-destructive/5 p-4"
                >
                    <p class="text-sm font-semibold text-destructive">
                        {{ t('phase02.payment.correctionRequired') }}
                    </p>
                    <p class="mt-1 text-sm">
                        {{ registration.payment.correctionReason }}
                    </p>
                </div>

                <form
                    v-if="
                        registration.payment.status === 'PENDING' ||
                        registration.payment.status === 'CORRECTION_REQUIRED'
                    "
                    class="mt-6 min-w-0 space-y-4"
                    @submit.prevent="submitProof"
                >
                    <div>
                        <label class="text-sm font-medium" for="proof">
                            {{ t('phase02.payment.proof') }}
                            <span class="text-muted-foreground">
                                ({{ t('phase02.form.required') }})
                            </span>
                        </label>
                        <Input
                            id="proof"
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="mt-1 block w-full rounded-md border bg-background p-2 text-sm"
                            required
                            :aria-invalid="Boolean(proofForm.errors.proof)"
                            :aria-describedby="
                                proofForm.errors.proof
                                    ? 'proof-error'
                                    : undefined
                            "
                            @change="onProofChange"
                        />
                        <p
                            v-if="proofForm.errors.proof"
                            id="proof-error"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ proofError(proofForm.errors.proof) }}
                        </p>
                    </div>

                    <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="text-sm font-medium"
                                for="submitted-amount"
                            >
                                {{ t('phase02.payment.transferredAmount') }}
                                <span class="text-muted-foreground">
                                    ({{ t('phase02.form.required') }})
                                </span>
                            </label>
                            <Input
                                id="submitted-amount"
                                v-model="proofForm.submitted_amount"
                                type="number"
                                min="1"
                                step="0.01"
                                class="mt-1 min-h-10 w-full rounded-md border bg-background px-3"
                                required
                                :aria-invalid="
                                    Boolean(proofForm.errors.submitted_amount)
                                "
                                :aria-describedby="
                                    proofForm.errors.submitted_amount
                                        ? 'submitted-amount-error'
                                        : undefined
                                "
                            />
                            <p
                                v-if="proofForm.errors.submitted_amount"
                                id="submitted-amount-error"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{
                                    proofError(
                                        proofForm.errors.submitted_amount,
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-sm font-medium"
                                for="sender-name"
                            >
                                {{ t('phase02.payment.senderName') }}
                                <span class="text-muted-foreground">
                                    ({{ t('phase02.form.optional') }})
                                </span>
                            </label>
                            <Input
                                id="sender-name"
                                v-model="proofForm.sender_name"
                                type="text"
                                class="mt-1 min-h-10 w-full rounded-md border bg-background px-3"
                                :aria-invalid="
                                    Boolean(proofForm.errors.sender_name)
                                "
                                :aria-describedby="
                                    proofForm.errors.sender_name
                                        ? 'sender-name-error'
                                        : undefined
                                "
                            />
                            <p
                                v-if="proofForm.errors.sender_name"
                                id="sender-name-error"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ proofError(proofForm.errors.sender_name) }}
                            </p>
                        </div>
                        <div>
                            <label
                                class="text-sm font-medium"
                                for="transfer-date"
                            >
                                {{ t('phase02.payment.transferDate') }}
                                <span class="text-muted-foreground">
                                    ({{ t('phase02.form.optional') }})
                                </span>
                            </label>
                            <Input
                                id="transfer-date"
                                v-model="proofForm.transfer_date"
                                type="date"
                                class="mt-1 min-h-10 w-full rounded-md border bg-background px-3"
                                :aria-invalid="
                                    Boolean(proofForm.errors.transfer_date)
                                "
                                :aria-describedby="
                                    proofForm.errors.transfer_date
                                        ? 'transfer-date-error'
                                        : undefined
                                "
                            />
                            <p
                                v-if="proofForm.errors.transfer_date"
                                id="transfer-date-error"
                                class="mt-1 text-sm text-destructive"
                            >
                                {{ proofError(proofForm.errors.transfer_date) }}
                            </p>
                        </div>
                    </div>

                    <Button
                        type="submit"
                        class="inline-flex h-auto min-h-10 max-w-full min-w-0 items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold whitespace-normal text-primary-foreground disabled:opacity-50"
                        :disabled="proofForm.processing || !proofForm.proof"
                    >
                        <FileUp class="size-4" />
                        {{
                            registration.payment.status ===
                            'CORRECTION_REQUIRED'
                                ? t('phase02.payment.replaceProof')
                                : t('phase02.payment.submitProof')
                        }}
                    </Button>
                </form>
            </Card>

            <Card v-else class="min-w-0 p-4">
                <p
                    v-if="
                        registration.billingMode === 'PAID' &&
                        !registration.complimentary
                    "
                    class="mb-2 font-medium"
                >
                    {{
                        money(registration.feeAmount, registration.currencyCode)
                    }}
                </p>
                <p class="text-sm font-semibold">
                    {{
                        registration.complimentary
                            ? t('phase02.billing.complimentary')
                            : registration.billingMode === 'FREE'
                              ? t('phase02.billing.free')
                              : registration.billingState === 'AFTER_ACCEPT'
                                ? t('wizardUx.paidAfterAccept')
                                : t('wizardUx.paidNoObligation')
                    }}
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{
                        registration.complimentary ||
                        registration.billingMode === 'FREE'
                            ? t('phase02.billing.noPaymentRequired')
                            : registration.billingState === 'AFTER_ACCEPT'
                              ? t('wizardUx.intro')
                              : registration.billingState ===
                                  'AWAITING_PAYMENT_SETUP'
                                ? t('wizardUx.awaitingPayment')
                                : t('wizardUx.paidNoObligation')
                    }}
                </p>
            </Card>
            <div
                v-if="registration.nextActionUrl"
                class="flex min-w-0 flex-wrap gap-3"
            >
                <Button
                    class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                    as-child
                    ><Link :href="registration.nextActionUrl">{{
                        actionLabel(registration.nextAction)
                    }}</Link></Button
                >
            </div>
        </template>
    </div>
</template>
