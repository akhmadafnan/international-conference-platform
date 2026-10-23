<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { PhSpinnerGap } from '@phosphor-icons/vue';
import { PhDownloadSimple } from '@phosphor-icons/vue';
import AcademicPacket from '@/components/academic/AcademicPacket.vue';
import AcademicFeedback from '@/components/academic/AcademicFeedback.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import KeywordInput from '@/components/submissions/KeywordInput.vue';
import AcademicAbstractEditor from '@/components/submissions/AcademicAbstractEditor.vue';
import { keywordTags } from '@/lib/submissionInputs';
import type { AcademicPaper } from '@/types/academic';
const props = defineProps<{
    paper: AcademicPaper;
    decisions: {
        abstract_version: number;
        outcome: string;
        author_message: string;
    }[];
    screening: { outcome: string; note: string } | null;
    reports: { version: number; comments: string }[];
    canRevise: boolean;
    loaUrl: string | null;
    saved: boolean;
}>();
const { t } = useI18n();
const form = useForm({
    version: props.paper.version,
    title: props.paper.packet.title,
    abstract: props.paper.packet.abstract,
    keywords: props.paper.packet.keywords,
    response: '',
});
const keywords = ref(props.paper.packet.keywords.join('\n'));
const chips = ref<InstanceType<typeof KeywordInput>>();
const confirming = ref(false);
function revise() {
    chips.value?.commit();
    form.keywords = keywordTags(keywords.value);
    form.post(`/dashboard/submissions/${props.paper.id}/academic/revision`, {
        onSuccess: () => {
            confirming.value = false;
        },
    });
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
    <Head :title="paper.paperCode" />
    <div class="grid w-full min-w-0 gap-6 p-4 [overflow-wrap:anywhere] md:p-8">
        <header class="min-w-0 [overflow-wrap:anywhere]">
            <Link
                href="/dashboard/submissions"
                class="admin-text-link text-sm text-muted-foreground underline-offset-4 hover:text-foreground focus-visible:rounded-sm focus-visible:outline-ring"
                >{{ t('submissionWizard.navigation') }}</Link
            >
            <h1 class="mt-2 text-2xl font-semibold tracking-tight">
                <bdi>{{ paper.paperCode }}</bdi>
            </h1>
            <p class="mt-2 text-sm">
                <Badge
                    variant="secondary"
                    class="max-w-full text-start whitespace-normal"
                    :class="statusTone(paper.status)"
                    >{{ t('academic.states.' + paper.status) }}</Badge
                >
                <span aria-hidden="true">·</span> {{ t('academic.version') }}
                {{ paper.version }}
            </p>
        </header>
        <AcademicFeedback :saved="saved" />
        <Card class="min-w-0"
            ><CardContent
                class="min-w-0 space-y-4 divide-y divide-border [&>article]:pt-4 [&>div]:pt-4"
                ><p class="text-sm text-muted-foreground">
                    {{ t('academic.authorIntro') }}
                </p>
                <div v-if="screening">
                    <h2 class="font-semibold">
                        {{ t('academic.screening') }} —
                        <Badge
                            variant="secondary"
                            class="max-w-full text-start whitespace-normal"
                            :class="statusTone(screening.outcome)"
                            >{{
                                t('academic.states.' + screening.outcome)
                            }}</Badge
                        >
                    </h2>
                    <p
                        class="mt-2 text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ screening.note }}
                    </p>
                </div>
                <article v-for="(decision, i) in decisions" :key="i">
                    <h2 class="font-semibold">
                        {{ t('academic.version') }}
                        {{ decision.abstract_version }} —
                        <Badge
                            variant="secondary"
                            class="max-w-full text-start whitespace-normal"
                            :class="statusTone(decision.outcome)"
                            >{{
                                t('academic.states.' + decision.outcome)
                            }}</Badge
                        >
                    </h2>
                    <p
                        class="mt-2 text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ decision.author_message }}
                    </p>
                </article>
                <article v-for="(report, i) in reports" :key="i">
                    <h3 class="font-medium">
                        {{ t('academic.authorComments') }} —
                        {{ t('academic.version') }} {{ report.version }}
                    </h3>
                    <p
                        class="mt-2 text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ report.comments }}
                    </p>
                </article>
                <div v-if="loaUrl" class="min-w-0 space-y-3">
                    <p class="text-sm">{{ t('academic.loaHelp') }}</p>
                    <Button
                        as-child
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        ><a :href="loaUrl"
                            ><PhDownloadSimple
                                class="size-4"
                                aria-hidden="true"
                            />{{ t('academic.downloadLoa') }}</a
                        ></Button
                    >
                </div></CardContent
            ></Card
        >
        <AcademicPacket :packet="paper.packet" />
        <Card class="min-w-0" v-if="canRevise"
            ><CardHeader
                ><CardTitle>{{
                    paper.status === 'REVISION_REQUIRED'
                        ? t('academic.revision')
                        : t('academic.correction')
                }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ t('academic.revisionHelp') }}
                </p></CardHeader
            ><CardContent
                ><form
                    :aria-busy="form.processing"
                    class="grid w-full min-w-0 gap-4"
                    @submit.prevent="confirming = true"
                >
                    <fieldset
                        :disabled="form.processing"
                        class="grid min-w-0 gap-4 disabled:opacity-60 sm:grid-cols-2 [&>button]:sm:col-span-2"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="revised-title">{{
                                t('academic.abstractTitle')
                            }}</Label
                            ><Input
                                id="revised-title"
                                v-model="form.title"
                                required
                                maxlength="500"
                            />
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="revised-abstract">{{
                                t('academic.abstract')
                            }}</Label
                            ><AcademicAbstractEditor
                                id="revised-abstract"
                                v-model="form.abstract"
                                :disabled="form.processing"
                                :dir="
                                    paper.packet.locale === 'ar' ? 'rtl' : 'ltr'
                                "
                            />
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="revised-keywords">{{
                                t('academic.keywords')
                            }}</Label
                            ><KeywordInput
                                ref="chips"
                                id="revised-keywords"
                                v-model="keywords"
                                :min="null"
                                :max="null"
                                :disabled="form.processing"
                            />
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="author-response">{{
                                t('academic.responseToComments')
                            }}</Label
                            ><Textarea
                                id="author-response"
                                v-model="form.response"
                                required
                                maxlength="16000"
                            />
                        </div>
                        <div class="min-w-0 sm:col-span-2">
                            <AcademicFeedback :errors="form.errors" />
                        </div>
                        <Button
                            class="h-auto min-h-10 max-w-full min-w-0 justify-self-start text-start whitespace-normal"
                            :disabled="form.processing"
                            ><PhSpinnerGap
                                v-if="form.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            />{{ t('academic.submitRevision') }}</Button
                        >
                    </fieldset>
                </form></CardContent
            ></Card
        >
        <Dialog v-model:open="confirming"
            ><DialogContent
                ><DialogHeader
                    ><DialogTitle>{{
                        t('academic.submitRevision')
                    }}</DialogTitle
                    ><DialogDescription>{{
                        t('academic.revisionConfirm')
                    }}</DialogDescription></DialogHeader
                ><AcademicFeedback :errors="form.errors" /><DialogFooter
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        variant="outline"
                        :disabled="form.processing"
                        @click="confirming = false"
                        ><PhSpinnerGap
                            v-if="form.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.cancel') }}</Button
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        :disabled="form.processing"
                        @click="revise"
                        ><PhSpinnerGap
                            v-if="form.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.confirm') }}</Button
                    ></DialogFooter
                ></DialogContent
            ></Dialog
        >
    </div>
</template>
