<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { PhPlus, PhTrash, PhUploadSimple } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InstitutionLookup from '@/components/submissions/InstitutionLookup.vue';

type Aff = {
    institution_id: string | null;
    institution_name: string;
    subdivision: string | null;
    city: string | null;
    country_code: string;
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
    contributors: Author[];
    references: Ref[];
    files: { id: string; version: number; name: string | null }[];
    readiness: {
        status: string;
        findings: { code: string; field: string; severity: string }[];
    } | null;
    routes: {
        contributors: string;
        references: string;
        upload: string;
        submit: string;
    };
}>();
const { t, te } = useI18n();
const c = (key: string) => t('submissionWizard.' + key);
const contributorForm = useForm({
    contributors: props.contributors.map((x) => ({
        ...x,
        affiliations: x.affiliations.map((a) => ({ ...a })),
    })),
});
const referenceForm = useForm({
    references: props.references.map((x) => ({ ...x })),
});
const fileForm = useForm({ file: null as File | null });
const submitForm = useForm({});
const submitError = computed(
    () => (submitForm.errors as Record<string, string | undefined>).submit,
);
const blocked = computed(
    () => props.readiness === null || props.readiness.status === 'BLOCKED',
);
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
}
function corresponding(i: number) {
    contributorForm.contributors.forEach((a, index) => {
        a.is_corresponding = index === i;
    });
}
function uploadFile(event: Event) {
    fileForm.file = (event.target as HTMLInputElement).files?.[0] ?? null;
}
function finding(code: string) {
    return te('submissionWizard.findings.' + code)
        ? t('submissionWizard.findings.' + code)
        : code.replaceAll('_', ' ');
}
</script>

<template>
    <div class="grid w-full max-w-5xl gap-6">
        <Card>
            <CardHeader
                ><CardTitle>{{ c('contributors') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('contributorsHelp') }}
                </p>
            </CardHeader>
            <CardContent>
                <form
                    class="grid gap-4"
                    @submit.prevent="
                        contributorForm.put(routes.contributors, {
                            preserveScroll: true,
                        })
                    "
                >
                    <fieldset
                        :disabled="!draft || contributorForm.processing"
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
                                                author.orcid = String(value);
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
                                        :model-value="author.given_name ?? ''"
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
                                        :model-value="author.family_name ?? ''"
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
                                        <Label :for="'inst-' + i + '-' + j">{{
                                            c('institution')
                                        }}</Label>
                                        <InstitutionLookup
                                            :input-id="'inst-' + i + '-' + j"
                                            :value="aff.institution_name"
                                            :selected-id="aff.institution_id"
                                            :disabled="
                                                !draft ||
                                                contributorForm.processing
                                            "
                                            @manual="
                                                (value) => {
                                                    aff.institution_id = null;
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
                                            :for="'aff-country-' + i + '-' + j"
                                            >{{ c('country') }}</Label
                                        >
                                        <Input
                                            :id="'aff-country-' + i + '-' + j"
                                            v-model="aff.country_code"
                                            required
                                            maxlength="2"
                                        />
                                    </div>
                                    <div class="grid gap-1">
                                        <Label :for="'city-' + i + '-' + j">{{
                                            c('city')
                                        }}</Label>
                                        <Input
                                            :id="'city-' + i + '-' + j"
                                            :model-value="aff.city ?? ''"
                                            @update:model-value="
                                                (value) => {
                                                    aff.city = String(value);
                                                }
                                            "
                                            maxlength="255"
                                        />
                                    </div>
                                    <div class="grid gap-1">
                                        <Label :for="'dept-' + i + '-' + j">{{
                                            c('department')
                                        }}</Label>
                                        <Input
                                            :id="'dept-' + i + '-' + j"
                                            :model-value="aff.subdivision ?? ''"
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
                                        draft && author.affiliations.length > 1
                                    "
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    @click="author.affiliations.splice(j, 1)"
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

        <Card>
            <CardHeader
                ><CardTitle>{{ c('references') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('referencesHelp') }}
                </p></CardHeader
            >
            <CardContent>
                <form
                    class="grid gap-4"
                    @submit.prevent="
                        referenceForm.put(routes.references, {
                            preserveScroll: true,
                        })
                    "
                >
                    <fieldset
                        :disabled="!draft || referenceForm.processing"
                        class="grid gap-4"
                    >
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
                                        referenceForm.references.splice(i, 1)
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

        <Card>
            <CardHeader
                ><CardTitle>{{ c('file') }}</CardTitle>
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
                    @submit.prevent="
                        fileForm.post(routes.upload, {
                            forceFormData: true,
                            preserveScroll: true,
                            onSuccess: () => fileForm.reset(),
                        })
                    "
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

        <Card>
            <CardHeader
                ><CardTitle>{{ c('submitHeading') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ c('submitHelp') }}
                </p></CardHeader
            >
            <CardContent class="grid gap-4">
                <div
                    v-if="readiness"
                    role="status"
                    class="border border-border p-4 text-sm"
                >
                    <p class="font-semibold">
                        {{ c('readiness') }}: {{ readiness.status }}
                    </p>
                    <ul
                        v-if="readiness.findings.length"
                        class="mt-3 list-inside list-disc"
                    >
                        <li v-for="(item, i) in readiness.findings" :key="i">
                            {{ finding(item.code) }} ({{ item.severity }})
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
                <form
                    v-if="draft"
                    @submit.prevent="
                        submitForm.post(routes.submit, { preserveScroll: true })
                    "
                >
                    <Button
                        type="submit"
                        :disabled="blocked || submitForm.processing"
                        >{{ c('submitAction') }}</Button
                    >
                    <p class="mt-2 text-xs text-muted-foreground">
                        {{ c('submitConfirmation') }}
                    </p>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
