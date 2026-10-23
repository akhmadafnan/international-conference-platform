<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    PhSpinnerGap,
    PhPlus,
    PhProhibit,
    PhArrowCounterClockwise,
} from '@phosphor-icons/vue';
import { reviewerTopicCopy } from '@/i18n/reviewerTopics';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
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

type Eligibility = {
    id: string;
    reviewer: string;
    email: string;
    verified: boolean;
    trackId: string;
    trackCode: string;
    trackName: Record<string, string>;
    trackActive: boolean;
    active: boolean;
};
const props = defineProps<{
    editions: { id: string; code: string }[];
    editionId: string | null;
    tracks: { id: string; code: string; name_i18n: Record<string, string> }[];
    eligibilities: Eligibility[];
    pagination: PaginationMeta;
    saved: boolean;
}>();
const { locale, t } = useI18n();
const c = computed(() => reviewerTopicCopy(locale.value));
const selection = useForm({ edition_id: props.editionId ?? '' });
const form = useForm({ reviewer_email: '', track_ids: [] as string[] });
const change = useForm({});
const pending = ref<Eligibility | null>(null);
const confirmGrant = ref(false);
const selectedCodes = computed(() =>
    props.tracks
        .filter((track) => form.track_ids.includes(track.id))
        .map((track) => track.code)
        .join(', '),
);
const busy = computed(
    () => form.processing || change.processing || selection.processing,
);
watch(
    () => props.editionId,
    (id) => {
        selection.edition_id = id ?? '';
        form.reset();
        form.clearErrors();
        pending.value = null;
        confirmGrant.value = false;
    },
);
function topicName(names: Record<string, string>) {
    return names[locale.value] ?? names.en ?? '';
}
function toggleTopic(id: string, active: boolean) {
    form.track_ids = active
        ? [...new Set([...form.track_ids, id])]
        : form.track_ids.filter((value) => value !== id);
}
function grant() {
    if (busy.value || !props.editionId) return;
    form.transform((data) => ({ ...data, edition_id: props.editionId })).post(
        '/academic/reviewer-topics',
        {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                confirmGrant.value = false;
            },
        },
    );
}
function confirm(row: Eligibility) {
    if (!busy.value) {
        change.clearErrors();
        pending.value = row;
    }
}
function applyChange() {
    const row = pending.value;
    if (!row || busy.value || !props.editionId) return;
    const options = {
        preserveScroll: true,
        onSuccess: () => {
            pending.value = null;
        },
    };
    if (row.active)
        change.delete(`/academic/reviewer-topics/${row.id}`, options);
    else
        change
            .transform(() => ({
                edition_id: props.editionId,
                reviewer_email: row.email,
                track_ids: [row.trackId],
                eligibility_id: row.id,
            }))
            .post('/academic/reviewer-topics', options);
}
function prepareGrant() {
    if (!busy.value && props.editionId && form.track_ids.length)
        confirmGrant.value = true;
}
function closeConfirmation() {
    if (!busy.value) {
        pending.value = null;
        confirmGrant.value = false;
    }
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
                selection.post('/academic/reviewer-topics/edition')
            "
        >
            <div class="grid min-w-0 gap-2">
                <Label for="topic-edition">{{ c.edition }}</Label>
                <Select v-model="selection.edition_id" :disabled="busy"
                    ><SelectTrigger id="topic-edition" class="w-full"
                        ><SelectValue :placeholder="c.edition" /></SelectTrigger
                    ><SelectContent>
                        <SelectItem
                            v-for="edition in editions"
                            :key="edition.id"
                            :value="edition.id"
                            >{{ edition.code }}</SelectItem
                        >
                    </SelectContent></Select
                >
            </div>
            <Button
                :disabled="busy || !selection.edition_id"
                class="h-auto min-h-10 whitespace-normal"
                ><PhSpinnerGap
                    v-if="selection.processing"
                    class="size-4 animate-spin motion-reduce:animate-none"
                    aria-hidden="true"
                />{{ c.open }}</Button
            >
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
        <template v-else>
            <Card class="min-w-0"
                ><CardHeader
                    ><CardTitle>{{ c.grant }}</CardTitle></CardHeader
                ><CardContent>
                    <p
                        v-if="!tracks.length"
                        class="text-sm text-muted-foreground"
                    >
                        {{ c.noTopics }}
                    </p>
                    <form
                        v-else
                        class="grid w-full min-w-0 gap-5"
                        :aria-busy="form.processing"
                        @submit.prevent="prepareGrant"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="reviewer-topic-email">{{
                                c.email
                            }}</Label>
                            <Input
                                id="reviewer-topic-email"
                                v-model="form.reviewer_email"
                                type="email"
                                required
                                maxlength="255"
                                autocomplete="off"
                                :disabled="busy"
                                :aria-invalid="!!form.errors.reviewer_email"
                                aria-describedby="reviewer-topic-error"
                            />
                            <p
                                v-if="form.errors.reviewer_email"
                                id="reviewer-topic-error"
                                role="alert"
                                class="text-sm text-destructive"
                            >
                                {{ c.reviewerInvalid }}
                            </p>
                        </div>
                        <fieldset
                            class="grid min-w-0 gap-3 md:grid-cols-2 xl:grid-cols-3"
                            :disabled="busy"
                        >
                            <legend class="mb-3 text-sm font-medium">
                                {{ c.topics }}
                            </legend>
                            <label
                                v-for="track in tracks"
                                :key="track.id"
                                class="flex min-h-12 min-w-0 items-start gap-3 rounded-lg border p-3"
                            >
                                <Checkbox
                                    :model-value="
                                        form.track_ids.includes(track.id)
                                    "
                                    :disabled="busy"
                                    :aria-label="track.code"
                                    @update:model-value="
                                        (value) =>
                                            toggleTopic(
                                                track.id,
                                                value === true,
                                            )
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
                            v-if="form.hasErrors && !form.errors.reviewer_email"
                            role="alert"
                            class="text-sm text-destructive"
                        >
                            {{ c.topicInvalid }}
                        </p>
                        <Button
                            :disabled="busy || form.track_ids.length === 0"
                            class="h-auto min-h-10 max-w-full justify-self-start whitespace-normal"
                            ><PhSpinnerGap
                                v-if="form.processing"
                                class="size-4 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            /><PhPlus
                                v-else
                                class="size-4"
                                aria-hidden="true"
                            />{{
                                form.processing ? c.loading : c.grant
                            }}</Button
                        >
                    </form>
                </CardContent></Card
            >
            <Card class="min-w-0"
                ><CardHeader
                    ><CardTitle>{{ c.reviewer }}</CardTitle></CardHeader
                ><CardContent class="min-w-0 space-y-4">
                    <p
                        v-if="!eligibilities.length"
                        class="text-sm text-muted-foreground"
                    >
                        {{ c.empty }}
                    </p>
                    <ul v-else class="grid min-w-0 gap-3 lg:grid-cols-2">
                        <li
                            v-for="row in eligibilities"
                            :key="row.id"
                            class="grid min-w-0 gap-3 rounded-lg border p-4"
                        >
                            <h3 class="text-sm font-semibold">
                                <bdi>{{ row.trackCode }}</bdi> ·
                                {{ topicName(row.trackName) }}
                            </h3>
                            <p class="text-sm">
                                <bdi>{{ row.reviewer }}</bdi> ·
                                <bdi>{{ row.email }}</bdi>
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <Badge variant="secondary">{{
                                    row.active ? c.active : c.inactive
                                }}</Badge
                                ><Badge
                                    v-if="!row.verified"
                                    variant="destructive"
                                    >{{ c.unverified }}</Badge
                                ><Badge
                                    v-if="!row.trackActive"
                                    variant="outline"
                                    >{{ c.topicInvalid }}</Badge
                                >
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                class="h-auto min-h-10 max-w-full justify-self-start whitespace-normal"
                                :disabled="
                                    busy ||
                                    (!row.active &&
                                        (!row.verified || !row.trackActive))
                                "
                                @click="confirm(row)"
                                ><PhProhibit
                                    v-if="row.active"
                                    class="size-4"
                                    aria-hidden="true"
                                /><PhArrowCounterClockwise
                                    v-else
                                    class="size-4"
                                    aria-hidden="true"
                                />{{
                                    row.active ? c.disable : c.reactivate
                                }}</Button
                            >
                        </li>
                    </ul>
                    <DataTablePagination
                        :pagination="pagination"
                    /> </CardContent
            ></Card>
        </template>
        <Dialog
            :open="pending !== null || confirmGrant"
            @update:open="
                (open) => {
                    if (!open) closeConfirmation();
                }
            "
            ><DialogContent :aria-busy="busy">
                <DialogHeader
                    ><DialogTitle>{{
                        confirmGrant
                            ? c.grant
                            : pending?.active
                              ? c.confirmDisable
                              : c.confirmReactivate
                    }}</DialogTitle
                    ><DialogDescription>{{
                        c.confirmDescription
                    }}</DialogDescription></DialogHeader
                >
                <p class="text-sm [overflow-wrap:anywhere]">
                    <bdi>{{
                        confirmGrant ? form.reviewer_email : pending?.email
                    }}</bdi>
                    ·
                    <bdi>{{
                        confirmGrant ? selectedCodes : pending?.trackCode
                    }}</bdi>
                </p>
                <p
                    v-if="change.hasErrors || (confirmGrant && form.hasErrors)"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{ c.invalid }}
                </p>
                <DialogFooter
                    ><Button
                        type="button"
                        variant="outline"
                        :disabled="busy"
                        @click="closeConfirmation"
                        >{{ c.cancel }}</Button
                    ><Button
                        type="button"
                        :disabled="busy"
                        class="h-auto min-h-10 whitespace-normal"
                        @click="confirmGrant ? grant() : applyChange()"
                        ><PhSpinnerGap
                            v-if="busy"
                            class="size-4 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{
                            busy
                                ? c.loading
                                : confirmGrant
                                  ? c.grant
                                  : pending?.active
                                    ? c.disable
                                    : c.reactivate
                        }}</Button
                    ></DialogFooter
                >
            </DialogContent></Dialog
        >
    </div>
</template>
