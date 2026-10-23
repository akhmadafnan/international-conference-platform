<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { PhSpinnerGap } from '@phosphor-icons/vue';
import AcademicPacket from '@/components/academic/AcademicPacket.vue';
import AcademicFeedback from '@/components/academic/AcademicFeedback.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
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
import type { AcademicPacket as Packet } from '@/types/academic';
const props = defineProps<{
    assignment: {
        id: string;
        status: string;
        version: number;
        sameAffiliation: boolean;
    };
    packet: Packet;
    review: {
        status: string;
        recommendation: string | null;
        author_comments: string | null;
        confidential_comments: string | null;
    } | null;
    saved: boolean;
}>();
const { t } = useI18n();
const response = useForm({
    response: props.assignment.status === 'ACCEPTED' ? 'COI' : 'ACCEPTED',
    note: '',
});
const report = useForm({
    final: false,
    recommendation: props.review?.recommendation ?? 'ACCEPT',
    author_comments: props.review?.author_comments ?? '',
    confidential_comments: props.review?.confidential_comments ?? '',
});
const confirming = ref(false);
function save(final: boolean) {
    report.final = final;
    report.put(`/reviews/${props.assignment.id}`, {
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
    <Head :title="packet.paperCode" />
    <div class="grid w-full min-w-0 gap-6 p-4 [overflow-wrap:anywhere] md:p-8">
        <header class="min-w-0 [overflow-wrap:anywhere]">
            <Link
                href="/reviews"
                class="admin-text-link text-sm text-muted-foreground underline-offset-4 hover:text-foreground focus-visible:rounded-sm focus-visible:outline-ring"
                >{{ t('academic.myReviews') }}</Link
            >
            <h1 class="mt-2 text-2xl font-semibold tracking-tight">
                <bdi>{{ packet.paperCode }}</bdi>
            </h1>
            <p class="mt-2 text-sm">
                {{ t('academic.version') }} {{ assignment.version }} —
                <Badge
                    variant="secondary"
                    class="max-w-full text-start whitespace-normal"
                    :class="statusTone(assignment.status)"
                    >{{ t('academic.states.' + assignment.status) }}</Badge
                >
            </p>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('academic.singleAnonymous') }}
            </p>
        </header>
        <AcademicFeedback :saved="saved" /><AcademicPacket :packet="packet" />
        <Card
            class="min-w-0"
            v-if="
                assignment.status === 'INVITED' ||
                assignment.status === 'ACCEPTED'
            "
            ><CardHeader
                ><CardTitle>{{
                    t('academic.invitation')
                }}</CardTitle></CardHeader
            ><CardContent
                ><p v-if="assignment.sameAffiliation" class="mb-3 text-sm">
                    {{ t('academic.sameAffiliation') }}
                </p>
                <form
                    :aria-busy="response.processing"
                    class="grid w-full min-w-0 gap-4"
                    @submit.prevent="
                        response.post(`/reviews/${assignment.id}/respond`)
                    "
                >
                    <fieldset
                        :disabled="response.processing"
                        class="grid min-w-0 gap-4 disabled:opacity-60 sm:grid-cols-2 [&>button]:sm:col-span-2"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="response">{{
                                t('academic.response')
                            }}</Label
                            ><Select v-model="response.response"
                                ><SelectTrigger id="response" class="w-full"
                                    ><SelectValue /></SelectTrigger
                                ><SelectContent
                                    ><SelectItem
                                        v-if="assignment.status === 'INVITED'"
                                        value="ACCEPTED"
                                        >{{
                                            t('academic.acceptInvitation')
                                        }}</SelectItem
                                    ><SelectItem
                                        v-if="assignment.status === 'INVITED'"
                                        value="DECLINED"
                                        >{{
                                            t('academic.declineInvitation')
                                        }}</SelectItem
                                    ><SelectItem value="COI">{{
                                        t('academic.declareCoi')
                                    }}</SelectItem></SelectContent
                                ></Select
                            >
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="response-note">{{
                                t('academic.reason')
                            }}</Label
                            ><Textarea
                                id="response-note"
                                v-model="response.note"
                                :required="response.response !== 'ACCEPTED'"
                                maxlength="8000"
                            />
                        </div>
                        <div class="min-w-0 sm:col-span-2">
                            <AcademicFeedback :errors="response.errors" />
                        </div>
                        <Button
                            class="h-auto min-h-10 max-w-full min-w-0 justify-self-start text-start whitespace-normal"
                            :disabled="response.processing"
                            ><PhSpinnerGap
                                v-if="response.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            />{{ t('academic.sendResponse') }}</Button
                        >
                    </fieldset>
                </form></CardContent
            ></Card
        >
        <Card
            class="min-w-0"
            v-if="
                assignment.status === 'ACCEPTED' ||
                assignment.status === 'COMPLETED'
            "
            ><CardHeader
                ><CardTitle>{{ t('academic.review') }}</CardTitle>
                <Badge
                    v-if="review"
                    :class="statusTone(review.status)"
                    variant="secondary"
                    class="max-w-full text-start whitespace-normal"
                    >{{ t('academic.states.' + review.status) }}</Badge
                >
                <p class="text-sm text-muted-foreground">
                    {{ t('academic.reviewHelp') }}
                </p></CardHeader
            ><CardContent
                ><form
                    :aria-busy="report.processing"
                    class="grid w-full min-w-0 gap-4"
                    @submit.prevent="save(false)"
                >
                    <fieldset
                        :disabled="
                            assignment.status === 'COMPLETED' ||
                            report.processing
                        "
                        class="grid w-full min-w-0 gap-4 disabled:opacity-60 sm:grid-cols-2 [&>button]:sm:col-span-2"
                    >
                        <div class="grid min-w-0 gap-2">
                            <Label for="recommendation">{{
                                t('academic.recommendation')
                            }}</Label
                            ><Select v-model="report.recommendation"
                                ><SelectTrigger
                                    id="recommendation"
                                    class="w-full"
                                    ><SelectValue /></SelectTrigger
                                ><SelectContent
                                    ><SelectItem
                                        v-for="value in [
                                            'ACCEPT',
                                            'REVISION',
                                            'REJECT',
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
                            <Label for="author-comments">{{
                                t('academic.authorComments')
                            }}</Label
                            ><Textarea
                                id="author-comments"
                                v-model="report.author_comments"
                                maxlength="32000"
                                class="min-h-40"
                            />
                        </div>
                        <div class="grid min-w-0 gap-2 sm:col-span-2">
                            <Label for="confidential-comments">{{
                                t('academic.confidential')
                            }}</Label
                            ><Textarea
                                id="confidential-comments"
                                v-model="report.confidential_comments"
                                maxlength="32000"
                                class="min-h-32"
                            />
                        </div>
                    </fieldset>
                    <AcademicFeedback :errors="report.errors" />
                    <div
                        v-if="assignment.status === 'ACCEPTED'"
                        class="flex min-w-0 flex-wrap gap-2"
                    >
                        <Button
                            class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                            type="submit"
                            variant="outline"
                            :disabled="report.processing"
                            ><PhSpinnerGap
                                v-if="report.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            />{{ t('academic.saveDraft') }}</Button
                        ><Button
                            class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                            type="button"
                            :disabled="
                                report.processing ||
                                !report.author_comments.trim()
                            "
                            @click="confirming = true"
                            ><PhSpinnerGap
                                v-if="report.processing"
                                class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                                aria-hidden="true"
                            />{{ t('academic.submitReview') }}</Button
                        >
                    </div>
                </form></CardContent
            ></Card
        >
        <Dialog v-model:open="confirming"
            ><DialogContent
                ><DialogHeader
                    ><DialogTitle>{{ t('academic.submitReview') }}</DialogTitle
                    ><DialogDescription>{{
                        t('academic.reviewConfirm')
                    }}</DialogDescription></DialogHeader
                ><AcademicFeedback :errors="report.errors" /><DialogFooter
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        variant="outline"
                        :disabled="report.processing"
                        @click="confirming = false"
                        ><PhSpinnerGap
                            v-if="report.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.cancel') }}</Button
                    ><Button
                        class="h-auto min-h-10 max-w-full min-w-0 text-start whitespace-normal"
                        :disabled="report.processing"
                        @click="save(true)"
                        ><PhSpinnerGap
                            v-if="report.processing"
                            class="size-4 shrink-0 animate-spin motion-reduce:animate-none"
                            aria-hidden="true"
                        />{{ t('academic.confirm') }}</Button
                    ></DialogFooter
                ></DialogContent
            ></Dialog
        >
    </div>
</template>
