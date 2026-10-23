<script setup lang="ts">
import { Head, Link, useForm, useRemember } from '@inertiajs/vue3';
import { computed, ref, watch, nextTick, unref, toRef } from 'vue';
import { useI18n } from 'vue-i18n';
import { submissionCopy } from '@/lib/submissionCopy';
import SubmissionWizardSections from '@/components/submissions/SubmissionWizardSections.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import KeywordInput from '@/components/submissions/KeywordInput.vue';
import AcademicAbstractEditor from '@/components/submissions/AcademicAbstractEditor.vue';
import { serializeKeywords, keywordTags } from '@/lib/submissionInputs';
import {
    wizardSteps,
    trackEmptyMessage,
    type AuthorReadiness,
} from '@/lib/authorWizard';

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
    policy: {
        minKeywords: number;
        maxKeywords: number;
        abstractFileRequired: boolean;
        trackRequired: boolean;
    } | null;
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
            ror_uri?: string | null;
        }[];
    }[];
    references: {
        id: string | null;
        raw_citation: string;
        doi: string | null;
        url: string | null;
    }[];
    files: { id: string; version: number; name: string | null }[];
    readiness: AuthorReadiness | null;
    routes: {
        index: string;
        update: string;
        contributors: string;
        references: string;
        upload: string;
        submit: string;
    };
}>();

const { locale, t } = useI18n();
const c = computed(() => submissionCopy(String(locale.value)));
const draft = computed(() => props.submission.status === 'DRAFT');
const detailsError = computed(
    () => (form.errors as Record<string, string | undefined>).details,
);
const form = useForm(`submission-details:${props.submission.id}`, {
    track_id: props.submission.trackId ?? '',
    title: props.submission.title,
    subtitle: props.submission.subtitle,
    abstract: props.submission.abstract,
    keywords_text: props.submission.keywordsText,
});
const navigation = unref(
    useRemember(
        { step: props.submission.status === 'DRAFT' ? 1 : 5 },
        `submission-step:${props.submission.id}`,
    ),
);
const step = toRef(navigation, 'step');
const stages = ref<InstanceType<typeof SubmissionWizardSections>>();
const keywords = ref<InstanceType<typeof KeywordInput>>();
const saving = ref(false);
const saved = ref(false);
const busy = computed(
    () => saving.value || form.processing || stages.value?.processing === true,
);
const stepKeys = wizardSteps;
const trackNotice = computed(() =>
    trackEmptyMessage(
        props.tracks.length,
        props.policy?.trackRequired === true,
    ),
);
watch(draft, (value) => {
    if (!value) step.value = 5;
});
watch(step, async () => {
    await nextTick();
    document.getElementById('wizard-step-heading')?.focus();
});
const reviewDetails = computed(() => ({
    title: form.title,
    subtitle: form.subtitle,
    abstract: form.abstract,
    keywords: keywordTags(form.keywords_text),
    primaryLocale: props.submission.primaryLocale,
    track: props.tracks.find((track) => track.id === form.track_id)
        ? trackName(props.tracks.find((track) => track.id === form.track_id)!)
        : c.value.noTrack,
}));

function trackName(track: Track): string {
    return (
        track.name?.[String(locale.value)] ??
        track.name?.en ??
        track.name?.id ??
        track.code
    );
}

function saveDetails(done: () => void): void {
    keywords.value?.commit();
    form.keywords_text = serializeKeywords(keywordTags(form.keywords_text));
    form.transform((data) => ({
        ...data,
        track_id: data.track_id || null,
    })).put(props.routes.update, {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults();
            done();
        },
        onError: () => failed(1),
        onCancel: () => failed(1),
    });
}
function failed(target: number) {
    saving.value = false;
    step.value = target;
    saved.value = false;
}
function saveCurrent(done: () => void) {
    if (step.value === 1) saveDetails(done);
    else if (step.value === 5) saveAll(done);
    else stages.value?.saveStep(step.value, done);
}
function saveAll(done: () => void) {
    saveDetails(() => stages.value?.saveAll(done));
}
function saveDraft() {
    if (!draft.value || busy.value) return;
    saving.value = true;
    saveCurrent(() => {
        saving.value = false;
        saved.value = true;
    });
}
function navigate(target: number) {
    if (busy.value || target === step.value) return;
    if (!draft.value || target < step.value) {
        step.value = target;
        return;
    }
    saving.value = true;
    saveCurrent(() => {
        saving.value = false;
        step.value = target;
        saved.value = false;
    });
}
function prepareSubmit() {
    if (!draft.value || busy.value) return;
    saving.value = true;
    saveAll(async () => {
        saving.value = false;
        step.value = 5;
        await nextTick();
        stages.value?.confirmSubmission();
    });
}
</script>

