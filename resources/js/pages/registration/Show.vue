<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Copy, CreditCard, FileUp, Ticket } from '@lucide/vue';
import { computed } from 'vue';
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

const { t, locale } = useI18n();

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

const destination = computed(() => props.registration?.payment?.destination ?? {});

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
    }
}
</script>

<template>
    <Head :title="t('phase02.registration.title')" />

    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4 md:p-6">
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

        <section
            v-if="!registration"
            class="grid gap-4 md:grid-cols-2"
            aria-labelledby="package-heading"
        >
            <div class="md:col-span-2">
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
                class="flex flex-col justify-between gap-5 rounded-xl border bg-card p-5 shadow-sm"
            >
                <div class="space-y-2">
                    <div class="flex items-start justify-between gap-3">
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

                <button
                    type="button"
                    class="inline-flex min-h-10 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground disabled:opacity-50"
                    :disabled="registrationForm.processing"
                    @click="choosePackage(packageItem)"
                >
                    {{ t('phase02.registration.selectPackage') }}
                </button>
            </article>

            <p
                v-if="registrationForm.errors.package_id"
                class="md:col-span-2 text-sm text-destructive"
            >
                {{ registrationForm.errors.package_id }}
            </p>
        </section>

        <template v-else>
            <section class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
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
            </section>

            <section class="rounded-xl border bg-card p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                    {{ t('phase02.nextAction.label') }}
                </p>
                <h2 class="mt-2 text-xl font-semibold">
                    {{
                        t(
                            `phase02.nextAction.${registration.nextAction}`,
                        )
                    }}
                </h2>

                <Link
                    v-if="registration.eventPass"
                    :href="registration.eventPass.showUrl"
                    class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground"
                >
                    <Ticket class="size-4" />
                    {{ t('phase02.eventPass.open') }}
                </Link>
            </section>

            <section
                v-if="registration.payment"
                class="rounded-xl border bg-card p-5 shadow-sm"
            >
                <div class="flex items-center gap-2">
                    <CreditCard class="size-5" />
                    <h2 class="text-lg font-semibold">
                        {{ t('phase02.payment.title') }}
                    </h2>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
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
                    <div class="sm:col-span-2">
                        <p class="text-xs text-muted-foreground">
                            {{ t('phase02.payment.accountNumber') }}
                        </p>
                        <div class="mt-1 flex flex-wrap items-center gap-2">
                            <code class="rounded bg-muted px-2 py-1 text-base">
                                {{ destination.account_number }}
                            </code>
                            <button
                                type="button"
                                class="inline-flex min-h-9 items-center gap-2 rounded-md border px-3 text-sm font-medium"
                                @click="copyAccount"
                            >
                                <Copy class="size-4" />
                                {{ t('phase02.payment.copy') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    v-if="
                        registration.payment.status === 'CORRECTION_REQUIRED' &&
                        registration.payment.correctionReason
                    "
                    class="mt-5 rounded-lg border border-destructive/30 bg-destructive/5 p-4"
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
                    class="mt-6 space-y-4"
                    @submit.prevent="submitProof"
                >
                    <div>
                        <label class="text-sm font-medium" for="proof">
                            {{ t('phase02.payment.proof') }}
                        </label>
                        <input
                            id="proof"
                            type="file"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="mt-1 block w-full rounded-md border bg-background p-2 text-sm"
                            required
                            @change="onProofChange"
                        />
                        <p
                            v-if="proofForm.errors.proof"
                            class="mt-1 text-sm text-destructive"
                        >
                            {{ proofForm.errors.proof }}
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label
                                class="text-sm font-medium"
                                for="submitted-amount"
                            >
                                {{ t('phase02.payment.transferredAmount') }}
                            </label>
                            <input
                                id="submitted-amount"
                                v-model="proofForm.submitted_amount"
                                type="number"
                                min="1"
                                step="0.01"
                                class="mt-1 min-h-10 w-full rounded-md border bg-background px-3"
                                required
                            />
                        </div>
                        <div>
                            <label class="text-sm font-medium" for="sender-name">
                                {{ t('phase02.payment.senderName') }}
                            </label>
                            <input
                                id="sender-name"
                                v-model="proofForm.sender_name"
                                type="text"
                                class="mt-1 min-h-10 w-full rounded-md border bg-background px-3"
                            />
                        </div>
                        <div>
                            <label
                                class="text-sm font-medium"
                                for="transfer-date"
                            >
                                {{ t('phase02.payment.transferDate') }}
                            </label>
                            <input
                                id="transfer-date"
                                v-model="proofForm.transfer_date"
                                type="date"
                                class="mt-1 min-h-10 w-full rounded-md border bg-background px-3"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex min-h-10 items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground disabled:opacity-50"
                        :disabled="proofForm.processing || !proofForm.proof"
                    >
                        <FileUp class="size-4" />
                        {{
                            registration.payment.status ===
                            'CORRECTION_REQUIRED'
                                ? t('phase02.payment.replaceProof')
                                : t('phase02.payment.submitProof')
                        }}
                    </button>
                </form>
            </section>

            <section
                v-else
                class="rounded-xl border bg-card p-5 shadow-sm"
            >
                <p class="text-sm font-semibold">
                    {{
                        registration.complimentary
                            ? t('phase02.billing.complimentary')
                            : t('phase02.billing.free')
                    }}
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ t('phase02.billing.noPaymentRequired') }}
                </p>
            </section>
        </template>
    </div>
</template>
