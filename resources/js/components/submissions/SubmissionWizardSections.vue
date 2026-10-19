<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import {
    PhPlus,
    PhTrash,
    PhUploadSimple,
    PhArrowUp,
    PhArrowDown,
    PhPencilSimple,
} from '@phosphor-icons/vue';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InstitutionLookup from '@/components/submissions/InstitutionLookup.vue';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import {
    bulkReferences,
    referencesText,
    removedReferences,
} from '@/lib/submissionInputs';
import {
    authorBlockers,
    configurationBlockers,
    findingStep,
    findingMessage,
    submissionBlocked,
    wizardSteps,
    type AuthorReadiness,
    type ReadinessFinding,
} from '@/lib/authorWizard';

type Aff = {
    institution_id: string | null;
    institution_name: string;
    subdivision: string | null;
    city: string | null;
    country_code: string;
    ror_uri?: string | null;
};
type Author = {
    id: string | null;
    display_name: string;
    given_name: string | null;
    family_name: string | null;
    single_name: boolean;
    email: string;
    country_code: string;
    orcid: string | null;
    is_corresponding: boolean;
    affiliations: Aff[];
};
type Ref = {
    id: string | null;
    raw_citation: string;
    doi: string | null;
    url: string | null;
};
const props = defineProps<{
    draft: boolean;
    step: number;
    saving: boolean;
    details: {
        title: string;
        subtitle: string;
        abstract: string;
        keywords: string[];
        primaryLocale: string;
        track: string;
    };
    contributors: Author[];
    references: Ref[];
    files: { id: string; version: number; name: string | null }[];
    readiness: AuthorReadiness | null;
    routes: {
        contributors: string;
        references: string;
        upload: string;
        submit: string;
    };
}>();
const emit = defineEmits<{
    edit: [step: number];
    'save-error': [step: number];
    'prepare-submit': [];
}>();
const { t } = useI18n();
const c = (key: string) => t('submissionWizard.' + key);
const contributorForm = useForm(
    `submission-authors:${props.routes.contributors}`,
    {
        contributors: props.contributors.map((x) => ({
            ...x,
            affiliations: x.affiliations.map((a) => ({ ...a })),
        })),
    },
);
const referenceForm = useForm(
    `submission-references:${props.routes.references}`,
    {
        references: props.references.map((x) => ({ ...x })),
    },
);
const fileForm = useForm({ file: null as File | null });
const submitForm = useForm({});
const activeAuthor = ref<number | null>(null);
const individualReferences = ref(false);
const bulkText = ref(referencesText(referenceForm.references));
const confirmOpen = ref(false);
const deletionOpen = ref(false);
const removed = ref<Ref[]>([]);
let pendingReferences: (() => void) | null = null;
const processing = computed(
    () =>
        contributorForm.processing ||
        referenceForm.processing ||
        fileForm.processing ||
        submitForm.processing,
);
function moveAuthor(index: number, offset: number) {
    const next = index + offset;
    if (next < 0 || next >= contributorForm.contributors.length) return;
    const [author] = contributorForm.contributors.splice(index, 1);
    contributorForm.contributors.splice(next, 0, author);
    activeAuthor.value = next;
}
function toggleReferences() {
    if (individualReferences.value)
        bulkText.value = referencesText(referenceForm.references);
    else
        referenceForm.references = bulkReferences(
            bulkText.value,
            referenceForm.references,
        );
    individualReferences.value = !individualReferences.value;
}
function saveAuthors(done: () => void = () => {}) {
    contributorForm
        .transform((data) => ({
            contributors: data.contributors.map((author) => ({
                ...author,
                affiliations: author.affiliations.map((aff) => ({
                    institution_id: aff.institution_id,
                    institution_name: aff.institution_name,
                    subdivision: aff.subdivision,
                    city: aff.city,
                    country_code: aff.country_code,
                })),
            })),
        }))
        .put(props.routes.contributors, {
            preserveScroll: true,
            onSuccess: () => {
                contributorForm.contributors = props.contributors.map(
                    (author) => ({
                        ...author,
                        affiliations: author.affiliations.map((aff) => ({
                            ...aff,
                        })),
                    }),
                );
                contributorForm.defaults();
                done();
            },
            onError: (errors) => {
                const key = Object.keys(errors).find((key) =>
                    /^contributors\.\d+/.test(key),
                );
                activeAuthor.value = key ? Number(key.split('.')[1]) : 0;
                emit('save-error', 3);
            },
            onCancel: () => emit('save-error', 3),
        });
}
function saveReferences(done: () => void = () => {}) {
    const next = individualReferences.value
        ? referenceForm.references
        : bulkReferences(bulkText.value, referenceForm.references);
    const write = () => {
        referenceForm.references = next;
        referenceForm.put(props.routes.references, {
            preserveScroll: true,
            onSuccess: () => {
                referenceForm.references = props.references.map((item) => ({
                    ...item,
                }));
                referenceForm.defaults();
                bulkText.value = referencesText(referenceForm.references);
                done();
            },
            onError: () => emit('save-error', 4),
            onCancel: () => emit('save-error', 4),
        });
    };
    removed.value = removedReferences(next, props.references);
    if (removed.value.length) {
        pendingReferences = write;
        deletionOpen.value = true;
    } else write();
}
function resolveReferences(confirm: boolean) {
    deletionOpen.value = false;
    const pending = pendingReferences;
    pendingReferences = null;
    if (confirm) pending?.();
    else emit('save-error', 4);
}
function saveFile(done: () => void = () => {}) {
    if (!fileForm.file) {
        done();
        return;
    }
    fileForm.post(props.routes.upload, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            fileForm.reset();
            done();
        },
        onError: () => emit('save-error', 2),
        onCancel: () => emit('save-error', 2),
    });
}
function saveStep(step: number, done: () => void) {
    if (step === 2) saveFile(done);
    else if (step === 3) saveAuthors(done);
    else if (step === 4) saveReferences(done);
}
function saveAll(done: () => void) {
    saveFile(() => saveAuthors(() => saveReferences(done)));
}
function confirmSubmission() {
    if (!blocked.value) confirmOpen.value = true;
}
function officialSubmit() {
    if (blocked.value || processing.value) return;
    submitForm.post(props.routes.submit, {
        preserveScroll: true,
        onSuccess: () => {
            confirmOpen.value = false;
        },
    });
}
defineExpose({ saveStep, saveAll, confirmSubmission, processing });
const submitError = computed(
    () => (submitForm.errors as Record<string, string | undefined>).submit,
);
const blocked = computed(() => submissionBlocked(props.readiness));
const blockers = computed(() => authorBlockers(props.readiness));
const configuration = computed(() => configurationBlockers(props.readiness));
const newAff = (): Aff => ({
    institution_id: null,
    institution_name: '',
    subdivision: null,
    city: null,
    country_code: 'ID',
});
function addAuthor() {
    contributorForm.contributors.push({
        id: null,
        display_name: '',
        given_name: '',
        family_name: '',
        single_name: false,
        email: '',
        country_code: 'ID',
        orcid: null,
        is_corresponding: contributorForm.contributors.length === 0,
        affiliations: [newAff()],
    });
    activeAuthor.value = contributorForm.contributors.length - 1;
}
function corresponding(i: number) {
    contributorForm.contributors.forEach((a, index) => {
        a.is_corresponding = index === i;
    });
}
function uploadFile(event: Event) {
    fileForm.file = (event.target as HTMLInputElement).files?.[0] ?? null;
}
function finding(item: ReadinessFinding) {
    const context = item.context ?? {};
    return t('wizardUx.' + findingMessage(item), {
        ...context,
        remaining: Math.max(0, (context.min ?? 0) - (context.actual ?? 0)),
        excess: Math.max(0, (context.actual ?? 0) - (context.max ?? 0)),
    });
}
function fixLabel(item: ReadinessFinding) {
    const target = findingStep(item);
    return t('wizardUx.fixStep', {
        step: target ? t('wizardUx.' + wizardSteps[target - 1]) : '',
    });
}
</script>

