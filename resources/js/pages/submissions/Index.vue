<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { PhPencilSimple, PhTrash, PhSpinnerGap } from '@phosphor-icons/vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import { submissionPolicyCopy } from '@/i18n/submissionPolicy';
import { useI18n } from 'vue-i18n';
import { submissionCopy } from '@/lib/submissionCopy';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Registration = {
    id: string;
    code: string;
    edition: string;
    year: number;
    package: string;
    canCreateDraft: boolean;
};
type SubmissionSummary = {
    id: string;
    paperCode: string;
    edition: string;
    title: string | null;
    status: string;
    detailsUrl: string;
    destroyUrl: string | null;
};

const props = defineProps<{
    registrations: Registration[];
    editionLinks: { code: string; year: number; registrationUrl: string }[];
    submissions: SubmissionSummary[];
    routes: { store: string; dashboard: string };
}>();

const { locale } = useI18n();
const c = computed(() => submissionCopy(String(locale.value)));
const policy = computed(() => submissionPolicyCopy(String(locale.value)));
const eligible = computed(() =>
    props.registrations.filter((registration) => registration.canCreateDraft),
);
const form = useForm({
    registration_id: eligible.value.length === 1 ? eligible.value[0].id : '',
    primary_locale: 'id',
});
const selectedRegistration = computed(() =>
    props.registrations.find(
        (registration) => registration.id === form.registration_id,
    ),
);
watch(
    () => props.registrations,
    (registrations) => {
        if (
            !registrations.some(
                (registration) =>
                    registration.id === form.registration_id &&
                    registration.canCreateDraft,
            )
        ) {
            form.registration_id =
                eligible.value.length === 1 ? eligible.value[0].id : '';
        }
    },
);

function createDraft(): void {
    if (form.processing || !selectedRegistration.value?.canCreateDraft) return;
    form.post(props.routes.store, { preserveScroll: true });
}

const pendingDelete = ref<SubmissionSummary | null>(null);
const deletion = useForm({});
function openDelete(item: SubmissionSummary) {
    if (deletion.processing || item.status !== 'DRAFT' || !item.destroyUrl)
        return;
    deletion.clearErrors();
    pendingDelete.value = item;
}
function deleteDraft() {
    if (deletion.processing || !pendingDelete.value?.destroyUrl) return;
    deletion.delete(pendingDelete.value.destroyUrl, {
        preserveScroll: true,
        onSuccess: () => {
            pendingDelete.value = null;
        },
    });
}
</script>

