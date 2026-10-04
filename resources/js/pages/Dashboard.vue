<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, CheckCircle2, Clock3, Ticket } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { dashboard } from '@/routes';

type RegistrationSummary = {
    id: string;
    code: string;
    status: string;
    packageName: string;
    billingMode: string;
    feeAmount: string;
    currencyCode: string;
    complimentary: boolean;
    nextAction: string;
    registrationUrl: string;
    eventPass: {
        showUrl: string;
    } | null;
};

defineProps<{
    phase02: {
        registration: RegistrationSummary | null;
    };
}>();

const { t } = useI18n();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});
</script>

<template>
    <Head :title="t('navigation.dashboard')" />

    <div class="mx-auto flex w-full max-w-5xl flex-col gap-6 p-4 md:p-6">
        <header>
            <p class="text-sm font-medium text-muted-foreground">
                {{ t('phase02.dashboard.eyebrow') }}
            </p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">
                {{ t('phase02.dashboard.title') }}
            </h1>
            <p class="mt-1 max-w-2xl text-sm text-muted-foreground">
                {{ t('phase02.dashboard.description') }}
            </p>
        </header>

        <section
            v-if="phase02.registration"
            class="rounded-xl border bg-card p-5 shadow-sm"
            aria-labelledby="next-action-heading"
        >
            <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                {{ t('phase02.nextAction.label') }}
            </p>
            <h2
                id="next-action-heading"
                class="mt-2 text-xl font-semibold"
            >
                {{
                    t(
                        `phase02.nextAction.${phase02.registration.nextAction}`,
                    )
                }}
            </h2>

            <div class="mt-5 flex flex-wrap gap-3">
                <Link
                    :href="phase02.registration.registrationUrl"
                    class="inline-flex min-h-10 items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground"
                >
                    {{ t('phase02.dashboard.continue') }}
                    <ArrowRight class="size-4 rtl:rotate-180" />
                </Link>

                <Link
                    v-if="phase02.registration.eventPass"
                    :href="phase02.registration.eventPass.showUrl"
                    class="inline-flex min-h-10 items-center gap-2 rounded-md border px-4 py-2 text-sm font-semibold"
                >
                    <Ticket class="size-4" />
                    {{ t('phase02.eventPass.open') }}
                </Link>
            </div>
        </section>

        <section
            v-if="phase02.registration"
            class="grid gap-4 sm:grid-cols-2"
        >
            <article class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <CheckCircle2
                        v-if="phase02.registration.status === 'CONFIRMED'"
                        class="size-5"
                    />
                    <Clock3 v-else class="size-5" />
                    <h2 class="font-semibold">
                        {{ t('phase02.dashboard.registration') }}
                    </h2>
                </div>
                <p class="mt-4 font-mono text-sm font-semibold">
                    {{ phase02.registration.code }}
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ phase02.registration.packageName }}
                </p>
                <p class="mt-3 text-sm font-medium">
                    {{
                        t(
                            `phase02.registrationStatus.${phase02.registration.status}`,
                        )
                    }}
                </p>
            </article>

            <article class="rounded-xl border bg-card p-5 shadow-sm">
                <div class="flex items-center gap-2">
                    <Ticket class="size-5" />
                    <h2 class="font-semibold">
                        {{ t('phase02.eventPass.title') }}
                    </h2>
                </div>
                <p class="mt-4 text-sm text-muted-foreground">
                    {{
                        phase02.registration.eventPass
                            ? t('phase02.dashboard.eventPassReady')
                            : t('phase02.dashboard.eventPassPending')
                    }}
                </p>
            </article>
        </section>

        <section
            v-else
            class="rounded-xl border border-dashed bg-card p-6 text-center"
        >
            <h2 class="text-lg font-semibold">
                {{ t('phase02.dashboard.noRegistration') }}
            </h2>
            <p class="mx-auto mt-2 max-w-xl text-sm text-muted-foreground">
                {{ t('phase02.dashboard.noRegistrationHelp') }}
            </p>
        </section>
    </div>
</template>
