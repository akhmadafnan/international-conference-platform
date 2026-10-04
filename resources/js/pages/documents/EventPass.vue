<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2 } from '@lucide/vue';
import { useI18n } from 'vue-i18n';

defineProps<{
    eventPass: {
        id: string;
        issuedAt: string | null;
        snapshot: {
            registration_code?: string;
            participant_name?: string;
            package_name?: string;
            participant_category?: string | null;
            edition_code?: string;
            edition_year?: number;
            conference_series?: string | null;
            confirmed_at?: string | null;
        };
        publicCode: string | null;
        qrUrl: string;
    };
    routes: {
        dashboard: string;
    };
}>();

const { t } = useI18n();
</script>

<template>
    <Head :title="t('phase02.eventPass.title')" />

    <div class="mx-auto flex w-full max-w-3xl flex-col gap-5 p-4 md:p-6">
        <Link
            :href="routes.dashboard"
            class="inline-flex w-fit items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground"
        >
            <ArrowLeft class="size-4" />
            {{ t('phase02.eventPass.back') }}
        </Link>

        <article
            class="overflow-hidden rounded-2xl border bg-card shadow-sm"
            aria-labelledby="event-pass-title"
        >
            <div class="border-b bg-muted/30 p-5 md:p-6">
                <p class="text-sm font-semibold text-muted-foreground">
                    {{ eventPass.snapshot.edition_code }}
                    <span v-if="eventPass.snapshot.edition_year">
                        · {{ eventPass.snapshot.edition_year }}
                    </span>
                </p>
                <h1
                    id="event-pass-title"
                    class="mt-1 text-2xl font-semibold tracking-tight"
                >
                    {{ t('phase02.eventPass.title') }}
                </h1>
            </div>

            <div class="grid gap-6 p-5 md:grid-cols-[1fr_auto] md:p-6">
                <div class="space-y-5">
                    <div>
                        <p class="text-xs uppercase tracking-wider text-muted-foreground">
                            {{ t('phase02.eventPass.participant') }}
                        </p>
                        <p class="mt-1 text-xl font-semibold">
                            {{ eventPass.snapshot.participant_name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-muted-foreground">
                            {{ t('phase02.registration.registrationId') }}
                        </p>
                        <p class="mt-1 font-mono font-semibold">
                            {{ eventPass.snapshot.registration_code }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wider text-muted-foreground">
                            {{ t('phase02.eventPass.package') }}
                        </p>
                        <p class="mt-1 font-medium">
                            {{ eventPass.snapshot.package_name }}
                        </p>
                    </div>

                    <div
                        class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-sm font-semibold"
                    >
                        <CheckCircle2 class="size-4" />
                        {{ t('phase02.eventPass.confirmed') }}
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center gap-3">
                    <img
                        :src="eventPass.qrUrl"
                        :alt="t('phase02.eventPass.qrAlt')"
                        class="size-52 rounded-lg border bg-white p-2"
                        width="208"
                        height="208"
                    />
                    <code class="max-w-60 break-all text-center text-xs text-muted-foreground">
                        {{ eventPass.publicCode }}
                    </code>
                    <p class="max-w-56 text-center text-xs text-muted-foreground">
                        {{ t('phase02.eventPass.lookupNote') }}
                    </p>
                </div>
            </div>
        </article>
    </div>
</template>