<template>
    <Head :title="c.index" />
    <div
        class="flex w-full min-w-0 flex-col gap-6 p-4 [overflow-wrap:anywhere] md:p-8"
    >
        <header class="space-y-1">
            <Link
                :href="routes.dashboard"
                class="text-sm text-muted-foreground underline-offset-4 hover:underline"
            >
                {{ c.back }}
            </Link>
            <h1 class="text-2xl font-semibold tracking-tight">{{ c.index }}</h1>
            <p class="text-sm text-muted-foreground">{{ c.indexHint }}</p>
        </header>

        <Card class="min-w-0 p-4" aria-labelledby="create-draft-title">
            <h2 id="create-draft-title" class="text-lg font-semibold">
                {{ c.create }}
            </h2>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ policy.onePerEdition }}
            </p>
            <div
                v-if="registrations.length === 0"
                class="mt-3 min-w-0 space-y-3"
            >
                <p class="text-sm text-muted-foreground">
                    {{ c.noRegistrations }} {{ c.noRegistrationsHelp }}
                </p>
                <div
                    v-if="editionLinks.length > 0"
                    class="flex flex-wrap gap-2"
                >
                    <Button
                        class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                        v-for="edition in editionLinks"
                        :key="edition.registrationUrl"
                        as-child
                        variant="outline"
                    >
                        <Link :href="edition.registrationUrl">
                            {{ c.editionLink }} · {{ edition.code }}
                            {{ edition.year }}
                        </Link>
                    </Button>
                </div>
            </div>
            <form
                v-else
                class="mt-4 flex min-w-0 flex-col gap-4"
                @submit.prevent="createDraft"
            >
                <div class="grid min-w-0 gap-4 sm:grid-cols-2">
                    <div class="min-w-0 space-y-2">
                        <label
                            for="registration-id"
                            class="text-sm font-medium"
                            >{{ c.chooseRegistration }}</label
                        >
                        <Select
                            v-model="form.registration_id"
                            name="registration_id"
                            :disabled="form.processing"
                        >
                            <SelectTrigger
                                id="registration-id"
                                class="w-full"
                                :aria-invalid="!!form.errors.registration_id"
                                aria-describedby="registration-error"
                            >
                                <SelectValue
                                    :placeholder="c.chooseRegistration"
                                >
                                    <span
                                        v-if="selectedRegistration"
                                        class="truncate"
                                        ><bdi>{{
                                            selectedRegistration.edition
                                        }}</bdi>
                                        ·
                                        <bdi>{{
                                            selectedRegistration.code
                                        }}</bdi></span
                                    >
                                    <span v-else>{{
                                        c.chooseRegistration
                                    }}</span>
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="registration in registrations"
                                    :key="registration.id"
                                    :value="registration.id"
                                    :disabled="!registration.canCreateDraft"
                                    :text-value="`${registration.edition} · ${registration.code}`"
                                >
                                    <span class="grid gap-1 text-start"
                                        ><span
                                            ><bdi>{{
                                                registration.edition
                                            }}</bdi>
                                            · {{ registration.year }}</span
                                        ><span
                                            class="text-xs text-muted-foreground"
                                            ><bdi>{{ registration.code }}</bdi>
                                            · {{ registration.package }}</span
                                        ></span
                                    >
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="min-w-0 space-y-2">
                        <label
                            for="primary-locale"
                            class="text-sm font-medium"
                            >{{ c.chooseLocale }}</label
                        >
                        <Select
                            v-model="form.primary_locale"
                            name="primary_locale"
                            :disabled="form.processing"
                            ><SelectTrigger
                                id="primary-locale"
                                class="w-full"
                                :aria-invalid="!!form.errors.primary_locale"
                                aria-describedby="locale-error"
                                ><SelectValue /></SelectTrigger
                            ><SelectContent
                                ><SelectItem value="id"
                                    >Bahasa Indonesia</SelectItem
                                ><SelectItem value="en">English</SelectItem
                                ><SelectItem value="ar"
                                    >العربية</SelectItem
                                ></SelectContent
                            ></Select
                        >
                    </div>
                </div>
                <p
                    v-if="form.errors.registration_id"
                    id="registration-error"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{
                        form.errors.registration_id ===
                        'SUBMISSION_ALREADY_EXISTS'
                            ? policy.alreadyExists
                            : form.errors.registration_id ===
                                'REGISTRATION_CONTEXT_INVALID'
                              ? c.invalidRegistration
                              : form.errors.registration_id
                    }}
                </p>
                <p
                    v-if="form.errors.primary_locale"
                    id="locale-error"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{ form.errors.primary_locale }}
                </p>
                <div>
                    <Button
                        class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                        type="submit"
                        :disabled="
                            form.processing ||
                            !selectedRegistration?.canCreateDraft
                        "
                        >{{ c.create }}</Button
                    >
                </div>
            </form>
        </Card>

        <section
            aria-labelledby="submissions-heading"
            class="min-w-0 space-y-3"
        >
            <h2 id="submissions-heading" class="text-lg font-semibold">
                {{ c.index }}
            </h2>
            <Card
                v-if="submissions.length === 0"
                class="min-w-0 border-dashed p-4 text-center"
            >
                <h3 class="font-semibold">{{ c.noSubmissions }}</h3>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ c.noSubmissionsHelp }}
                </p>
            </Card>
            <ul v-else class="grid gap-3">
                <li v-for="item in submissions" :key="item.id">
                    <Card class="min-w-0 p-4">
                        <div
                            class="flex min-w-0 flex-wrap items-center justify-between gap-3"
                        >
                            <div class="min-w-0 space-y-1">
                                <p
                                    class="font-mono text-xs text-muted-foreground"
                                    dir="ltr"
                                >
                                    {{ item.paperCode }}
                                </p>
                                <h3 class="font-medium">
                                    {{ item.title || c.details }}
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    {{ item.edition }} ·
                                    {{
                                        item.status === 'DRAFT'
                                            ? c.draft
                                            : item.status === 'SUBMITTED'
                                              ? c.submitted
                                              : item.status
                                    }}
                                </p>
                            </div>
                            <div class="flex min-w-0 flex-wrap gap-2">
                                <Button
                                    class="h-auto min-h-10 max-w-full min-w-0 whitespace-normal"
                                    as-child
                                    variant="outline"
                                    ><Link :href="item.detailsUrl"
                                        ><PhPencilSimple
                                            v-if="item.status === 'DRAFT'"
                                            class="size-4 shrink-0"
                                            aria-hidden="true"
                                        />{{
                                            item.status === 'DRAFT'
                                                ? policy.edit
                                                : policy.view
                                        }}</Link
                                    ></Button
                                >
                                <Button
                                    v-if="
                                        item.status === 'DRAFT' &&
                                        item.destroyUrl
                                    "
                                    type="button"
                                    variant="destructive"
                                    class="h-auto min-h-10 max-w-full whitespace-normal"
                                    :disabled="deletion.processing"
                                    @click="openDelete(item)"
                                >
                                    <PhTrash
                                        class="size-4 shrink-0"
                                        aria-hidden="true"
                                    />{{ policy.remove }}
                                </Button>
                            </div>
                        </div>
                    </Card>
                </li>
            </ul>
        </section>
        <Dialog
            :open="pendingDelete !== null"
            @update:open="
                (open) => {
                    if (!open && !deletion.processing) pendingDelete = null;
                }
            "
        >
            <DialogContent :aria-busy="deletion.processing">
                <DialogHeader>
                    <DialogTitle>{{ policy.confirmTitle }}</DialogTitle>
                    <DialogDescription>{{
                        policy.confirmDescription
                    }}</DialogDescription>
                </DialogHeader>
                <p class="min-w-0 text-sm [overflow-wrap:anywhere]">
                    <bdi>{{ pendingDelete?.paperCode }}</bdi>
                </p>
                <p
                    v-if="deletion.hasErrors"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{ policy.deleteFailed }}
                </p>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        :disabled="deletion.processing"
                        @click="pendingDelete = null"
                        >{{ policy.cancel }}</Button
                    >
                    <Button
                        type="button"
                        variant="destructive"
                        :disabled="deletion.processing"
                        @click="deleteDraft"
                    >
                        <PhSpinnerGap
                            v-if="deletion.processing"
                            class="size-4 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />
                        <PhTrash v-else class="size-4" aria-hidden="true" />{{
                            deletion.processing
                                ? policy.deleting
                                : policy.remove
                        }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
