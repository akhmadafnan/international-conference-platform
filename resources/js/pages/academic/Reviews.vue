<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
defineProps<{
    assignments: {
        id: string;
        paperCode: string;
        edition: string;
        version: number;
        status: string;
        url: string | null;
    }[];
}>();
const { t } = useI18n();

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
    <Head :title="t('academic.myReviews')" />
    <div class="grid w-full min-w-0 gap-6 p-4 md:p-8">
        <header class="min-w-0 [overflow-wrap:anywhere]">
            <h1 class="text-2xl font-semibold tracking-tight">
                {{ t('academic.myReviews') }}
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('academic.reviewerIntro') }}
            </p>
        </header>
        <Card
            ><CardContent
                ><p
                    v-if="!assignments.length"
                    class="py-6 text-sm text-muted-foreground"
                >
                    {{ t('academic.noAssignments') }}
                </p>
                <ul v-else class="divide-y divide-border">
                    <li
                        v-for="assignment in assignments"
                        :key="assignment.id"
                        class="flex flex-wrap items-center justify-between gap-3 py-4"
                    >
                        <div class="min-w-0 space-y-2 [overflow-wrap:anywhere]">
                            <h2 class="font-semibold">
                                <bdi>{{ assignment.paperCode }}</bdi>
                            </h2>
                            <p class="text-sm text-muted-foreground">
                                <bdi>{{ assignment.edition }}</bdi>
                                <span aria-hidden="true">·</span>
                                {{ t('academic.version') }}
                                {{ assignment.version }} —
                                <Badge
                                    variant="secondary"
                                    class="max-w-full text-start whitespace-normal"
                                    :class="statusTone(assignment.status)"
                                    >{{
                                        t(
                                            'academic.states.' +
                                                assignment.status,
                                        )
                                    }}</Badge
                                >
                            </p>
                        </div>
                        <Button
                            class="h-auto min-h-9 max-w-full text-start whitespace-normal"
                            v-if="assignment.url"
                            as-child
                            variant="outline"
                            ><Link :href="assignment.url">{{
                                t('academic.open')
                            }}</Link></Button
                        >
                    </li>
                </ul></CardContent
            ></Card
        >
    </div>
</template>