<template>
    <Head :title="c.details" />
    <div
        class="flex w-full min-w-0 flex-col gap-6 p-4 [overflow-wrap:anywhere] md:p-8"
    >
        <header class="space-y-1">
            <Link
                :href="routes.index"
                class="text-sm text-muted-foreground underline-offset-4 hover:underline"
                >{{ c.index }}</Link
            >
            <p class="text-sm font-medium text-muted-foreground">
                {{ t('wizardUx.progress', { current: step, total: 5 }) }}
            </p>
            <h1
                id="wizard-step-heading"
                tabindex="-1"
                class="text-2xl font-semibold tracking-tight"
            >
                {{ t(`wizardUx.${stepKeys[step - 1]}`) }}
            </h1>
            <p class="font-mono text-sm text-muted-foreground" dir="ltr">
                {{ submission.paperCode }} · {{ submission.edition }}
            </p>
            <p class="text-sm text-muted-foreground">
                {{ t('wizardUx.intro') }}
            </p>
        </header>

        <nav :aria-label="t('wizardUx.steps')" class="w-full min-w-0">
            <ol class="grid min-w-0 auto-rows-fr gap-2 md:grid-cols-5">
                <li v-for="(key, index) in stepKeys" :key="key" class="min-w-0">
                    <Button
                        type="button"
                        variant="outline"
                        class="h-full! min-h-14 w-full max-w-full min-w-0 justify-start text-start whitespace-normal"
                        :class="
                            step === index + 1
                                ? 'border-primary bg-primary/10 text-primary'
                                : ''
                        "
                        :aria-current="step === index + 1 ? 'step' : undefined"
                        :disabled="busy"
                        @click="navigate(index + 1)"
                        ><span aria-hidden="true">{{ index + 1 }}.</span
                        >{{ t(`wizardUx.${key}`) }}</Button
                    >
                </li>
            </ol>
        </nav>
        <p v-if="saved" role="status" class="text-sm text-primary">
            {{ t('wizardUx.saved') }}
        </p>
        <div
            v-if="!draft"
            class="min-w-0 rounded-lg border bg-muted px-4 py-3 text-sm"
            role="status"
        >
            {{ c.locked }}
            <Link
                :href="`/dashboard/submissions/${submission.id}/academic`"
                class="ms-2 underline"
                >{{ t('academic.academicStatus') }}</Link
            >
        </div>
        <Card v-show="step === 1" class="w-full min-w-0"
            ><CardContent>
                <form class="min-w-0 space-y-5" @submit.prevent="saveDraft">
                    <fieldset
                        :disabled="!draft || saving || form.processing"
                        class="grid min-w-0 gap-5 disabled:opacity-80 sm:grid-cols-2"
                    >
                        <div class="min-w-0 space-y-1">
                            <label class="text-sm font-medium">{{
                                c.primaryLocale
                            }}</label>
                            <p
                                class="rounded-md border bg-muted px-3 py-2 text-sm"
                            >
                                {{ submission.primaryLocale.toUpperCase() }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ c.primaryLocaleHelp }}
                            </p>
                        </div>
                        <div class="min-w-0 space-y-2">
                            <label for="track" class="text-sm font-medium">{{
                                c.track
                            }}</label>
                            <Select
                                :model-value="form.track_id || '__no_track__'"
                                :disabled="!draft || saving || form.processing"
                                @update:model-value="
                                    (value) =>
                                        (form.track_id =
                                            value === '__no_track__'
                                                ? ''
                                                : String(value))
                                "
                            >
                                <SelectTrigger
                                    id="track"
                                    class="w-full"
                                    :aria-invalid="!!form.errors.track_id"
                                    aria-describedby="track-notice track-error"
                                    ><SelectValue
                                /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="__no_track__">{{
                                        policy?.trackRequired
                                            ? t('wizardUx.selectTrack')
                                            : c.noTrack
                                    }}</SelectItem>
                                    <SelectItem
                                        v-for="track in tracks"
                                        :key="track.id"
                                        :value="track.id"
                                    >
                                        {{ trackName(track) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p
                                v-if="trackNotice"
                                id="track-notice"
                                role="status"
                                class="text-sm text-muted-foreground"
                            >
                                {{ t(`wizardUx.${trackNotice}`) }}
                            </p>
                            <p
                                v-if="form.errors.track_id"
                                id="track-error"
                                class="text-sm text-destructive"
                                role="alert"
                            >
                                {{ form.errors.track_id }}
                            </p>
                        </div>
                        <div class="min-w-0 space-y-2">
                            <label for="title" class="text-sm font-medium">{{
                                c.title
                            }}</label>
                            <Input
                                id="title"
                                v-model="form.title"
                                required
                                maxlength="500"
                                :dir="
                                    submission.primaryLocale === 'ar'
                                        ? 'rtl'
                                        : 'ltr'
                                "
                                :aria-invalid="!!form.errors.title"
                                aria-describedby="title-error"
                            />
                            <p
                                v-if="form.errors.title"
                                id="title-error"
                                role="alert"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>
                        <div class="min-w-0 space-y-2">
                            <label for="subtitle" class="text-sm font-medium">{{
                                c.subtitle
                            }}</label>
                            <Input
                                id="subtitle"
                                v-model="form.subtitle"
                                maxlength="500"
                                :dir="
                                    submission.primaryLocale === 'ar'
                                        ? 'rtl'
                                        : 'ltr'
                                "
                                :aria-invalid="!!form.errors.subtitle"
                                aria-describedby="subtitle-error"
                            />
                            <p
                                v-if="form.errors.subtitle"
                                id="subtitle-error"
                                role="alert"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.subtitle }}
                            </p>
                        </div>
                        <div class="min-w-0 space-y-2 sm:col-span-2">
                            <label for="abstract" class="text-sm font-medium">{{
                                c.abstract
                            }}</label>
                            <AcademicAbstractEditor
                                id="abstract"
                                v-model="form.abstract"
                                :dir="
                                    submission.primaryLocale === 'ar'
                                        ? 'rtl'
                                        : 'ltr'
                                "
                                :disabled="!draft || saving"
                                :invalid="!!form.errors.abstract"
                            />
                            <p
                                v-if="form.errors.abstract"
                                id="abstract-error"
                                role="alert"
                                class="text-sm text-destructive"
                            >
                                {{ form.errors.abstract }}
                            </p>
                        </div>
                        <div class="min-w-0 space-y-2 sm:col-span-2">
                            <label for="keywords" class="text-sm font-medium">{{
                                c.keywords
                            }}</label>
                            <KeywordInput
                                ref="keywords"
                                id="keywords"
                                v-model="form.keywords_text"
                                :min="policy?.minKeywords ?? null"
                                :max="policy?.maxKeywords ?? null"
                                :disabled="!draft || saving"
                            />
                            <p
                                v-if="form.errors.keywords_text"
                                id="keywords-error"
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
                    <p class="text-xs text-muted-foreground">
                        {{ draft ? c.savedHint : c.locked }}
                    </p>
                </form>
            </CardContent></Card
        >
        <SubmissionWizardSections
            ref="stages"
            :step="step"
            :details="reviewDetails"
            :saving="saving"
            @edit="(target) => navigate(target)"
            @save-error="failed"
            @prepare-submit="prepareSubmit"
            :draft="draft"
            :contributors="contributors"
            :references="references"
            :files="files"
            :readiness="readiness"
            :routes="routes"
        />
        <footer
            class="flex w-full flex-wrap items-center justify-between gap-3 border-t border-border pt-4"
        >
            <Button
                class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                type="button"
                variant="outline"
                :disabled="step === 1 || busy"
                @click="navigate(step - 1)"
                >{{ t('wizardUx.previous') }}</Button
            >
            <div class="flex min-w-0 flex-wrap gap-2">
                <Button
                    class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                    v-if="draft"
                    type="button"
                    variant="outline"
                    :disabled="busy"
                    @click="saveDraft"
                    >{{
                        saving ? t('wizardUx.saving') : t('wizardUx.saveDraft')
                    }}</Button
                >
                <Button
                    class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                    v-if="step < 5"
                    type="button"
                    :disabled="busy"
                    @click="navigate(step + 1)"
                    >{{ t('wizardUx.continue') }}</Button
                >
            </div>
        </footer>
    </div>
</template>
