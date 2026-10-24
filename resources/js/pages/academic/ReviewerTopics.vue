<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { PhSpinnerGap, PhPlus, PhPencilSimple } from '@phosphor-icons/vue';
import { reviewerTopicCopy } from '@/i18n/reviewerTopics';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import DataTablePagination from '@/components/DataTablePagination.vue';
import type { PaginationMeta } from '@/types/pagination';

type Reviewer = {
    id: string;
    name: string;
    email: string;
    verified: boolean;
    topics: { id: string; code: string }[];
    hasHistoricalTopics: boolean;
    stateToken: string;
};
const props = defineProps<{
    editions: { id: string; code: string }[];
    editionId: string | null;
    tracks: { id: string; code: string; name_i18n: Record<string, string> }[];
    reviewers: Reviewer[];
    reviewerOptions: { id: string; name: string; email: string }[];
    pagination: PaginationMeta;
    saved: boolean;
}>();
const { locale, t } = useI18n();
const c = computed(() => reviewerTopicCopy(locale.value));
const selection = useForm({ edition_id: props.editionId ?? '' });
const form = useForm({
    reviewer_id: null as string | null,
    track_ids: [] as string[],
    expected_state: 'new',
});
const editorOpen = ref(false);
const editing = ref<Reviewer | null>(null);
const busy = computed(() => form.processing || selection.processing);
const stale = computed(() => !!form.errors.expected_state);
const topicError = computed(() =>
    Object.keys(form.errors).some((key) => key.startsWith('track_ids')),
);
watch(
    () => props.editionId,
    (id) => {
        selection.edition_id = id ?? '';
        form.reset();
        form.clearErrors();
        editorOpen.value = false;
        editing.value = null;
    },
);
function topicName(names: Record<string, string>) {
    return names[locale.value] ?? names.en ?? '';
}
function openEditor(reviewer: Reviewer | null = null) {
    if (busy.value) return;
    form.reset();
    form.clearErrors();
    editing.value = reviewer;
    form.reviewer_id = reviewer?.id ?? null;
    form.track_ids = reviewer?.topics.map((topic) => topic.id) ?? [];
    form.expected_state = reviewer?.stateToken ?? 'new';
    editorOpen.value = true;
}
function toggleTopic(id: string, selected: boolean) {
    form.track_ids = selected
        ? [...new Set([...form.track_ids, id])]
        : form.track_ids.filter((value) => value !== id);
}
function save() {
    if (busy.value || stale.value || !props.editionId || !form.reviewer_id)
        return;
    form.transform((data) => ({ ...data, edition_id: props.editionId })).put(
        '/academic/reviewer-topics',
        {
            preserveScroll: true,
            onSuccess: () => {
                editorOpen.value = false;
            },
        },
    );
}
function reload() {
    if (busy.value) return;
    editorOpen.value = false;
    router.reload();
}
</script>

