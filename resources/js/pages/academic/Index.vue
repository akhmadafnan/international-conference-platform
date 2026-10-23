<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';
import { reviewerTopicCopy } from '@/i18n/reviewerTopics';
import { PhSpinnerGap } from '@phosphor-icons/vue';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import DataTablePagination from '@/components/DataTablePagination.vue';
import AcademicFeedback from '@/components/academic/AcademicFeedback.vue';
import type { PaginationMeta } from '@/types/pagination';
const props = defineProps<{
    editions: { id: string; code: string }[];
    editionId: string | null;
    submissions: {
        id: string;
        paperCode: string;
        status: string;
        version: number;
        url: string;
    }[];
    pagination: PaginationMeta;
    reviewerTopicsUrl: string | null;
}>();
const { t, locale } = useI18n();
const topicCopy = computed(() => reviewerTopicCopy(locale.value));
const form = useForm({ edition_id: props.editionId ?? '' });
function select() {
    form.post('/academic/edition', { preserveScroll: true });
}

function statusTone(status: string) {
    if (['PASS', 'ACCEPT', 'ACCEPTED', 'COMPLETED'].includes(status))
        return 'bg-success/10 text-success';
    if (
        [
            'CORRECTION_REQUIRED',
            'REVISION_REQUIRED',
            'REVISION',
            'COI',
        ].includes(status)
    )
        return 'bg-warning/10 text-warning';
    if (
        ['INELIGIBLE', 'REJECT', 'REJECTED', 'DECLINED', 'CANCELLED'].includes(
            status,
        )
    )
        return 'bg-destructive/10 text-destructive';
    return 'bg-info/10 text-info';
}
</script>
<template>
    <Head :title="t('academic.title')" />
    <div class="grid w-full min-w-0 gap-6 p-4 md:p-8">
        <header class="min-w-0 [overflow-wrap:anywhere]">
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ t('academic.title') }}
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('academic.intro') }}
            </p>
            <Button
                v-if="reviewerTopicsUrl"
                as-child
                variant="outline"
                class="mt-3 h-auto min-h-10 max-w-full whitespace-normal"
                ><Link :href="reviewerTopicsUrl">{{
                    topicCopy.title
                }}</Link></Button
            >
        </header>
        <form
            :aria-busy="form.processing"
            class="flex flex-wrap items-end gap-3"
            @submit.prevent="select"
        >
            <div class="grid w-full min-w-0 gap-2 sm:w-72">
                <Label for="academic-edition">{{ t('academic.edition') }}</Label
                ><Select v-model="form.edition_id" :disabled="form.processing"
                    ><SelectTrigger id="academic-edition" class="w-full"
                        ><SelectValue
                            :placeholder="
                                t('academic.chooseEdition')
                            " /></SelectTrigger
                    ><SelectContent
                        ><SelectItem
                            v-for="edition in editions"
                            :key="edition.id"
                            :value="edition.id"
                            >{{ edition.code }}</SelectItem
                        ></SelectContent
                    ></Select
                >
            </div>
            <Button
                class="h-auto min-h-9 max-w-full text-start whitespace-normal"
                :disabled="form.processing || !form.edition_id"
                ><PhSpinnerGap
                    v-if="form.processing"
                    class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                    aria-hidden="true"
                />{{ t('academic.openEdition') }}</Button
            >
        </form>
        <AcademicFeedback :errors="form.errors" />
        <Card
            ><CardContent class="min-w-0"
                ><p
                    v-if="!submissions.length"
                    class="py-6 text-sm text-muted-foreground"
                >
                    {{ t('academic.empty') }}
                </p>
                <div v-else class="min-w-0 overflow-x-auto">
                    <table class="admin-data-table">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{ t('academic.paper') }}
                                </th>
                                <th scope="col">
                                    {{ t('academic.status') }}
                                </th>
                                <th scope="col">
                                    {{ t('academic.version') }}
                                </th>
                                <th scope="col">
                                    {{ t('academic.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="paper in submissions" :key="paper.id">
                                <th scope="row">
                                    <bdi>{{ paper.paperCode }}</bdi>
                                </th>
                                <td>
                                    <Badge
                                        variant="secondary"
                                        class="max-w-full text-start whitespace-normal"
                                        :class="statusTone(paper.status)"
                                        >{{
                                            t('academic.states.' + paper.status)
                                        }}</Badge
                                    >
                                </td>
                                <td>{{ paper.version }}</td>
                                <td>
                                    <Button
                                        class="h-auto min-h-9 max-w-full text-start whitespace-normal"
                                        as-child
                                        size="sm"
                                        variant="outline"
                                        ><Link :href="paper.url">{{
                                            t('academic.open')
                                        }}</Link></Button
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <DataTablePagination :pagination="pagination" /></CardContent
        ></Card>
    </div>
</template>