<template>
    <div class="grid w-full max-w-5xl gap-6">
        <Card v-show="step === 3">
            <CardHeader
                ><CardTitle>{{ t('wizardUx.contributors') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('contributorsHelp') }}
                </p>
            </CardHeader>
            <CardContent>
                <form
                    class="grid gap-4"
                    novalidate
                    @submit.prevent="saveAuthors()"
                >
                    <fieldset
                        :disabled="!draft || saving || processing"
                        class="grid gap-5"
                    >
                        <div
                            v-for="(author, i) in contributorForm.contributors"
                            :key="author.id ?? i"
                            class="grid gap-4 border border-border p-4"
                        >
                            <div
                                class="flex items-center justify-between gap-2"
                            >
                                <h3 class="font-semibold">
                                    {{ c('author') }} {{ i + 1 }}
                                </h3>
                                <Button
                                    v-if="
                                        draft &&
                                        contributorForm.contributors.length > 1
                                    "
                                    variant="outline"
                                    type="button"
                                    size="sm"
                                    @click="
                                        contributorForm.contributors.splice(
                                            i,
                                            1,
                                        )
                                    "
                                >
                                    <PhTrash class="size-4" /> {{ c('remove') }}
                                </Button>
                            </div>
                            <p class="text-sm">
                                <bdi>{{
                                    author.display_name || c('author')
                                }}</bdi>
                                <span aria-hidden="true"> · </span
                                ><bdi>{{ author.email }}</bdi
                                ><span
                                    v-if="author.is_corresponding"
                                    class="ms-2 text-primary"
                                    >{{ c('corresponding') }}</span
                                >
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="
                                        activeAuthor =
                                            activeAuthor === i ? null : i
                                    "
                                    ><PhPencilSimple
                                        class="size-4"
                                        aria-hidden="true"
                                    />{{ t('wizardUx.edit') }}</Button
                                >
                                <Button
                                    v-if="draft"
                                    type="button"
                                    size="icon-sm"
                                    variant="outline"
                                    :disabled="i === 0"
                                    :aria-label="t('wizardUx.moveUp')"
                                    @click="moveAuthor(i, -1)"
                                    ><PhArrowUp
                                        class="size-4"
                                        aria-hidden="true"
                                /></Button>
                                <Button
                                    v-if="draft"
                                    type="button"
                                    size="icon-sm"
                                    variant="outline"
                                    :disabled="
                                        i ===
                                        contributorForm.contributors.length - 1
                                    "
                                    :aria-label="t('wizardUx.moveDown')"
                                    @click="moveAuthor(i, 1)"
                                    ><PhArrowDown
                                        class="size-4"
                                        aria-hidden="true"
                                /></Button>
                            </div>
                            <div v-show="activeAuthor === i" class="grid gap-4">
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="grid gap-1">
                                        <Label :for="'author-name-' + i">{{
                                            c('name')
                                        }}</Label>
                                        <Input
                                            :id="'author-name-' + i"
                                            v-model="author.display_name"
                                            required
                                            maxlength="255"
                                        />
                                    </div>
                                    <div class="grid gap-1">
                                        <Label :for="'author-email-' + i">{{
                                            c('email')
                                        }}</Label>
                                        <Input
                                            :id="'author-email-' + i"
                                            v-model="author.email"
                                            required
                                            type="email"
                                        />
                                    </div>
                                    <div class="grid gap-1">
                                        <Label :for="'author-country-' + i">{{
                                            c('country')
                                        }}</Label>
                                        <Input
                                            :id="'author-country-' + i"
                                            v-model="author.country_code"
                                            required
                                            maxlength="2"
                                        />
                                    </div>
                                    <div class="grid gap-1">
                                        <Label :for="'author-orcid-' + i">{{
                                            c('orcid')
                                        }}</Label>
                                        <Input
                                            :id="'author-orcid-' + i"
                                            :model-value="author.orcid ?? ''"
                                            @update:model-value="
                                                (value) => {
                                                    author.orcid =
                                                        String(value);
                                                }
                                            "
                                            maxlength="255"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Checkbox
                                        :id="'single-' + i"
                                        v-model="author.single_name"
                                        @update:model-value="
                                            (value) => {
                                                if (value) {
                                                    author.given_name = null;
                                                    author.family_name = null;
                                                }
                                            }
                                        "
                                    />
                                    <Label :for="'single-' + i">{{
                                        c('single')
                                    }}</Label>
                                </div>
                                <div
                                    v-if="!author.single_name"
                                    class="grid gap-4 sm:grid-cols-2"
                                >
                                    <div class="grid gap-1">
                                        <Label :for="'given-' + i">{{
                                            c('given')
                                        }}</Label>
                                        <Input
                                            :id="'given-' + i"
                                            :model-value="
                                                author.given_name ?? ''
                                            "
                                            @update:model-value="
                                                (value) => {
                                                    author.given_name =
                                                        String(value);
                                                }
                                            "
                                            maxlength="255"
                                        />
                                    </div>
                                    <div class="grid gap-1">
                                        <Label :for="'family-' + i">{{
                                            c('family')
                                        }}</Label>
                                        <Input
                                            :id="'family-' + i"
                                            :model-value="
                                                author.family_name ?? ''
                                            "
                                            @update:model-value="
                                                (value) => {
                                                    author.family_name =
                                                        String(value);
                                                }
                                            "
                                            maxlength="255"
                                        />
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Checkbox
                                        :id="'corresponding-' + i"
                                        :model-value="author.is_corresponding"
                                        @update:model-value="
                                            (value) => {
                                                if (value) corresponding(i);
                                            }
                                        "
                                    />
                                    <Label :for="'corresponding-' + i">{{
                                        c('corresponding')
                                    }}</Label>
                                </div>
                                <div
                                    v-for="(aff, j) in author.affiliations"
                                    :key="j"
                                    class="grid gap-3 border-s-2 border-border ps-3"
                                >
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="grid gap-1">
                                            <Label
                                                :for="'inst-' + i + '-' + j"
                                                >{{ c('institution') }}</Label
                                            >
                                            <InstitutionLookup
                                                :input-id="
                                                    'inst-' + i + '-' + j
                                                "
                                                :value="aff.institution_name"
                                                :selected-id="
                                                    aff.institution_id
                                                "
                                                :disabled="
                                                    !draft ||
                                                    contributorForm.processing
                                                "
                                                @manual="
                                                    (value) => {
                                                        aff.institution_id =
                                                            null;
                                                        aff.institution_name =
                                                            value;
                                                    }
                                                "
                                                @selected="
                                                    (item) => {
                                                        aff.institution_id =
                                                            item.id;
                                                        aff.institution_name =
                                                            item.name;
                                                        if (item.country_code)
                                                            aff.country_code =
                                                                item.country_code;
                                                    }
                                                "
                                            />
                                        </div>
                                        <div class="grid gap-1">
                                            <Label
                                                :for="
                                                    'aff-country-' + i + '-' + j
                                                "
                                                >{{ c('country') }}</Label
                                            >
                                            <Input
                                                :id="
                                                    'aff-country-' + i + '-' + j
                                                "
                                                v-model="aff.country_code"
                                                required
                                                maxlength="2"
                                            />
                                        </div>
                                        <div class="grid gap-1">
                                            <Label
                                                :for="'city-' + i + '-' + j"
                                                >{{ c('city') }}</Label
                                            >
                                            <Input
                                                :id="'city-' + i + '-' + j"
                                                :model-value="aff.city ?? ''"
                                                @update:model-value="
                                                    (value) => {
                                                        aff.city =
                                                            String(value);
                                                    }
                                                "
                                                maxlength="255"
                                            />
                                        </div>
                                        <div class="grid gap-1">
                                            <Label
                                                :for="'dept-' + i + '-' + j"
                                                >{{ c('department') }}</Label
                                            >
                                            <Input
                                                :id="'dept-' + i + '-' + j"
                                                :model-value="
                                                    aff.subdivision ?? ''
                                                "
                                                @update:model-value="
                                                    (value) => {
                                                        aff.subdivision =
                                                            String(value);
                                                    }
                                                "
                                                maxlength="255"
                                            />
                                        </div>
                                    </div>
                                    <Button
                                        v-if="
                                            draft &&
                                            author.affiliations.length > 1
                                        "
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        @click="
                                            author.affiliations.splice(j, 1)
                                        "
                                        >{{ c('removeAffiliation') }}</Button
                                    >
                                </div>
                                <Button
                                    v-if="draft"
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="author.affiliations.push(newAff())"
                                    ><PhPlus class="size-4" />{{
                                        c('addAffiliation')
                                    }}</Button
                                >
                            </div>
                        </div>
                        <Button
                            v-if="draft"
                            type="button"
                            variant="outline"
                            @click="addAuthor"
                        >
                            <PhPlus class="size-4" />{{ c('addAuthor') }}
                        </Button>
                    </fieldset>
                    <p
                        v-if="contributorForm.hasErrors"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ c('formError') }}
                    </p>
                    <Button
                        v-if="draft"
                        type="submit"
                        :disabled="
                            contributorForm.processing ||
                            !contributorForm.contributors.length
                        "
                        >{{ c('saveAuthors') }}</Button
                    >
                </form>
            </CardContent>
        </Card>

        <Card v-show="step === 4">
            <CardHeader
                ><CardTitle>{{ t('wizardUx.references') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('referencesHelp') }}
                </p></CardHeader
            >
            <CardContent>
                <form
                    class="grid gap-4"
                    novalidate
                    @submit.prevent="saveReferences()"
                >
                    <fieldset
                        :disabled="!draft || saving || processing"
                        class="grid gap-4"
                    >
                        <div v-if="!individualReferences" class="grid gap-2">
                            <Label for="bulk-references">{{
                                t('wizardUx.bulkReferences')
                            }}</Label>
                            <Textarea
                                id="bulk-references"
                                v-model="bulkText"
                                rows="12"
                                :placeholder="t('wizardUx.oneReferencePerLine')"
                                aria-describedby="bulk-references-help"
                            />
                            <p
                                id="bulk-references-help"
                                class="text-xs text-muted-foreground"
                            >
                                {{ t('wizardUx.oneReferencePerLine') }}
                            </p>
                        </div>
                        <Button
                            type="button"
                            variant="outline"
                            @click="toggleReferences"
                            >{{ t('wizardUx.individualReferences') }}</Button
                        >
                        <div v-show="individualReferences" class="grid gap-4">
                            <div
                                v-for="(ref, i) in referenceForm.references"
                                :key="ref.id ?? i"
                                class="grid gap-3 border border-border p-4"
                            >
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold">
                                        {{ c('reference') }} {{ i + 1 }}
                                    </h3>
                                    <Button
                                        v-if="draft"
                                        size="sm"
                                        type="button"
                                        variant="outline"
                                        @click="
                                            referenceForm.references.splice(
                                                i,
                                                1,
                                            )
                                        "
                                        ><PhTrash class="size-4" />{{
                                            c('remove')
                                        }}</Button
                                    >
                                </div>
                                <Label :for="'citation-' + i">{{
                                    c('citation')
                                }}</Label>
                                <Textarea
                                    :id="'citation-' + i"
                                    v-model="ref.raw_citation"
                                    required
                                    rows="3"
                                />
                                <Label :for="'doi-' + i">DOI</Label
                                ><Input
                                    :id="'doi-' + i"
                                    :model-value="ref.doi ?? ''"
                                    @update:model-value="
                                        (value) => {
                                            ref.doi = String(value);
                                        }
                                    "
                                />
                                <Label :for="'url-' + i">URL</Label
                                ><Input
                                    :id="'url-' + i"
                                    :model-value="ref.url ?? ''"
                                    @update:model-value="
                                        (value) => {
                                            ref.url = String(value);
                                        }
                                    "
                                    type="url"
                                />
                            </div>
                            <Button
                                v-if="draft"
                                type="button"
                                variant="outline"
                                @click="
                                    referenceForm.references.push({
                                        id: null,
                                        raw_citation: '',
                                        doi: null,
                                        url: null,
                                    })
                                "
                            >
                                <PhPlus class="size-4" />
                                {{ c('addReference') }}</Button
                            >
                        </div>
                    </fieldset>
                    <p
                        v-if="referenceForm.hasErrors"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ c('formError') }}
                    </p>
                    <Button
                        v-if="draft"
                        type="submit"
                        :disabled="referenceForm.processing"
                        >{{ c('saveReferences') }}</Button
                    >
                </form>
            </CardContent>
        </Card>

        <Card v-show="step === 2">
            <CardHeader
                ><CardTitle>{{ t('wizardUx.files') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('fileHelp') }}
                </p></CardHeader
            >
            <CardContent class="grid gap-4">
                <ul class="text-sm">
                    <li
                        v-for="file in files"
                        :key="file.id"
                        class="border-b py-2"
                    >
                        {{ c('version') }} {{ file.version }} · {{ file.name }}
                    </li>
                </ul>
                <form
                    v-if="draft"
                    class="grid gap-3"
                    @submit.prevent="saveFile()"
                >
                    <Label for="abstract-document">{{ c('chooseFile') }}</Label>
                    <Input
                        id="abstract-document"
                        type="file"
                        accept=".pdf,.docx"
                        required
                        @change="uploadFile"
                    />
                    <p
                        v-if="fileForm.errors.file"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ c('fileError') }}
                    </p>
                    <Button
                        type="submit"
                        :disabled="!fileForm.file || fileForm.processing"
                    >
                        <PhUploadSimple class="size-4" />
                        {{ c('uploadFile') }}</Button
                    >
                </form>
            </CardContent>
        </Card>

        <Card v-show="step === 5">
            <CardHeader
                ><CardTitle>{{ t('wizardUx.review') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('submitHelp') }}
                </p></CardHeader
            >
            <CardContent class="grid gap-4">
                <section class="grid gap-4" :aria-label="t('wizardUx.review')">
                    <div class="flex justify-between gap-3">
                        <h3 class="font-medium">{{ t('wizardUx.details') }}</h3>
                        <Button
                            v-if="draft"
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="emit('edit', 1)"
                            >{{ t('wizardUx.edit') }}</Button
                        >
                    </div>
                    <h4 class="text-base font-medium" dir="auto">
                        {{ details.title }}
                    </h4>
                    <p v-if="details.subtitle" dir="auto">
                        {{ details.subtitle }}
                    </p>
                    <p class="leading-7 whitespace-pre-wrap" dir="auto">
                        {{ details.abstract }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-for="keyword in details.keywords"
                            :key="keyword"
                            variant="secondary"
                            ><bdi>{{ keyword }}</bdi></Badge
                        >
                    </div>
                    <p>
                        {{ details.primaryLocale.toUpperCase() }}
                        <span aria-hidden="true"> · </span>
                        {{ details.track }}
                    </p>
                    <div class="flex justify-between gap-3">
                        <h3 class="font-medium">{{ t('wizardUx.files') }}</h3>
                        <Button
                            v-if="draft"
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="emit('edit', 2)"
                            >{{ t('wizardUx.edit') }}</Button
                        >
                    </div>
                    <p v-for="file in files" :key="file.id">
                        <bdi>{{ file.name }}</bdi>
                        <span aria-hidden="true"> · </span>{{ c('version') }}
                        {{ file.version }}
                    </p>
                    <div class="flex justify-between gap-3">
                        <h3 class="font-medium">
                            {{ t('wizardUx.contributors') }}
                        </h3>
                        <Button
                            v-if="draft"
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="emit('edit', 3)"
                            >{{ t('wizardUx.edit') }}</Button
                        >
                    </div>
                    <ol class="grid gap-3">
                        <li
                            v-for="(author, i) in contributorForm.contributors"
                            :key="author.id ?? i"
                            class="border-s-2 border-border ps-3"
                        >
                            <p>
                                <bdi>{{ author.display_name }}</bdi
                                ><span
                                    v-if="author.is_corresponding"
                                    class="ms-2 text-primary"
                                    >{{ c('corresponding') }}</span
                                >
                            </p>
                            <p>
                                <bdi>{{ author.email }}</bdi>
                                <span aria-hidden="true"> · </span>
                                {{ author.country_code
                                }}<span v-if="author.orcid">
                                    <span aria-hidden="true"> · </span>ORCID:
                                    <bdi>{{ author.orcid }}</bdi></span
                                >
                            </p>
                            <p v-for="(aff, j) in author.affiliations" :key="j">
                                <bdi>{{ aff.institution_name }}</bdi
                                ><span v-if="aff.subdivision">
                                    <span aria-hidden="true"> · </span
                                    ><bdi>{{ aff.subdivision }}</bdi></span
                                >
                                <span aria-hidden="true"> · </span
                                >{{ aff.city }} {{ aff.country_code
                                }}<span
                                    v-if="aff.institution_id"
                                    class="ms-2 text-muted-foreground"
                                    >{{
                                        aff.ror_uri ??
                                        t('submissionWizard.rorSelected')
                                    }}</span
                                >
                            </p>
                        </li>
                    </ol>
                    <div class="flex justify-between gap-3">
                        <h3 class="font-medium">
                            {{ t('wizardUx.references') }}
                        </h3>
                        <Button
                            v-if="draft"
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="emit('edit', 4)"
                            >{{ t('wizardUx.edit') }}</Button
                        >
                    </div>
                    <ol class="list-inside list-decimal space-y-2">
                        <li
                            v-for="(reference, i) in individualReferences
                                ? referenceForm.references
                                : bulkReferences(
                                      bulkText,
                                      referenceForm.references,
                                  )"
                            :key="reference.id ?? i"
                            class="break-words"
                        >
                            <bdi>{{ reference.raw_citation }}</bdi
                            ><span v-if="reference.doi">
                                <span aria-hidden="true"> · </span>DOI:
                                <bdi>{{ reference.doi }}</bdi></span
                            ><span v-if="reference.url">
                                <span aria-hidden="true"> · </span
                                ><bdi>{{ reference.url }}</bdi></span
                            >
                        </li>
                    </ol>
                </section>
                <div
                    v-if="readiness"
                    role="status"
                    class="border border-border p-4 text-sm"
                >
                    <p class="font-semibold">
                        {{ c('readiness') }}:
                        {{
                            t(
                                blocked
                                    ? 'wizardUx.needsCorrection'
                                    : 'wizardUx.readyToSubmit',
                            )
                        }}
                    </p>
                    <p
                        v-for="(item, i) in configuration"
                        :key="item.code + i"
                        class="mt-3 text-muted-foreground"
                    >
                        {{ finding(item) }}
                    </p>
                    <ul
                        v-if="blockers.length"
                        class="mt-3 list-inside list-disc"
                    >
                        <li
                            v-for="(item, i) in blockers"
                            :key="i"
                            class="text-destructive"
                        >
                            {{ finding(item) }}
                            <Button
                                v-if="draft && findingStep(item) !== null"
                                type="button"
                                variant="outline"
                                size="sm"
                                class="ms-2 mt-2"
                                :disabled="saving || processing"
                                @click="emit('edit', findingStep(item)!)"
                                >{{ fixLabel(item) }}</Button
                            >
                        </li>
                    </ul>
                </div>
                <p v-else role="status" class="text-sm text-muted-foreground">
                    {{ c('alreadySubmitted') }}
                </p>
                <p
                    v-if="submitError"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{ c('submitError') }}
                </p>
                <form v-if="draft" @submit.prevent="emit('prepare-submit')">
                    <Button
                        type="submit"
                        :disabled="blocked || saving || processing"
                        >{{ c('submitAction') }}</Button
                    >
                    <p class="mt-2 text-xs text-muted-foreground">
                        {{ c('submitConfirmation') }}
                    </p>
                </form>
            </CardContent>
        </Card>
        <Dialog
            :open="confirmOpen"
            @update:open="
                (value) => {
                    if (!submitForm.processing) confirmOpen = value;
                }
            "
        >
            <DialogContent :show-close-button="false"
                ><DialogHeader
                    ><DialogTitle>{{ c('submitAction') }}</DialogTitle
                    ><DialogDescription>{{
                        c('submitConfirmation')
                    }}</DialogDescription></DialogHeader
                >
                <p
                    v-if="submitError"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{ c('submitError') }}
                </p>
                <DialogFooter
                    ><Button
                        type="button"
                        variant="outline"
                        :disabled="submitForm.processing"
                        @click="confirmOpen = false"
                        >{{ t('wizardUx.cancel') }}</Button
                    ><Button
                        type="button"
                        :disabled="blocked || submitForm.processing"
                        @click="officialSubmit"
                        >{{
                            submitForm.processing
                                ? t('wizardUx.saving')
                                : c('submitAction')
                        }}</Button
                    ></DialogFooter
                >
            </DialogContent>
        </Dialog>
        <Dialog
            :open="deletionOpen"
            @update:open="
                (value) => {
                    if (!value) resolveReferences(false);
                }
            "
        >
            <DialogContent :show-close-button="false"
                ><DialogHeader
                    ><DialogTitle>{{
                        t('wizardUx.confirmReferenceRemoval')
                    }}</DialogTitle
                    ><DialogDescription>{{
                        t('wizardUx.referenceRemovalHelp')
                    }}</DialogDescription></DialogHeader
                >
                <ul class="max-h-48 overflow-y-auto text-sm">
                    <li
                        v-for="reference in removed"
                        :key="reference.id ?? reference.raw_citation"
                    >
                        {{ reference.raw_citation }}
                    </li>
                </ul>
                <DialogFooter
                    ><Button
                        type="button"
                        variant="outline"
                        @click="resolveReferences(false)"
                        >{{ t('wizardUx.cancel') }}</Button
                    ><Button
                        type="button"
                        variant="destructive"
                        @click="resolveReferences(true)"
                        >{{ t('wizardUx.confirmChanges') }}</Button
                    ></DialogFooter
                >
            </DialogContent>
        </Dialog>
    </div>
</template>