<template>
    <Head :title="c.title" />
    <div class="grid w-full min-w-0 gap-6 p-4 [overflow-wrap:anywhere] md:p-8">
        <header class="space-y-2">
            <Link href="/academic" class="admin-text-link text-sm">{{
                t('academic.title')
            }}</Link>
            <h1 class="text-2xl font-semibold tracking-tight">{{ c.title }}</h1>
            <p class="text-sm text-muted-foreground">{{ c.intro }}</p>
        </header>
        <form
            class="grid min-w-0 gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end"
            :aria-busy="selection.processing"
            @submit.prevent="
                !busy && selection.post('/academic/reviewer-topics/edition')
            "
        >
            <div class="grid min-w-0 gap-2">
                <Label for="topic-edition">{{ c.edition }}</Label>
                <Select v-model="selection.edition_id" :disabled="busy">
                    <SelectTrigger id="topic-edition" class="w-full"
                        ><SelectValue :placeholder="c.edition"
                    /></SelectTrigger>
                    <SelectContent
                        ><SelectItem
                            v-for="edition in editions"
                            :key="edition.id"
                            :value="edition.id"
                            >{{ edition.code }}</SelectItem
                        ></SelectContent
                    >
                </Select>
            </div>
            <Button :disabled="busy || !selection.edition_id">{{
                c.open
            }}</Button>
        </form>
        <p
            v-if="selection.hasErrors"
            role="alert"
            class="text-sm text-destructive"
        >
            {{ c.invalid }}
        </p>
        <p v-if="saved" role="status" class="text-sm text-success">
            {{ c.saved }}
        </p>
        <p v-if="!editionId" class="text-sm text-muted-foreground">
            {{ c.chooseEdition }}
        </p>
        <section v-else class="min-w-0 space-y-4" :aria-label="c.reviewer">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">{{ c.reviewer }}</h2>
                <Button
                    type="button"
                    :disabled="busy || !tracks.length"
                    @click="openEditor()"
                    ><PhPlus class="size-4" aria-hidden="true" />{{
                        c.addReviewer
                    }}</Button
                >
            </div>
            <p v-if="!tracks.length" class="text-sm text-muted-foreground">
                {{ c.noTopics }}
            </p>
            <p v-if="!reviewers.length" class="text-sm text-muted-foreground">
                {{ c.empty }}
            </p>
            <div v-else class="overflow-x-auto">
                <table class="admin-data-table w-full text-start text-sm">
                    <caption class="sr-only">
                        {{
                            c.title
                        }}
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col" class="p-3 text-start">
                                {{ c.reviewer }}
                            </th>
                            <th scope="col" class="p-3 text-start">
                                {{ c.topics }}
                            </th>
                            <th scope="col" class="p-3 text-end">
                                <span class="sr-only">{{ c.edit }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="reviewer in reviewers"
                            :key="reviewer.id"
                            class="border-t"
                        >
                            <th scope="row" class="p-3 text-start font-normal">
                                <bdi class="font-medium">{{
                                    reviewer.name
                                }}</bdi>
                                <div class="text-muted-foreground">
                                    <bdi>{{ reviewer.email }}</bdi>
                                </div>
                                <Badge
                                    v-if="!reviewer.verified"
                                    variant="destructive"
                                    >{{ c.unverified }}</Badge
                                >
                            </th>
                            <td class="p-3">
                                <div class="flex flex-wrap gap-2">
                                    <Badge
                                        v-for="topic in reviewer.topics"
                                        :key="topic.id"
                                        variant="secondary"
                                        ><bdi>{{ topic.code }}</bdi></Badge
                                    ><span
                                        v-if="!reviewer.topics.length"
                                        class="text-muted-foreground"
                                        >{{ c.noActiveTopics }}</span
                                    >
                                </div>
                            </td>
                            <td class="p-3 text-end">
                                <Button
                                    type="button"
                                    variant="outline"
                                    :disabled="busy"
                                    :aria-label="`${c.edit}: ${reviewer.name}`"
                                    @click="openEditor(reviewer)"
                                    ><PhPencilSimple
                                        class="size-4"
                                        aria-hidden="true"
                                    />{{ c.edit }}</Button
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <DataTablePagination :pagination="pagination" />
        </section>
        <Dialog
            :open="editorOpen"
            @update:open="
                (open) => {
                    if (!busy) editorOpen = open;
                }
            "
        >
            <DialogContent
                class="max-h-[90dvh] overflow-y-auto"
                :dir="locale.startsWith('ar') ? 'rtl' : 'ltr'"
                :show-close-button="!busy"
                @escape-key-down="
                    (event) => {
                        if (busy) event.preventDefault();
                    }
                "
                @interact-outside="
                    (event) => {
                        if (busy) event.preventDefault();
                    }
                "
            >
                <DialogHeader
                    ><DialogTitle>{{
                        editing ? c.editReviewer : c.addReviewer
                    }}</DialogTitle
                    ><DialogDescription>{{
                        c.editorHelp
                    }}</DialogDescription></DialogHeader
                >
                <form
                    class="grid min-w-0 gap-5"
                    :aria-busy="form.processing"
                    @submit.prevent="save"
                >
                    <div class="grid gap-2">
                        <template v-if="editing">
                            <p class="text-sm font-medium">{{ c.reviewer }}</p>
                            <p class="text-sm">
                                <bdi>{{ editing.name }}</bdi> ·
                                <bdi>{{ editing.email }}</bdi>
                            </p>
                        </template>
                        <template v-else-if="reviewerOptions.length">
                            <Label for="reviewer-topic-account">{{
                                c.account
                            }}</Label>
                            <Select
                                v-model="form.reviewer_id"
                                :disabled="busy || stale"
                            >
                                <SelectTrigger
                                    id="reviewer-topic-account"
                                    class="w-full"
                                    :aria-invalid="!!form.errors.reviewer_id"
                                    aria-describedby="reviewer-topic-error"
                                >
                                    <SelectValue
                                        :placeholder="c.chooseReviewer"
                                    />
                                </SelectTrigger>
                                <SelectContent
                                    :dir="
                                        locale.startsWith('ar') ? 'rtl' : 'ltr'
                                    "
                                >
                                    <SelectItem
                                        v-for="account in reviewerOptions"
                                        :key="account.id"
                                        :value="account.id"
                                        class="[overflow-wrap:anywhere] whitespace-normal"
                                    >
                                        <bdi>{{ account.name }}</bdi> ·
                                        <bdi>{{ account.email }}</bdi>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </template>
                        <p
                            v-else
                            role="status"
                            class="text-sm text-muted-foreground"
                        >
                            {{ c.noReviewers }}
                        </p>
                        <p
                            v-if="form.errors.reviewer_id"
                            id="reviewer-topic-error"
                            role="alert"
                            class="text-sm text-destructive"
                        >
                            {{ c.reviewerInvalid }}
                        </p>
                    </div>
                    <p
                        v-if="editing?.hasHistoricalTopics"
                        class="text-sm text-muted-foreground"
                    >
                        {{ c.historicalHelp }}
                    </p>
                    <fieldset
                        class="grid min-w-0 gap-3"
                        :disabled="busy || stale"
                        :aria-invalid="topicError"
                        aria-describedby="reviewer-topics-error"
                    >
                        <legend class="mb-3 text-sm font-medium">
                            {{ c.topics }}
                        </legend>
                        <label
                            v-for="track in tracks"
                            :key="track.id"
                            class="flex min-h-10 min-w-0 items-start gap-3"
                        >
                            <Checkbox
                                :id="`reviewer-topic-${track.id}`"
                                :model-value="form.track_ids.includes(track.id)"
                                :disabled="busy || stale"
                                @update:model-value="
                                    (value) =>
                                        toggleTopic(track.id, value === true)
                                "
                            />
                            <span class="min-w-0 text-sm"
                                ><bdi class="font-semibold">{{
                                    track.code
                                }}</bdi>
                                · {{ topicName(track.name_i18n) }}</span
                            >
                        </label>
                    </fieldset>
                    <p
                        v-if="topicError"
                        id="reviewer-topics-error"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ c.topicInvalid }}
                    </p>
                    <p
                        v-if="stale"
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ c.stale }}
                    </p>
                    <p
                        v-else-if="
                            form.hasErrors &&
                            !form.errors.reviewer_id &&
                            !topicError
                        "
                        role="alert"
                        class="text-sm text-destructive"
                    >
                        {{ c.invalid }}
                    </p>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="busy"
                            @click="editorOpen = false"
                            >{{ c.cancel }}</Button
                        >
                        <Button
                            v-if="stale"
                            type="button"
                            :disabled="busy"
                            @click="reload"
                            >{{ c.reload }}</Button
                        >
                        <Button
                            v-else
                            type="submit"
                            :disabled="
                                busy ||
                                !form.reviewer_id ||
                                (!editing && !form.track_ids.length)
                            "
                            ><PhSpinnerGap
                                v-if="form.processing"
                                class="size-4 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            />{{ form.processing ? c.loading : c.save }}</Button
                        >
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
