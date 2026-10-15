<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { submissionCopy } from '@/lib/submissionCopy';

type Registration = {
    id: string;
    code: string;
    edition: string;
    year: number;
    package: string;
};
type SubmissionSummary = {
    id: string;
    paperCode: string;
    edition: string;
    title: string | null;
    status: string;
    detailsUrl: string;
};

const props = defineProps<{
    registrations: Registration[];
    editionLinks: { code: string; year: number; registrationUrl: string }[];
    submissions: SubmissionSummary[];
    routes: { store: string; dashboard: string };
}>();

const { locale } = useI18n();
const c = computed(() => submissionCopy(String(locale.value)));
const form = useForm({ registration_id: '', primary_locale: 'id' });

function createDraft(): void {
    if (form.processing || !form.registration_id) return;
    form.post(props.routes.store, { preserveScroll: true });
}
</script>

<template>
    <Head :title="c.index" />
    <div class="flex w-full flex-col gap-6 px-4 py-6 md:ps-14 md:pe-8">
        <header class="space-y-1">
            <Link :href="routes.dashboard" class="text-sm text-muted-foreground underline-offset-4 hover:underline">
                {{ c.back }}
            </Link>
            <h1 class="text-2xl font-semibold tracking-tight">{{ c.index }}</h1>
            <p class="text-sm text-muted-foreground">{{ c.indexHint }}</p>
        </header>

        <section class="rounded-xl border bg-card p-5" aria-labelledby="create-draft-title">
            <h2 id="create-draft-title" class="text-lg font-semibold">{{ c.create }}</h2>
            <div v-if="registrations.length === 0" class="mt-3 space-y-3">
                <p class="text-sm text-muted-foreground">{{ c.noRegistrations }} {{ c.noRegistrationsHelp }}</p>
                <div v-if="editionLinks.length > 0" class="flex flex-wrap gap-2">
                    <Link v-for="edition in editionLinks" :key="edition.registrationUrl" :href="edition.registrationUrl"
                        class="rounded-md border px-3 py-2 text-sm font-medium hover:bg-accent">
                        {{ c.editionLink }} · {{ edition.code }} {{ edition.year }}
                    </Link>
                </div>
            </div>
            <form v-else class="mt-4 flex flex-col gap-4 md:max-w-3xl" @submit.prevent="createDraft">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="registration-id" class="text-sm font-medium">{{ c.chooseRegistration }}</label>
                        <select id="registration-id" v-model="form.registration_id" required
                            class="min-h-10 w-full rounded-md border bg-background px-3 text-sm">
                            <option value="" disabled>—</option>
                            <option v-for="registration in registrations" :key="registration.id" :value="registration.id">
                                {{ registration.edition }} · {{ registration.year }} · {{ registration.code }}
                            </option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="primary-locale" class="text-sm font-medium">{{ c.chooseLocale }}</label>
                        <select id="primary-locale" v-model="form.primary_locale"
                            class="min-h-10 w-full rounded-md border bg-background px-3 text-sm">
                            <option value="id">Bahasa Indonesia</option>
                            <option value="en">English</option>
                            <option value="ar">العربية</option>
                        </select>
                    </div>
                </div>
                <p v-if="form.errors.registration_id" role="alert" class="text-sm text-destructive">
                    {{ form.errors.registration_id === 'REGISTRATION_CONTEXT_INVALID' ? c.invalidRegistration : form.errors.registration_id }}
                </p>
                <p v-if="form.errors.primary_locale" role="alert" class="text-sm text-destructive">
                    {{ form.errors.primary_locale }}
                </p>
                <div><button type="submit" :disabled="form.processing || !form.registration_id"
                    class="min-h-10 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50">
                    {{ c.create }}
                </button></div>
            </form>
        </section>

        <section aria-labelledby="submissions-heading" class="space-y-3">
            <h2 id="submissions-heading" class="text-lg font-semibold">{{ c.index }}</h2>
            <div v-if="submissions.length === 0" class="rounded-xl border border-dashed bg-card p-6 text-center">
                <h3 class="font-semibold">{{ c.noSubmissions }}</h3>
                <p class="mt-2 text-sm text-muted-foreground">{{ c.noSubmissionsHelp }}</p>
            </div>
            <ul v-else class="grid gap-3">
                <li v-for="item in submissions" :key="item.id" class="rounded-xl border bg-card p-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="min-w-0 space-y-1">
                            <p class="font-mono text-xs text-muted-foreground" dir="ltr">{{ item.paperCode }}</p>
                            <h3 class="font-medium">{{ item.title || c.details }}</h3>
                            <p class="text-sm text-muted-foreground">{{ item.edition }} · {{ item.status === 'DRAFT' ? c.draft : item.status === 'SUBMITTED' ? c.submitted : item.status }}</p>
                        </div>
                        <Link :href="item.detailsUrl" class="rounded-md border px-4 py-2 text-sm font-medium hover:bg-accent">{{ c.open }}</Link>
                    </div>
                </li>
            </ul>
        </section>
    </div>
</template>
