<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { submissionCopy } from '@/lib/submissionCopy';
import SubmissionWizardSections from '@/components/submissions/SubmissionWizardSections.vue';

type Track = { id: string; code: string; name: Record<string, string> | null };
type Submission = {
    id: string;
    paperCode: string;
    edition: string;
    status: string;
    primaryLocale: string;
    trackId: string | null;
    title: string;
    subtitle: string;
    abstract: string;
    keywordsText: string;
};

const props = defineProps<{
    submission: Submission;
    tracks: Track[];
    contributors: {
        id: string | null;
        display_name: string;
        given_name: string | null;
        family_name: string | null;
        single_name: boolean;
        email: string;
        country_code: string;
        orcid: string | null;
        is_corresponding: boolean;
        affiliations: {
            institution_id: string | null;
            institution_name: string;
            subdivision: string | null;
            city: string | null;
            country_code: string;
        }[];
    }[];
    references: {
        id: string | null;
        raw_citation: string;
        doi: string | null;
        url: string | null;
    }[];
    files: { id: string; version: number; name: string | null }[];
    readiness: {
        status: string;
        findings: { code: string; field: string; severity: string }[];
    } | null;
    routes: {
        index: string;
        update: string;
        contributors: string;
        references: string;
        upload: string;
        submit: string;
    };
}>();

const { locale } = useI18n();
const c = computed(() => submissionCopy(String(locale.value)));
const draft = computed(() => props.submission.status === 'DRAFT');
const detailsError = computed(
    () => (form.errors as Record<string, string | undefined>).details,
);
const form = useForm({
    track_id: props.submission.trackId ?? '',
    title: props.submission.title,
    subtitle: props.submission.subtitle,
    abstract: props.submission.abstract,
    keywords_text: props.submission.keywordsText,
});

function trackName(track: Track): string {
    return (
        track.name?.[String(locale.value)] ??
        track.name?.en ??
        track.name?.id ??
        track.code
    );
}

function saveDraft(): void {
    if (!draft.value || form.processing) return;
    form.transform((data) => ({
        ...data,
        track_id: data.track_id || null,
    })).put(props.routes.update, { preserveScroll: true });
}
</script>

<template>
    <Head :title="c.details" />
    <div class="flex w-full flex-col gap-6 px-4 py-6 md:ps-14 md:pe-8">
        <header class="space-y-1">
            <Link
                :href="routes.index"
                class="text-sm text-muted-foreground underline-offset-4 hover:underline"
                >{{ c.index }}</Link
            >
            <p class="text-sm font-medium text-muted-foreground">
                {{ c.step }}
            </p>
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ c.details }}
            </h1>
            <p class="font-mono text-sm text-muted-foreground" dir="ltr">
                {{ submission.paperCode }} ┬╖ {{ submission.edition }}
            </p>
            <p class="max-w-3xl text-sm text-muted-foreground">
                {{ c.detailsHelp }}
            </p>
        </header>

        <div
            v-if="!draft"
            class="rounded-lg border bg-muted px-4 py-3 text-sm"
            role="status"
        >
            {{ c.locked }}
        </div>
        <form
            class="max-w-5xl space-y-5 rounded-xl border bg-card p-5"
            @submit.prevent="saveDraft"
        >
            <fieldset
                :disabled="!draft || form.processing"
                class="space-y-5 disabled:opacity-80"
            >
                <div class="space-y-1">
                    <label class="text-sm font-medium">{{
                        c.primaryLocale
                    }}</label>
                    <p class="rounded-md border bg-muted px-3 py-2 text-sm">
                        {{ submission.primaryLocale.toUpperCase() }}
                    </p>
                    <p class="text-xs text-muted-foreground">
                        {{ c.primaryLocaleHelp }}
                    </p>
                </div>
                <div class="space-y-2">
                    <label for="track" class="text-sm font-medium">{{
                        c.track
                    }}</label>
                    <select
                        id="track"
                        v-model="form.track_id"
                        class="min-h-10 w-full rounded-md border bg-background px-3 text-sm"
                    >
                        <option value="">{{ c.noTrack }}</option>
                        <option
                            v-for="track in tracks"
                            :key="track.id"
                            :value="track.id"
                        >
                            {{ trackName(track) }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.track_id"
                        class="text-sm text-destructive"
                        role="alert"
                    >
                        {{ form.errors.track_id }}
                    </p>
                </div>
                <div class="space-y-2">
                    <label for="title" class="text-sm font-medium">{{
                        c.title
                    }}</label>
                    <input
                        id="title"
                        v-model="form.title"
                        required
                        maxlength="500"
                        :dir="submission.primaryLocale === 'ar' ? 'rtl' : 'ltr'"
                        class="min-h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                    />
                    <p
                        v-if="form.errors.title"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.title }}
                    </p>
                </div>
                <div class="space-y-2">
                    <label for="subtitle" class="text-sm font-medium">{{
                        c.subtitle
                    }}</label>
                    <input
                        id="subtitle"
                        v-model="form.subtitle"
                        maxlength="500"
                        :dir="submission.primaryLocale === 'ar' ? 'rtl' : 'ltr'"
                        class="min-h-10 w-full rounded-md border bg-background px-3 py-2 text-sm"
                    />
                    <p
                        v-if="form.errors.subtitle"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.subtitle }}
                    </p>
                </div>
                <div class="space-y-2">
                    <label for="abstract" class="text-sm font-medium">{{
                        c.abstract
                    }}</label>
                    <textarea
                        id="abstract"
                        v-model="form.abstract"
                        rows="8"
                        required
                        maxlength="30000"
                        :dir="submission.primaryLocale === 'ar' ? 'rtl' : 'ltr'"
                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                    />
                    <p
                        v-if="form.errors.abstract"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.abstract }}
                    </p>
                </div>
                <div class="space-y-2">
                    <label for="keywords" class="text-sm font-medium">{{
                        c.keywords
                    }}</label>
                    <textarea
                        id="keywords"
                        v-model="form.keywords_text"
                        rows="5"
                        maxlength="2000"
                        :dir="submission.primaryLocale === 'ar' ? 'rtl' : 'ltr'"
                        class="w-full rounded-md border bg-background px-3 py-2 text-sm"
                    />
                    <p class="text-xs text-muted-foreground">
                        {{ c.keywordsHelp }}
                    </p>
                    <p
                        v-if="form.errors.keywords_text"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ form.errors.keywords_text }}
                    </p>
                </div>
            </fieldset>
            <p
                v-if="detailsError"
                role="alert"
                class="text-sm text-destructive"
            >
                {{ c.invalidDetails }}
            </p>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    v-if="draft"
                    type="submit"
                    :disabled="form.processing"
                    class="min-h-10 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground disabled:opacity-50"
                >
                    {{ c.save }}
                </button>
                <Link
                    :href="routes.index"
                    class="min-h-10 rounded-md border px-4 py-2 text-sm leading-6 font-medium hover:bg-accent"
                    >{{ c.index }}</Link
                >
            </div>
            <p class="text-xs text-muted-foreground">
                {{ draft ? c.savedHint : c.locked }}
            </p>
        </form>
        <SubmissionWizardSections
            :draft="draft"
            :contributors="contributors"
            :references="references"
            :files="files"
            :readiness="readiness"
            :routes="routes"
        />
    </div>
</template>
