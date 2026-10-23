<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { PhSpinnerGap } from '@phosphor-icons/vue';
import { PhGavel, PhUserPlus } from '@phosphor-icons/vue';
import AcademicPacket from '@/components/academic/AcademicPacket.vue';
import TopicRoutingEditor from '@/components/academic/TopicRoutingEditor.vue';
import AcademicFeedback from '@/components/academic/AcademicFeedback.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
import type { AcademicPaper, AcademicTopicRouting } from '@/types/academic';
const props = defineProps<{
    paper: AcademicPaper;
    routing: AcademicTopicRouting;
    saved: boolean;
    reviewerCount: number;
    abilities: { screen: boolean; assign: boolean; decide: boolean };
    screenings: {
        id: string;
        abstract_version: number;
        outcome: string;
        note: string;
    }[];
    assignments: {
        id: string;
        reviewer: string;
        version: number;
        status: string;
        sameAffiliation: boolean;
        screeningNote: string;
        responseNote: string | null;
        review: {
            recommendation: string;
            author_comments: string;
            confidential_comments: string;
        } | null;
    }[];
    decisions: {
        id: string;
        abstract_version: number;
        outcome: string;
        author_message: string;
        rationale: string;
    }[];
}>();
const { t } = useI18n();
const base = `/academic/submissions/${props.paper.id}`;
const screen = useForm({
    version: props.paper.version,
    outcome: 'PASS',
    note: '',
});
const assign = useForm({
    version: props.paper.version,
    reviewer_email: '',
    coi_screened: false,
    same_affiliation_acknowledged: false,
    screening_note: '',
});
const decision = useForm({
    version: props.paper.version,
    outcome: 'ACCEPTED',
    author_message: '',
    rationale: '',
});
const cancel = useForm({ reason: '' });
const cancelled = ref<string | null>(null);
const confirming = ref(false);
const open = computed(() =>
    ['SUBMITTED', 'UNDER_REVIEW'].includes(props.paper.status),
);
function submitDecision() {
    decision.post(base + '/decide', {
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
                href="/academic"
                class="admin-text-link text-sm text-muted-foreground underline-offset-4 hover:text-foreground focus-visible:rounded-sm focus-visible:outline-ring"
                >{{ t('academic.title') }}</Link
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
        <AcademicPacket :packet="paper.packet" />
        <TopicRoutingEditor :submission-id="paper.id" :routing="routing" />
        <Card class="min-w-0"
            ><CardHeader
                ><CardTitle>{{
                    t('academic.screening')
                }}</CardTitle></CardHeader
            ><CardContent class="min-w-0 space-y-4"
                ><p
                    v-if="!screenings.length"
                    class="text-sm text-muted-foreground"
                >
                    {{ t('academic.noScreening') }}
                </p>
                <ol class="min-w-0 space-y-3">
                    <li v-for="item in screenings" :key="item.id">
                        <p class="font-medium">
                            {{ t('academic.version') }}
                            {{ item.abstract_version }} —
                            <Badge
                                variant="secondary"
                                class="max-w-full text-start whitespace-normal"
                                :class="statusTone(item.outcome)"
                                >{{
                                    t('academic.states.' + item.outcome)
                                }}</Badge
                            >
                        </p>
                        <p
                            class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                        >
                            {{ item.note }}
                        </p>
                    </li>
                </ol>
                <form
                    :aria-busy="screen.processing"
                    v-if="abilities.screen && paper.status === 'SUBMITTED'"
                    class="grid w-full min-w-0 gap-4"
                    @submit.prevent="screen.post(base + '/screen')"
                >
                    <fieldset
                        :disabled="screen.processing"
                        class="grid min-w-0 gap-4 disabled:opacity-60 sm:grid-cols-2 [&>button]:sm:col-span-2"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="screen-result">{{
                                t('academic.outcome')
                            }}</Label
                            ><Select v-model="screen.outcome"
                                ><SelectTrigger
                                    id="screen-result"
                                    class="w-full"
                                    ><SelectValue /></SelectTrigger
                                ><SelectContent
                                    ><SelectItem
                                        v-for="value in [
                                            'PASS',
                                            'CORRECTION_REQUIRED',
                                            'INELIGIBLE',
                                        ]"
                                        :key="value"
                                        :value="value"
                                        >{{
                                            t('academic.states.' + value)
                                        }}</SelectItem
                                    ></SelectContent
                                ></Select
                            >
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="screen-note">{{
                                t('academic.authorNote')
                            }}</Label
                            ><Textarea
                                id="screen-note"
                                v-model="screen.note"
                                required
                                maxlength="8000"
                            />
                        </div>
                        <div class="min-w-0 sm:col-span-2">
                            <AcademicFeedback :errors="screen.errors" />
                        </div>
                        <Button
                            class="h-auto min-h-10 max-w-full min-w-0 justify-self-start text-start whitespace-normal"
                            :disabled="screen.processing"
                            ><PhSpinnerGap
                                v-if="screen.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            />{{ t('academic.saveScreening') }}</Button
                        >
                    </fieldset>
                </form>
            </CardContent></Card
        >
        <Card class="min-w-0" v-if="abilities.assign || abilities.decide"
            ><CardHeader
                ><CardTitle>{{ t('academic.assignments') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ t('academic.reviewPolicy', { count: reviewerCount }) }}
                </p></CardHeader
            ><CardContent class="min-w-0 space-y-5"
                ><p
                    v-if="!assignments.length"
                    class="text-sm text-muted-foreground"
                >
                    {{ t('academic.noAssignments') }}
                </p>
                <article
                    v-for="item in assignments"
                    :key="item.id"
                    class="min-w-0 space-y-3 rounded-lg border border-border bg-muted/20 p-4 [overflow-wrap:anywhere]"
                >
                    <h3 class="font-semibold">
                        <bdi>{{ item.reviewer }}</bdi>
                    </h3>
                    <p class="text-sm">
                        {{ t('academic.version') }} {{ item.version }} —
                        <Badge
                            variant="secondary"
                            class="max-w-full text-start whitespace-normal"
                            :class="statusTone(item.status)"
                            >{{ t('academic.states.' + item.status) }}</Badge
                        >
                    </p>
                    <p
                        class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ item.screeningNote }}
                    </p>
                    <p v-if="item.sameAffiliation" class="text-sm">
                        {{ t('academic.sameAffiliation') }}
                    </p>
                    <p
                        v-if="item.responseNote"
                        class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ item.responseNote }}
                    </p>
                    <div
                        v-if="item.review"
                        class="min-w-0 space-y-3 border-t pt-3"
                    >
                        <p class="font-medium">
                            {{ t('academic.recommendation') }}:
                            {{
                                t(
                                    'academic.states.' +
                                        item.review.recommendation,
                                )
                            }}
                        </p>
                        <h4 class="text-sm font-semibold">
                            {{ t('academic.authorComments') }}
                        </h4>
                        <p
                            class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                        >
                            {{ item.review.author_comments }}
                        </p>
                        <h4 class="text-sm font-semibold">
                            {{ t('academic.confidential') }}
                        </h4>
                        <p
                            class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                        >
                            {{ item.review.confidential_comments }}
                        </p>
                    </div>
                    <Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        v-if="
                            abilities.assign &&
                            ['INVITED', 'ACCEPTED'].includes(item.status) &&
                            open
                        "
                        type="button"
                        variant="destructive"
                        size="sm"
                        @click="cancelled = item.id"
                        >{{ t('academic.cancelAssignment') }}</Button
                    >
                </article>
                <form
                    :aria-busy="assign.processing"
                    v-if="abilities.assign && open"
                    class="grid w-full min-w-0 gap-4"
                    @submit.prevent="assign.post(base + '/assign')"
                >
                    <fieldset
                        :disabled="assign.processing"
                        class="grid min-w-0 gap-4 disabled:opacity-60 sm:grid-cols-2 [&>button]:sm:col-span-2"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="reviewer-email">{{
                                t('academic.reviewerEmail')
                            }}</Label
                            ><Input
                                id="reviewer-email"
                                v-model="assign.reviewer_email"
                                type="email"
                                required
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('academic.coiHelp') }}
                        </p>
                        <div class="flex min-w-0 items-center gap-2">
                            <Checkbox
                                id="coi-screened"
                                v-model="assign.coi_screened"
                            /><Label for="coi-screened">{{
                                t('academic.coiScreened')
                            }}</Label>
                        </div>
                        <div class="flex min-w-0 items-center gap-2">
                            <Checkbox
                                id="same-affiliation"
                                v-model="assign.same_affiliation_acknowledged"
                            /><Label for="same-affiliation">{{
                                t('academic.affiliationAcknowledged')
                            }}</Label>
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="coi-note">{{
                                t('academic.screeningNote')
                            }}</Label
                            ><Textarea
                                id="coi-note"
                                v-model="assign.screening_note"
                                required
                                maxlength="8000"
                            />
                        </div>
                        <div class="min-w-0 sm:col-span-2">
                            <AcademicFeedback :errors="assign.errors" />
                        </div>
                        <Button
                            class="h-auto min-h-10 max-w-full min-w-0 justify-self-start text-start whitespace-normal"
                            :disabled="assign.processing"
                            ><PhSpinnerGap
                                v-if="assign.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            /><PhUserPlus class="size-4" aria-hidden="true" />{{
                                t('academic.invite')
                            }}</Button
                        >
                    </fieldset>
                </form>
            </CardContent></Card
        >
        <Card class="min-w-0" v-if="abilities.assign || abilities.decide"
            ><CardHeader
                ><CardTitle>{{ t('academic.decisions') }}</CardTitle>
                <p class="text-sm text-muted-foreground">
                    {{ t('academic.decisionHelp') }}
                </p></CardHeader
            ><CardContent class="min-w-0 space-y-5"
                ><article
                    v-for="item in decisions"
                    :key="item.id"
                    class="min-w-0 space-y-3 rounded-lg border border-border bg-muted/20 p-4 [overflow-wrap:anywhere]"
                >
                    <h3 class="font-semibold">
                        {{ t('academic.version') }}
                        {{ item.abstract_version }} —
                        <Badge
                            variant="secondary"
                            class="max-w-full text-start whitespace-normal"
                            :class="statusTone(item.outcome)"
                            >{{ t('academic.states.' + item.outcome) }}</Badge
                        >
                    </h3>
                    <p
                        class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ item.author_message }}
                    </p>
                    <h4 class="text-sm font-semibold">
                        {{ t('academic.rationale') }}
                    </h4>
                    <p
                        class="text-sm leading-6 [overflow-wrap:anywhere] whitespace-pre-wrap"
                    >
                        {{ item.rationale }}
                    </p>
                </article>
                <form
                    :aria-busy="decision.processing"
                    v-if="abilities.decide && paper.status === 'UNDER_REVIEW'"
                    class="grid w-full min-w-0 gap-4"
                    @submit.prevent="confirming = true"
                >
                    <fieldset
                        :disabled="decision.processing"
                        class="grid min-w-0 gap-4 disabled:opacity-60 sm:grid-cols-2 [&>button]:sm:col-span-2"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="decision">{{
                                t('academic.outcome')
                            }}</Label
                            ><Select v-model="decision.outcome"
                                ><SelectTrigger id="decision" class="w-full"
                                    ><SelectValue /></SelectTrigger
                                ><SelectContent
                                    ><SelectItem
                                        v-for="value in [
                                            'ACCEPTED',
                                            'REVISION_REQUIRED',
                                            'REJECTED',
                                        ]"
                                        :key="value"
                                        :value="value"
                                        >{{
                                            t('academic.states.' + value)
                                        }}</SelectItem
                                    ></SelectContent
                                ></Select
                            >
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="author-message">{{
                                t('academic.authorMessage')
                            }}</Label
                            ><Textarea
                                id="author-message"
                                v-model="decision.author_message"
                                required
                                maxlength="16000"
                            />
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="decision-rationale">{{
                                t('academic.rationale')
                            }}</Label
                            ><Textarea
                                id="decision-rationale"
                                v-model="decision.rationale"
                                required
                                maxlength="16000"
                            />
                        </div>
                        <div class="min-w-0 sm:col-span-2">
                            <AcademicFeedback :errors="decision.errors" />
                        </div>
                        <Button
                            class="h-auto min-h-10 max-w-full min-w-0 justify-self-start text-start whitespace-normal"
                            :disabled="decision.processing"
                            ><PhSpinnerGap
                                v-if="decision.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            /><PhGavel class="size-4" aria-hidden="true" />{{
                                t('academic.recordDecision')
                            }}</Button
                        >
                    </fieldset>
                </form>
            </CardContent></Card
        >
        <Dialog v-model:open="confirming"
            ><DialogContent
                ><DialogHeader
                    ><DialogTitle>{{
                        t('academic.recordDecision')
                    }}</DialogTitle
                    ><DialogDescription>{{
                        t('academic.decisionConfirm')
                    }}</DialogDescription></DialogHeader
                >
                <p>
                    <Badge
                        variant="secondary"
                        class="max-w-full text-start whitespace-normal"
                        :class="statusTone(decision.outcome)"
                        >{{ t('academic.states.' + decision.outcome) }}</Badge
                    >
                </p>
                <AcademicFeedback :errors="decision.errors" /><DialogFooter
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        variant="outline"
                        :disabled="decision.processing"
                        @click="confirming = false"
                        ><PhSpinnerGap
                            v-if="decision.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.cancel') }}</Button
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        :disabled="decision.processing"
                        @click="submitDecision"
                        ><PhSpinnerGap
                            v-if="decision.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.confirm') }}</Button
                    ></DialogFooter
                ></DialogContent
            ></Dialog
        >
        <Dialog
            :open="cancelled !== null"
            @update:open="
                (value) => {
                    if (!value && !cancel.processing) cancelled = null;
                }
            "
            ><DialogContent
                ><DialogHeader
                    ><DialogTitle>{{
                        t('academic.cancelAssignment')
                    }}</DialogTitle
                    ><DialogDescription>{{
                        t('academic.cancelHelp')
                    }}</DialogDescription></DialogHeader
                ><Label for="cancel-reason">{{ t('academic.reason') }}</Label
                ><Textarea
                    id="cancel-reason"
                    v-model="cancel.reason"
                /><AcademicFeedback :errors="cancel.errors" /><DialogFooter
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        variant="outline"
                        :disabled="cancel.processing"
                        @click="cancelled = null"
                        ><PhSpinnerGap
                            v-if="cancel.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.cancel') }}</Button
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        variant="destructive"
                        :disabled="cancel.processing || !cancel.reason.trim()"
                        @click="
                            cancel.post(
                                `/academic/assignments/${cancelled}/cancel`,
                                {
                                    onSuccess: () => {
                                        cancelled = null;
                                        cancel.reset();
                                    },
                                },
                            )
                        "
                        >{{ t('academic.confirm') }}</Button
                    ></DialogFooter
                ></DialogContent
            ></Dialog
        >
    </div>
</template>
